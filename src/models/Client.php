<?php

namespace enovatedesign\rollbar\models;

use Craft;
use craft\helpers\StringHelper;
use enovatedesign\rollbar\Plugin;
use craft\base\Model;
use Rollbar\Rollbar;

class Client extends Model
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

    public function shouldReport($exception)
    {
        $plugin = Plugin::getInstance();

        /** @var Settings $settings */
        $settings = $plugin->getSettings();

        if (!$settings->reporting)
        {
            return false;
        }

        $ignoreCodes = StringHelper::split($settings->ignoreHTTPCodes);
        $ignoreCodes = array_map('intval', $ignoreCodes);

        $status = $this->getExceptionCode($exception);

        return !in_array($status, $ignoreCodes);
    }

    public function getExceptionCode($exception)
    {
        $status = null;
        
        if (property_exists($exception, 'statusCode'))
        {
            $status = (int) $exception->statusCode;
        }
        else if (method_exists($exception, 'getPrevious') && $previous = $exception->getPrevious())
        {
            if (is_object($previous) && property_exists($previous, 'statusCode'))
            {
                $status = (int) $previous->statusCode;
            }
        }
        
        return $status;
    }
    
    private function _setConfig(array $config = [])
    {
        $plugin   = Plugin::getInstance();
        /** @var Settings $settings */
        $settings = $plugin->getSettings();

        return array_merge([
            'access_token' => $settings->accessToken,
            'environment'  => $settings->environment,
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
        $person = [];
        $user   = Craft::$app->user->getIdentity();

        if ($user)
        {
            $fullName = ($user->getFullName() && $user->getFullName() !== $user->username)
                ? '('.$user->getFullName().')'
                : null;

            $person = [
                'id'       => $user->id,
                'email'    => $user->email,
                'username' => implode(' ', array_filter([
                    $user->username,
                    $fullName,
                ])),
            ];
        }

        return $person;
    }
}