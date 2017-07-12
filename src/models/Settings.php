<?php
namespace enovate\rollbar\models;

use craft\base\Model;

class Settings extends Model
{
    public $accessToken;
    public $clientAccessToken;
    public $reportInDevMode            = false;
    public $jsTracking                 = true;
    public $captureUnhandledRejections = false;
    public $enableCspEndpoint          = false;
    public $ignoreHTTPCodes            = '404, 403, 503';

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['accessToken', 'clientAccessToken'], 'required'],
        ];
    }
}
