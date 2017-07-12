<?php
namespace enovate\rollbar\models;

use Craft;
use craft\helpers\UrlHelper;
use enovate\rollbar\Plugin;
use craft\base\Model;
use Rollbar\Rollbar;
use Rollbar\Payload\Level;

class RollbarClient extends Model
{
    public function __construct(array $config = [])
    {
        $config = array_merge([
            'access_token' => Plugin::getInstance()->getSettings()->accessToken,
            'environment'  => UrlHelper::siteUrl(),
        ], $config);

        Rollbar::init($config);

        parent::__construct([]);
    }

    public function log($level, $message, array $extraData = [])
    {
        return Rollbar::log($level, $message, $extraData);
    }
}