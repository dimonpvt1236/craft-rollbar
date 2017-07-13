<?php
namespace enovate\rollbar\services;

use enovate\rollbar\models\Client;
use yii\base\Component;

class Rollbar extends Component
{
    private $_client;

    public function getClient(Client $client = null)
    {
        if (!$client)
        {
            $client = $this->_client ? $this->_client : new Client();
        }

        if (!$this->_client)
        {
            $this->_client = $client;
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
