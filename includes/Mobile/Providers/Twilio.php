<?php

namespace WCGQL\Mobile\Providers;

use Twilio\Rest\Client;
use WCGQL\Mobile\Contracts\MobileDriverInterface;

class Twilio extends MobileDriver implements MobileDriverInterface
{
    private $twilio;
    private $senderName;
    private $phoneNumber;

    /**
     * Populate the credentials required to consume the api
     */
    public function __construct()
    {
        $sid = get_option('twilio_sid');
        $token = get_option('twilio_token');
        $this->phoneNumber = get_option('twilio_phoneNumber');
        $this->senderName = get_option('twilio_senderName');
        $this->twilio = new Client($sid, $token);
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
        try {
            $from = $this->senderName ?? $this->phoneNumber;
            $this->twilio->messages
                ->create(
                    $telephone, // to
                    array(
                        "body" => urldecode($messageContent),
                        "from" => $from,
                    )
                );
            $response['data'][] = [
                'code' => 'DONE',
                'title' => 'Message Sent',
                'content' => 'Message has been sent successfully',
            ];
        } catch (\Exception $error) {
            $response['errors'][] = [
                'code' => 'FAILED',
                'title' => 'Couldn\'t Send',
                'content' => 'An error Occured during Message Send -> Brief: ' . $error->getMessage(),
            ];
        }
        return $response;
    }
}
