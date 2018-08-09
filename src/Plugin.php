<?php
namespace enovatedesign\rollbar;

use craft\events\TemplateEvent;
use enovatedesign\rollbar\services\Rollbar as RollbarService;
use enovatedesign\rollbar\variables\Rollbar as RollbarVariable;
use Craft;
use craft\base\Plugin as BasePlugin;
use craft\events\ExceptionEvent;
use craft\web\ErrorHandler;
use craft\web\twig\variables\CraftVariable;
use craft\web\View;
use yii\base\Event;
use Rollbar\Payload\Level;

/**
 * Class Rollbar
 * @package enovate\rollbar
 * @since 0.1.0
 *
 * @property RollbarService $rollbar
 */
class Plugin extends BasePlugin
{
    /**
     * @var Plugin
     */
    public static $plugin;

    public function init()
    {
        parent::init();

        self::$plugin = $this;

        $this->setComponents([
            'rollbar' => RollbarService::class,
        ]);

        Event::on(ErrorHandler::class, ErrorHandler::EVENT_BEFORE_HANDLE_EXCEPTION, function(ExceptionEvent $event)
        {
            if ($this->rollbar->shouldReport($event->exception)) {
                $this->rollbar->log(Level::ERROR, $event->exception->getMessage());
            }
        });

        Event::on(CraftVariable::class, CraftVariable::EVENT_INIT, function(Event $event)
        {
            /** @var RollbarVariable $variable */
            $variable = $event->sender;
            $variable->set('rollbar', RollbarVariable::class);
        });

        Event::on(View::class, View::EVENT_BEFORE_RENDER_TEMPLATE, function(TemplateEvent $e) {
            if (
                $e->template === 'settings/plugins/_settings' &&
                $e->variables['plugin'] === $this
            ) {
                $e->variables['tabs'] = [
                    ['label' => Craft::t('rollbar', 'General'), 'url' => '#settings-tab-general'],
                    ['label' => Craft::t('rollbar', 'Privacy'), 'url' => '#settings-tab-privacy'],
                    ['label' => Craft::t('rollbar', 'Server'), 'url' => '#settings-tab-server'],
                    ['label' => Craft::t('rollbar', 'Client'), 'url' => '#settings-tab-client'],
                ];
            }
        });
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
        $settings = $this->getSettings();

        $settings->validate();

        return Craft::$app->getView()->renderTemplate('rollbar/_settings', [
            'settings' => $settings,
            'configFile' => $configFile,
        ]);
    }
}