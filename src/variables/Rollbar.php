<?php
namespace enovate\rollbar\variables;

use Craft;
use enovate\rollbar\Plugin;

class Rollbar
{
    public function getJsTrackingCode()
    {
        $content = Plugin::getInstance()->rollbar->getJsTrackingCode();

        return $content ? new \Twig_Markup($content, Craft::$app->charset) : '';
    }
}