<?php
namespace enovate\rollbar\controllers;

use Craft;
use craft\helpers\Json;
use craft\web\Controller;
use enovate\rollbar\models\Settings;
use enovate\rollbar\Plugin;
use Rollbar\Payload\Level;
use yii\web\NotAcceptableHttpException;
use yii\web\NotFoundHttpException;

class CspController extends Controller
{
    public    $enableCsrfValidation = false;
    protected $allowAnonymous       = ['report'];

    public function actionView(array $variables = [])
    {
        $variables['csp'] = Craft::$app->getConfig()->getConfigFromFile('content-security-policy');

        return $this->renderTemplate('rollbar/_csp', $variables);
    }

    public function actionReport(array $variables = [])
    {
        /** @var Plugin $plugin */
        $plugin   = Plugin::getInstance();

        /** @var Settings $settings */
        $settings = $plugin->getSettings();

        if (!$settings->cspEndpoint)
        {
            throw new NotFoundHttpException();
        }

        $report = Json::decode(file_get_contents('php://input'));

        if (!isset($report['csp-report']))
        {
            throw new NotAcceptableHttpException();
        }

        $message = Craft::t('rollbar', 'Blocked "{blockedUri}" on {documentUri} because it violates the `{effectiveDirective}` policy', [
            'blockedUri'         => isset($report['csp-report']['blocked-uri']) ? $report['csp-report']['blocked-uri'] : 'unknown',
            'documentUri'        => isset($report['csp-report']['document-uri'])        ? $report['csp-report']['document-uri']        : 'unknown',
            'effectiveDirective' => isset($report['csp-report']['effective-directive']) ? $report['csp-report']['effective-directive'] : 'unknown',
        ]);

        if ($plugin->rollbar->log(Level::WARNING, $message, $report['csp-report']))
        {
            Craft::$app->end(201);
        }
        else
        {
           throw new NotAcceptableHttpException();
        }
    }
}