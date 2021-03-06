<?php

namespace Nexmo\Service;

use Nexmo\Exception as NexmoException;
use Nexmo\Exception;

/**
 * Class MarketingMessage
 * @package Nexmo\Service
 */
class MarketingMessage extends Service
{
    /**
     * @return string The short code marketing SMS endpoint.
     */
    public function getEndpoint()
    {
        return 'sc/us/marketing/json';
    }

    /**
     * Send a marketing message from Nexmo's shared short code.
     *
     * @param string|int    $from           Required. Nexmo Shared Short Code, can be found on your Dashboard.
     * @param string        $keyword        Required. The keyword you selected during Shared Short Code sign up process.
     * @param string|int    $to             Required. Mobile number in international format.
     *                                      Ex: 447525856424 or 00447525856424 when sending to UK.
     * @param string        $text           Required. Body of the text message.
     * @throws \Nexmo\Exception
     * @return array
     */
    public function invoke($from = null, $keyword = null, $to = null, $text = '')
    {
        if(!$from) {
            throw new Exception("\$from parameter cannot be blank");
        }

        if(!$keyword) {
            throw new Exception("\$keyword parameter cannot be blank");
        }

        if(!$to) {
            throw new Exception("\$to parameter cannot be blank");
        }

        if(!$text) {
            throw new Exception("\$text parameter cannot be blank");
        }

        return $this->exec([
            'from' => $from,
            'keyword' => $keyword,
            'to' => $to,
            'text' => $text
        ]);
    }

    protected function validateResponse(array $json)
    {
        if (!isset($json['message-count'])) {
            throw new NexmoException('message-count property expected');
        }

        if (!isset($json['messages'])) {
            throw new NexmoException('messages property expected');
        }

        foreach ($json['messages'] as $message) {
            if (!isset($message['status'])) {
                throw new NexmoException('status property expected');
            }

            if (!empty($message["error-text"])) {
                throw new NexmoException("Unable to send sms message: " . $message["error-text"] . ' - status ' . $message['status']);
            }

            if ($message['status'] > 0) {
                throw new NexmoException("Unable to send sms message: status " . $message['status']);
            }
        }

        return true;
    }
}
