<?php
namespace enovate\rollbar;

use enovate\rollbar\services\Rollbar as RollbarService;
use Craft;
use craft\events\ExceptionEvent;
use craft\events\RegisterUrlRulesEvent;
use craft\web\ErrorHandler;
use craft\web\UrlManager;
use yii\base\Event;
use Rollbar\Payload\Level;

class Plugin extends \craft\base\Plugin
{
    public $hasCpSettings = true;

    /**
     *
     */
    public function init()
    {
        $this->setComponents([
            'rollbar' => RollbarService::class,
        ]);

        Event::on(ErrorHandler::class, ErrorHandler::EVENT_BEFORE_HANDLE_EXCEPTION, function(ExceptionEvent $event)
        {
            $this->rollbar->log(Level::ERROR, $event->exception->getMessage());
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

    public function settingsHtml()
    {
        $configFile = Craft::$app->getConfig()->getConfigFromFile('rollbar');
        $settings   = $this->getSettings();
        $settings->validate();

        return Craft::$app->getView()->renderTemplate('rollbar/settings', [
            'settings'   => $settings,
            'configFile' => $configFile,
        ]);
    }
}