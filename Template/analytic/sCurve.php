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
  <h2><?= t('Full project timeline') ?></h2>
  <?= $this->app->component('chart-s-curve', array(
    'id' => 'main',
    'payload' => $payload['scurve']
    )) ?>
<?php endif ?>

<?php if (!empty($payload['categories'])): ?>
  <?php foreach ($payload['categories'] as $idx => $category): ?>
    <h2><?= $category['name'] ?></h2>
    <?= $this->app->component('chart-s-curve', array(
      'id' => $idx,
      'payload' => $category['tasks1']
      )) ?>
  <?php endforeach; ?>
<?php endif ?>

<pre>
<?php // print_r($payload) ?>
</pre>