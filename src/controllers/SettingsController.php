<?php
namespace enovatedesign\rollbar\controllers;

use enovatedesign\rollbar\models\Client;
use enovatedesign\rollbar\models\Settings;
use Craft;
use craft\web\Controller;
use enovatedesign\rollbar\Plugin;
use Exception;
use Rollbar\Payload\Level;
use Rollbar\Response;

class SettingsController extends Controller
{
    public function init()
    {
        parent::init();

        // All settings actions require an admin
        $this->requireAdmin();
    }

    /**
     * Tests the rollbar settings.
     */
    public function actionTest()
    {
    
        $this->requirePostRequest();

        // Create a Settings model populated with the post data
        $requestService = Craft::$app->getRequest();
        $sessionService = Craft::$app->getSession();
        
        $settings = new Settings();
        $settings->accessToken = $requestService->getBodyParam('settings.accessToken');
        $settings->clientAccessToken = $requestService->getBodyParam('settings.clientAccessToken');
        $settings->reporting = true;

        if ($settings->validate()) {
            $client = new Client([
                'access_token' => $settings->accessToken,
            ]);

            /** @var Response $response */
            $response = $client->log(Level::INFO, Craft::t('rollbar', 'This is a test message'));
            
            if ($response->wasSuccessful()) {
                // Assume that these will also be successful
                $client->log(Level::WARNING, Craft::t('rollbar', 'This is a test warning'));
                $client->log(Level::ERROR,   Craft::t('rollbar', 'This is a test error'));

                $sessionService->setNotice(Craft::t('rollbar', 'Test message posted successfully'));
            } else {
                $sessionService->setError(Craft::t('rollbar', 'Test message was not posted successfully'));
            }
        } else {
            $sessionService->setError(Craft::t('rollbar', 'Your settings are invalid'));
        }

        Craft::$app->getUrlManager()->setRouteParams([
            'settings' => $settings,
        ]);
    }
}