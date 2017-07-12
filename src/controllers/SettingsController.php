<?php
namespace enovate\rollbar\controllers;

use Craft;
use enovate\rollbar\models\RollbarClient;
use enovate\rollbar\models\Settings;
use craft\web\Controller;
use enovate\rollbar\Plugin;
use Rollbar\Payload\Level;
use Rollbar\Response;

class SettingsController extends Controller
{
    public function init()
    {
        // All setting actions require an admin
        $this->requireAdmin();
    }

    /**
     * Tests the rollbar settings.
     *
     * @return void
     */
    public function actionTest()
    {
        $this->requirePostRequest();

        // Create a Settings model populated with the post data
        $request = Craft::$app->getRequest();

        $settings = new Settings();
        $settings->accessToken       = $request->getBodyParam('settings.accessToken');
        $settings->clientAccessToken = $request->getBodyParam('settings.clientAccessToken');
        $settings->reportInDevMode   = true;

        $settingsAreValid = $settings->validate();

        if ($settingsAreValid)
        {
            $client = new RollbarClient([
                'access_token' => $settings->accessToken,
            ]);

            /** @var Response $response */
            $response = $client->log(Level::INFO, Craft::t('rollbar', 'This is a test message'));

            if ($response->wasSuccessful())
            {
                // Assume that these will also be successful
                $client->log(Level::WARNING, Craft::t('rollbar', 'This is a test warning'));
                $client->log(Level::ERROR,   Craft::t('rollbar', 'This is a test error'));

                Craft::$app->getSession()->setNotice(Craft::t('rollbar', 'Test message posted successfully'));
            }
            else
            {
                Craft::$app->getSession()->setError(Craft::t('rollbar', 'Test message was not posted successfully'));
            }
        }
        else
        {
            Craft::$app->getSession()->setError(Craft::t('rollbar', 'Your settings are invalid'));
        }

        Craft::$app->getUrlManager()->setRouteParams([
            'settings' => $settings,
        ]);
    }
}