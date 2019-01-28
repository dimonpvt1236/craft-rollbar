<?php
namespace enovatedesign\rollbar\models;

use enovatedesign\rollbar\Plugin;
use Rollbar\Rollbar;
use Craft;
use craft\helpers\StringHelper;
use craft\base\Model;
use Throwable;

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

    public function shouldReport(Throwable $throwable)
    {
        $plugin = Plugin::getInstance();

        /** @var Settings $settings */
        $settings = $plugin->getSettings();

        if (!$settings->reporting) {
            return false;
        }

        if (!\is_array($settings->ignoreHTTPCodes)) {
            $ignoreCodes = StringHelper::split($settings->ignoreHTTPCodes);
        }

        $ignoreCodes = array_map('intval', $ignoreCodes);

        $status = $this->getStatusCode($throwable);

        if (in_array($status, $ignoreCodes)) {
            return false;
        }
        
        // Check ignore rules
        $message = $throwable->getMessage();
        $pluginRules = Plugin::$plugin->getSettings()->ignoreRules);

        if (!\is_array($pluginRules)) {
            $pluginRules = explode("\n", $pluginRules);
        }

        foreach ($pluginRules as $rule) {
            $rule = trim($rule);
            if (preg_match("|" . preg_quote($rule, "|") . "|", $message) != 0) {
                return false;
            }
        }

        return true;
    }

    public function getStatusCode($throwable)
    {
        $status = null;
        
        if (property_exists($throwable, 'statusCode')) {
            $status = (int) $throwable->statusCode;
        } else if (method_exists($throwable, 'getPrevious') && $previous = $throwable->getPrevious()) {
            if (is_object($previous) && property_exists($previous, 'statusCode')) {
                $status = (int) $previous->statusCode;
            }
        }
        
        return $status;
    }
    
    private function _setConfig(array $config = [])
    {
        $plugin = Plugin::getInstance();
        /** @var Settings $settings */
        $settings = $plugin->getSettings();

        return array_merge([
            'access_token' => $settings->accessToken,
            'environment' => $settings->environment,
            'capture_email' => $settings->captureEmail,
            'capture_username' => $settings->captureUsername,
            'include_error_code_context' => true,
            'include_exception_code_context' => true,
            'capture_ip' => ($settings->captureIp === 'anonymize') ? $settings->captureIp : (bool) $settings->captureIp,
            'person' => $this->_getPerson(),
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
        $user = Craft::$app->user->getIdentity();

        if ($user) {
            /** @var Settings $settings */
            $settings = Plugin::getInstance()->getSettings();

            $person = [
                'id' => $user->id,
            ];

            if ($settings->captureEmail) {
                $person['email'] = $user->email;
            }

            if ($settings->captureUsername) {
                $fullName = ($user->getFullName() && $user->getFullName() !== $user->username)
                    ? '(' . $user->getFullName() . ')'
                    : null;

                $person['username'] = implode(' ', array_filter([
                    $user->username,
                    $fullName,
                ]));
            }
        }

        return $person;
    }
}