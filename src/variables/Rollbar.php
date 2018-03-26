<?php
namespace enovatedesign\rollbar\variables;

use Craft;
use enovatedesign\rollbar\Plugin;
use enovatedesign\rollbar\services\Rollbar as RollbarService;

class Rollbar
{
    /** @var RollbarService */
    protected $_service;

    public function __construct()
    {
        /** @var Rollbar $plugin */
        $plugin = Plugin::getInstance();

        $this->_service = $plugin->rollbar;
    }

    public function getJsTrackingCode()
    {
        $content = $this->_service->getJsTrackingCode();

        return $content ? new \Twig_Markup($content, Craft::$app->charset) : '';
    }
}