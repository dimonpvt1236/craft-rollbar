<?php
namespace enovate\rollbar\variables;

use Craft;
use enovate\rollbar\Plugin;
use enovate\rollbar\services\Rollbar as RollbarService;

class Rollbar
{
    /** @var RollbarService */
    protected $_service;

    public function __construct()
    {
        /** @var Plugin $plugin */
        $plugin = Plugin::getInstance();

        $this->_service = $plugin->rollbar;
    }

    public function getJsTrackingCode()
    {
        $content = $this->_service->getJsTrackingCode();

        return $content ? new \Twig_Markup($content, Craft::$app->charset) : '';
    }

    public function setCspHeader()
    {
        $this->_service->setCspHeader();
    }
}