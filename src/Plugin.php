<?php
namespace enovate\rollbar;

use Craft;
use craft\events\ExceptionEvent;
use craft\web\ErrorHandler;
use yii\base\Event;

class Plugin extends \craft\base\Plugin
{
    public function init()
    {
        Event::on(ErrorHandler::class, ErrorHandler::EVENT_BEFORE_HANDLE_EXCEPTION, function(ExceptionEvent $event)
        {
            Craft::dd($event);
        });

        parent::init();
    }

    /**
     * @return models\Settings
     */
    public function createSettingsModel()
    {
        return new models\Settings();
    }
}