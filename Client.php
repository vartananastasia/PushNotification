<?php

namespace PushNotification;

use GuzzleHttp\Client as GC;


/**
 * Class Client
 *
 * @package PushNotification
 */
class Client
{
    /**
     * @var array
     */
    private $body;

    /**
     * @var obj Notification
     */
    private $notification;

    # API settings
    const api_base_url = 'misafes1.qiwocloud2.com';
    const action = 'push_custome_message_to_family_by_device_id';
    const token = 'c55e7825';
    const message_type = '304';


    /**
     * Client constructor.
     * @param Notification $notification
     */
    public function __construct(Notification $notification)
    {
        $time = time();
        $this->notification = $notification;
        $this->body = [
            'time' => $time,
            'action' => self::action,
            'api_token' => self::api_token($time),
//            'access_token' => 'sess::04f26357afe965ea26854be35022fb79',
            'token' => self::token,
            'message_type' => self::message_type,
            'message_title' => $notification->getTitle(),
            'message_content' => $notification->getText(),
        ];
    }


    /**
     * @param $time
     * @return string
     */
    private function api_token($time){
        return md5($time.'abcdefg');
    }


    /**
     * pushes notification to users with qrcode
     */
    public function push()
    {
        $this->body["qrcode"] = $this->notification->getQrcode();
        $body = self::body();

        $client = new GC();
        $response = $client->request('POST', self::api_base_url, [
                "headers" => [
                    'content-type' => 'application/json'
                ],
            "body" => $body
            ]);
        $data = self::JsonInAr($response->getBody());

        if ($data[1]) {
            if (!$data[0]->error){
                foreach ($data[0]->info->result_of_each_user as $phone => $status)
                    self::Log($phone);
            }
        }
    }


    /**
     * Write log
     *
     * @param $phone
     */
    private function Log($phone){
        $checked = \Lexand\Hiload::GetHLEntityClass(\Lexand\Helper::PUSH_NOTIFICATION);
        $checked::add(
            [
                'UF_PHONE' => $phone,
                'UF_TEXT' => $this->body["message_title"].': '.$this->body["message_content"],
                'UF_QRCODE' => $this->body["qrcode"],
                'UF_DATE' => date("d.m.Y H:i:s")
            ]
        );
    }


    /**
     * constructing body of request
     *
     * @return string
     */
    private function body(){
        $body = '';
        foreach ($this->body as $key => $val)
        {
            $val = trim($val);
            if ($val)
            {
                $body .= $key . '=' . $val . '&';
            }
        }

        $body = substr($body,0,-1);

        return $body;
    }



    /**
     * terns json in arr
     *
     * @param $json
     * @return mixed
     */
    public static function JsonInAr($json)
    {
        $data = json_decode($json);
        $error = json_last_error();

        if ($error == JSON_ERROR_NONE)
        {
            return [$data, True];
        }else{
            return [json_last_error_msg(), False];
        }
    }
}