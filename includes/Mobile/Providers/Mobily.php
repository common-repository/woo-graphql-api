<?php

namespace WCGQL\Mobile\Providers;

use WCGQL\Mobile\Contracts\MobileDriverInterface;

class Mobily extends MobileDriver implements MobileDriverInterface
{
    private $senderName;
    private $client;
    
    /**
     * Populate the credentials required to consume the api
     */
    public function __construct()
    {
        $username = get_option('mobily_username');
        $password = get_option('mobily_password');
        $APIKEY = get_option('mobily_apikey');

        $this->senderName = get_option('mobily_senderName');
        $this->client = new \MobilySms($username, $password, $APIKEY);
    }

    /**
     * Sends message via SMS
     * @param array $mobileNumber
     * @param string $messageContent
     * @return array
     */
    public function sendSMS($mobileNumber, $messageContent)
    {
        $response = [
            'data' => [],
            'errors' => []
        ];
        $telephone = $mobileNumber['country_code'] . $mobileNumber['phone_number'];
        $sender = urlencode($this->senderName);
        $timeSend = date('h:i:s');
        $dateSend = date('m/d/Y');
        $notRepeat = 1;
        $deleteKey = 0;
        $method = 'curl';

        $result = $this->client->sendMsg($messageContent, $telephone, $sender, $timeSend, $dateSend, $notRepeat,$deleteKey, $method);

        if ($result) {
            $response['data'][] = [
                'code' => 'DONE',
                'title' => 'Message Sent',
                'content' => 'Message has been sent successfully',
            ];
        } else {
            $response['errors'][] = [
                'code' => 'FAILED',
                'title' => 'Couldn\'t Send',
                'content' => 'An error Occured during Message Send',
            ];
        }
        return $response;
    }
}
