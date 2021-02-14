<?php

namespace Kanboard\Plugin\StatisticsApi\Controller;

use Kanboard\Controller\BaseController;
use Kanboard\Model\TaskModel;

class StatisticsApiController extends BaseController
{

  public function hello()
  {
    echo 'hello world';
  }

  function cmp($a, $b)
  {
    return $a['date_due'] > $b['date_due'];
  }

  public function getCurveS()
  {
    $metrics = [];
    $payload = [
      'scurve' => [],
      'info' => []
    ];
    $project = $this->getProject();
    $without_date_due = 0;
    $without_score = 0;

    $tasks = $this->db->table(TaskModel::TABLE)
      ->eq(TaskModel::TABLE . '.project_id', $project['id'])
      ->findAll();

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
      foreach ($period as $key => $value) {
        $metrics[$value->format('Y-m-d')] = [
          $value->format('Y-m-d'),
          0,
          0
        ];
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
      }
      
      $payload['scurve'][] = ["Date", "Planned", "Realized"];
      $acc_planned = 0;
      $acc_realized = 0;
      $today = date('Y-m-d');
      $flagDay = true;
      foreach ($metrics as $key => $value) {
        $acc_planned += $value[1];
        $acc_realized += $value[2];
        $value[1] = $acc_planned;
        $value[2] = null;
        if ($flagDay) {
          if ($key == $today) {
            $flagDay = false;
            $value[2] = $acc_realized;
          }
        }
        $payload['scurve'][] = $value;
      }
      
      $payload['info'][] = $without_score . t(" tasks without complexity, these aren't showed in the chart.");
      $payload['info'][] = $without_date_due . t(' tasks without due date defined, please fix it. When this occur the date due dynamically assigned is for today.');

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
