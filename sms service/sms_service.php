<?php
require_once __DIR__ . '/Mwaka.SHRS.2/sms service/africastalking-php-master/src/AfricasTalking.php';

use AfricasTalking\SDK\AfricasTalking;

class SMSService {

    private $sms;

    public function __construct(){
        $username = "nyale2254";
        $apiKey   = "atsk_eb86a49ea3b2451607488d48fd4141d1de675367a197d42e33e719bc52427ed0e16bc8ff 

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