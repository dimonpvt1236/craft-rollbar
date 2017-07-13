<?php

namespace enovate\rollbar\models;

use Craft;
use craft\helpers\ArrayHelper;
use craft\helpers\UrlHelper;
use enovate\rollbar\Plugin;
use craft\base\Model;
use Rollbar\Rollbar;

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

    public function shouldReport($exception)
    {
        $ignoreCodes = ArrayHelper::toArray(Plugin::getInstance()->getSettings()->ignoreHTTPCodes);
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