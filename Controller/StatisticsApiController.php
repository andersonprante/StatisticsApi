<?php

namespace Kanboard\Plugin\StatisticsApi\Controller;

use Kanboard\Controller\BaseController;
use Kanboard\Model\TaskModel;

class StatisticsApiController extends BaseController
{

  function cmp($a, $b)
  {
    return $a['date_due'] > $b['date_due'];
  }

  public function getCurveS()
  {
    $metrics = [];
    $payload = [
      'scurve' => [],
      'info' => [],
      'tasks_status' => [
        ['Abertas', 0],
        ['Atrasadas', 0],
        ['Concluídas', 0],
      ],
      'overall' => [
        ['Desvio', 0]
      ]
    ];
    $project = $this->getProject();
    $without_date_due = 0;
    $without_score = 0;

    $tasks = $this->db->table(TaskModel::TABLE)
      ->eq(TaskModel::TABLE . '.project_id', $project['id'])
      ->findAll();

    $categories_in_project = $this->categoryModel->getAll($project['id']);

    foreach ($tasks as $task => $value) {
      if ($value['date_due'] === '0' || !$value['date_due']) {
        $without_date_due += 1;
        $tasks[$task]['date_due'] = strval(time());
      }
    }

    if (!empty($tasks)) {

      usort($tasks, array($this, 'cmp'));
  
      $first_date = new \DateTime(date('Y-m-d', $tasks[0]['date_due']));
      $last_date = new \DateTime(date('Y-m-d', end($tasks)['date_due']));
      $last_date = $last_date->modify('+1 day');
      $period = new \DatePeriod($first_date, new \DateInterval('P1D'), $last_date);
      $project_has_categories = count($categories_in_project);
      foreach ($period as $key => $value) {
        $day = $value->format('Y-m-d');
        $metrics[$day] = [
          $day,
          0,
          0
        ];

        if ($project_has_categories) {
          foreach ($categories_in_project as $key => $category) {
            $categories_in_project[$key]['tasks'][$day] = [
              $day,
              0,
              0
            ];
          }
        }

      }

      foreach ($tasks as $task => $value) {
        if ($value['score'] == 0) {
          $without_score += 1;
        }
        $date_due = date('Y-m-d', $value['date_due']);
        $metrics[$date_due][1] += $value['score'];
        $date_completed = $value['date_completed'] ? date('Y-m-d', $value['date_completed']) : null;
        if ($date_completed) {
          $metrics[$date_completed][2] += $value['score'];
        }

        $category_id = $value['category_id'];
        if ($project_has_categories && $category_id) {
          $idx = array_search($category_id, array_column($categories_in_project, 'id'));
          $categories_in_project[$idx]['tasks'][$date_due][1] += $value['score'];
          $date_completed = $value['date_completed'] ? date('Y-m-d', $value['date_completed']) : null;
          if ($date_completed) {
            $categories_in_project[$idx]['tasks'][$date_due][2] += $value['score'];
          }
        }

        if ($value['date_completed'] == null || $value['date_completed'] == '0') { // aberto
          if ($value['date_due'] <= time()) { // atrasada
            $payload['tasks_status'][1][1] += 1;
          } else { // regular
            $payload['tasks_status'][0][1] += 1;
          }
        } else {
          $payload['tasks_status'][2][1] += 1; // concluída
        }

      }
      
      $payload['scurve'][] = ["Date", "Planned", "Realized"];
      $acc_planned = 0;
      $acc_realized = 0;
      $today = date('Y-m-d');
      $flagDay = true;
      $flagDay_today = $flagDay;
      foreach ($metrics as $key => $value) {
        $acc_planned += $value[1];
        $acc_realized += $value[2];
        $value[1] = $acc_planned;
        $value[2] = $flagDay ? $acc_realized : null;
        $flagDay = $key < $today;
        $payload['scurve'][] = $value;

        if ($flagDay != $flagDay_today) {
          $flagDay_today = $flagDay;
          $payload['overall'][0][1] = round($acc_realized / $acc_planned - 1, 2);
        }
      }
      
      $payload['info'] = [];
      if ($without_score)
        $payload['info'][] = $without_score . t(" tasks without complexity, these aren't showed in the chart.");
      if ($without_date_due)
        $payload['info'][] = $without_date_due . t(' tasks without due date defined, please fix it. When this occur the date due dynamically assigned is for today.');
      
      if ($project_has_categories) {
        $payload['categories'] = $categories_in_project;
        foreach ($payload['categories'] as $idx_category => $value_category) {
          $acc_planned = 0;
          $acc_realized = 0;
          $flagDay = true;
          $payload['categories'][$idx_category]['tasks_status'] = [
            ['Abertas', 0],
            ['Atrasadas', 0],
            ['Concluídas', 0],
          ];
          foreach ($value_category['tasks'] as $idx_task => $value) {
            $acc_planned += $payload['categories'][$idx_category]['tasks'][$idx_task][1];
            $acc_realized += $payload['categories'][$idx_category]['tasks'][$idx_task][2];
            $value[1] = $acc_planned;
            $value[2] = $flagDay ? $acc_realized : null;
            $payload['categories'][$idx_category]['tasks1'][] = $value;
            $flagDay = $value[0] < $today;
          }
          array_unshift($payload['categories'][$idx_category]['tasks1'], ['Date', 'Planned', 'Realized']);
        }
      }

    }

    $this->response->html(
      $this->helper->layout->analytic('StatisticsApi:analytic/sCurve',
          array(
              'project'   => $project,
              'payload'   => $payload,
              'title'     => t('s-Curve'),
          )
      )
    );
  }
}
