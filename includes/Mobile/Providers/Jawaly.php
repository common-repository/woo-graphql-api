<?php

namespace WCGQL\Mobile\Providers;

use WCGQL\Mobile\Contracts\MobileDriverInterface;

class Jawaly extends MobileDriver implements MobileDriverInterface
{
    private $USER;
    private $PASSWORD;
    private $SENDERNAME;

    /**
     * Populate the credentials required to consume the api
     */
    public function __construct()
    {
        $this->USER = get_option('jawaly_username');
        $this->PASSWORD = get_option('jawaly_password');
        $this->SENDERNAME = get_option('jawaly_sendername');
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
        $username = $this->USER;
        $password = $this->PASSWORD;
        $senderName = $this->SENDERNAME;

        $url = 'http://www.4jawaly.net/api/sendsms.php';

        $query_array = array(
            'username' => $username,
            'password' => $password,
            'numbers' => $telephone,
            'message' => $messageContent,
            'sender' => $senderName,
            'unicode' => 'E',
            'return' => 'json'
        );

        $query = http_build_query($query_array);
        $result = file_get_contents($url . '?' . $query);

        $response = json_decode($result, true);
        if ($response['Code'] == 100) {
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
