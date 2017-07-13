<?php
namespace enovate\rollbar\models;

use craft\base\Model;
use craft\helpers\UrlHelper;

class Settings extends Model
{
    public $accessToken;
    public $clientAccessToken;
    public $reporting;
    public $jsTracking;
    public $captureUnhandledRejections;
    public $enableCspEndpoint;
    public $ignoreHTTPCodes;
    public $environment;

    public function init()
    {
        $this->reporting                  = true;
        $this->jsTracking                 = true;
        $this->captureUnhandledRejections = false;
        $this->enableCspEndpoint          = false;
        $this->ignoreHTTPCodes            = '404, 403, 503';
        $this->environment                = UrlHelper::siteUrl();

        parent::init();
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['accessToken', 'clientAccessToken', 'environment'], 'required'],
            [['accessToken', 'clientAccessToken'], 'string', 'length' => 32],
            [['reporting', 'jsTracking', 'captureUnhandledRejections', 'enableCspEndpoint'], 'boolean'],
        ];
    }
}
