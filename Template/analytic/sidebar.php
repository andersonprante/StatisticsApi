
<li <?= $this->app->checkMenuSelection('StatisticsApiController', 'getSCurve') ?>>
    <?= $this->modal->replaceLink(
            t('s-Curve'),
            'StatisticsApiController',
            'getCurveS',
            array(
                'plugin' => 'StatisticsApi',
                'project_id' => $project['id']
            )
        )
    ?>
</li>