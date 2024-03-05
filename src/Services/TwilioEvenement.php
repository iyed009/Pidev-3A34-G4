<?php

namespace App\Services;

use Twilio\Rest\Client;

class TwilioEvenement
{
    private $accountSid;
    private $authToken;
    private $twilioNumber;
    private $client;

    public function __construct()
    {
        $this->accountSid = "ACcabe995c392f3c6ac59a663e22e7b326";
        $this->authToken = "acc309941bff8fe69b3ea7ffca7b7407";
        $this->twilioNumber = "+14194929057";
        $this->client = new Client($this->accountSid, $this->authToken);
    }

    public function sendSms(string $to, string $body)
    {
        $client =new Client( $this->accountSid,  $this->authToken);
        $message= $client->messages->create(
            $to,
            [
                'from' => $this->twilioNumber,
                'body' => $body,
            ]
        );
    }
}