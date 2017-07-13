<?php
namespace enovate\rollbar;

use Craft;
use craft\events\DefineComponentsEvent;
use craft\events\ExceptionEvent;
use craft\web\ErrorHandler;
use craft\web\twig\variables\CraftVariable;
use enovate\rollbar\services\Rollbar as RollbarService;
use enovate\rollbar\variables\Rollbar as RollbarVariable;
use Rollbar\Payload\Level;
use yii\base\Event;

class Plugin extends \craft\base\Plugin
{
    public $hasCpSettings = true;

    public function init()
    {
        $this->setComponents([
            'rollbar' => RollbarService::class,
        ]);

        Event::on(ErrorHandler::class, ErrorHandler::EVENT_BEFORE_HANDLE_EXCEPTION, function(ExceptionEvent $event)
        {
            if ($this->rollbar->shouldReport($event->exception))
            {
                $this->rollbar->log(Level::ERROR, $event->exception->getMessage());
            }
        });

        Event::on(CraftVariable::class, CraftVariable::EVENT_DEFINE_COMPONENTS, function(DefineComponentsEvent $event)
        {
            $event->components['rollbar'] = new RollbarVariable();
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

        return Craft::$app->getView()->renderTemplate('rollbar/_settings', [
            'settings'   => $settings,
            'configFile' => $configFile,
        ]);
    }
}