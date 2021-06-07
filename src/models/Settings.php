<?php
namespace enovatedesign\rollbar\models;

use craft\base\Model;
use craft\helpers\UrlHelper;

class Settings extends Model
{
    public $accessToken;
    public $clientAccessToken;
    public $reporting;
    public $jsTracking;
    public $captureUnhandledRejections;
    public $ignoreHTTPCodes;
    public $ignoreRules;
    public $environment;
    public $rollbarJsVersion;
    public $captureIp;
    public $captureUsername;
    public $captureEmail;

    public function init()
    {
        // Defaults
        $this->reporting = true;
        $this->jsTracking = true;
        $this->rollbarJsVersion = '2.21.0';
        $this->captureIp = 'anonymize';
        $this->captureUsername = false;
        $this->captureEmail = false;
        $this->captureUnhandledRejections = false;
        $this->environment = UrlHelper::siteUrl();
        $this->ignoreHTTPCodes = '404, 403, 503';
        $this->ignoreRules = [
            'Unable to verify your data submission.',
            'Invalid verification code',
            'test message',
        ];

        parent::init();
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['accessToken', 'environment'], 'required'],
            [['accessToken', 'clientAccessToken'], 'string', 'length' => 32],
            [['reporting', 'jsTracking', 'captureUnhandledRejections', 'captureEmail', 'captureUsername'], 'boolean'],
        ];
    }
}
