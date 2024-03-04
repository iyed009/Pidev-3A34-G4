<?php

namespace App\Service;
use Twilio\Rest\Client;

class TwilioReservation
{

    private $accountSid;
    private $authToken;
    private $twilioNumber;
    private $client;

    public function __construct()
    {
        $this->accountSid = "ACfecb783380bed9a39dfa7d27edca8eb7";
        $this->authToken = "3452deaa99a19ea237c93f60a882b80c";
        $this->twilioNumber = "+16503382047";
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
