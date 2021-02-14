<?php if (! $is_ajax): ?>
    <div class="page-header">
        <h2><?= t('s-Curve') ?></h2>
    </div>
<?php endif ?>

<?php if (count($payload['scurve']) <= 2): ?>
  <p class="alert"><?= t('Not enough data to show the graph.') ?></p>
  <?php return ?>
<?php endif ?>

<?php if(count($payload['info']) > 0): ?>
  <div class="alert">
    <ul>
      <?php foreach ($payload['info'] as $info): ?>
        <li><?= $info ?></li>
      <?php endforeach ?>
    </ul>
  </div>
<?php endif ?>

<?php if (empty($payload)): ?>
  <p class="alert"><?= t('Not enough data to show the graph.') ?></p>
<?php else: ?>
  <?= $this->app->component('chart-s-curve', array(
      'payload' => $payload['scurve']
  )) ?>
<?php endif ?>