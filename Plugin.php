<?php

namespace Kanboard\Plugin\StatisticsApi;

use Kanboard\Core\Plugin\Base;
use Kanboard\Core\Translator;

class Plugin extends Base
{
    public function initialize()
    {
        $this->hook->on('template:layout:js', array('template' => 'plugins/StatisticsApi/Assets/js/components/chart-s-curve.js'));
        $this->hook->on('template:layout:js', array('template' => 'plugins/StatisticsApi/Assets/js/components/chart-s-donut.js'));
        $this->hook->on('template:layout:js', array('template' => 'plugins/StatisticsApi/Assets/js/components/chart-s-gauge.js'));
        $this->template->hook->attach('template:analytic:sidebar', 'StatisticsApi:analytic/sidebar');
    }

    public function onStartup()
    {
        Translator::load($this->languageModel->getCurrentLanguage(), __DIR__.'/Locale');
    }

    public function getPluginName()
    {
        return 'StatisticsApi';
    }

    public function getPluginDescription()
    {
        return t('My plugin is awesome');
    }

    public function getPluginAuthor()
    {
        return 'Anderson Prante';
    }

    public function getPluginVersion()
    {
        return '1.0.0';
    }

    public function getPluginHomepage()
    {
        return 'https://github.com/kanboard/plugin-myplugin';
    }
}

