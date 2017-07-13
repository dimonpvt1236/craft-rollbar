<?php
namespace enovate\rollbar\services;

use Craft;
use enovate\rollbar\models\RollbarClient;
use Rollbar\Payload\Level;
use yii\base\Component;

class Rollbar extends Component
{
    private $_client;

    public function getClient(RollbarClient $client = null)
    {
        if (!$client)
        {
            if (!$this->_client)
            {
                // Create a new RollbarClient model with the default plugin settings
                $this->_client = new RollbarClient();
            }

            $client = $this->_client;
        }

        return $client;
    }

    public function shouldReport($exception)
    {
        return $this->getClient()->shouldReport($exception);
    }

    public function log($level, $message, array $extraData = [])
    {
        return $this->getClient()->log($level, $message, $extraData);
    }
}
