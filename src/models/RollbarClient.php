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
        $config = $this->_setConfig($config);

        Rollbar::init($config);

        parent::__construct([]);
    }

    public function log($level, $message, array $extraData = [])
    {
        return Rollbar::log($level, $message, $extraData);
    }

    private function _setConfig(array $config = [])
    {
        return array_merge([
            'access_token' => Plugin::getInstance()->getSettings()->accessToken,
            'environment'  => UrlHelper::siteUrl(),
            'person'       => $this->_getPerson(),
        ], $config);
    }

    /**
     * Gets the current user details
     *
     * @return array|null
     */
    private function _getPerson()
    {
        $user = Craft::$app->user->getIdentity();

        if ($user)
        {
            $fullName = ($user->getFullName() && $user->getFullName() !== $user->username)
                ? '('.$user->getFullName().')'
                : null;

            return [
                'id'       => $user->id,
                'email'    => $user->email,
                'username' => implode(' ', array_filter([
                    $user->username,
                    $fullName,
                ])),
            ];
        }
    }
}