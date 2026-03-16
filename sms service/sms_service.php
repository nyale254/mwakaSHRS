<?php
require_once __DIR__ . '/Mwaka.SHRS.2/sms service/africastalking-php-master/src/AfricasTalking.php';

use AfricasTalking\SDK\AfricasTalking;

class SMSService {

    private $sms;

    public function __construct(){
        $username = "sandbox";
        $apiKey   = "atsk_1d85ec6c26bf0b9922b68a862f70e0c52aa7f1a242c9ff13ee08f7c2a62b76a5943ae60d

";

        $AT = new AfricasTalking\AfricasTalking($username,$apiKey);
        $this->sms = $AT->sms();
    }

    public function send($phone,$message){

        try {

            $result = $this->sms->send([
                'to' => $phone, 
                'message' => $message
            ]);

            return ['success'=>true,'response'=>$result];

        } catch (Exception $e) {

            return ['success'=>false,'error'=>$e->getMessage()];
        }
    }
}