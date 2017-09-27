<?php

namespace PushNotification;


/**
 * Class Notification
 * @package PushNotification
 */
class Notification
{
    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $text;

    /**
     * @var string
     */
    private $qrcode;

    const TEST_QRCODE = 'a69b85667172b507';  // Insert qrcode for tests


    /**
     * Notification constructor.
     * @param string $title
     * @param string $text
     * @param array $qr
     */
    public function __construct($title = 'test title', $text = 'text', $qr = ["child_name" => "Имя Устройства", "qr" => self::TEST_QRCODE])
    {
        $text = str_replace('name', $qr["child_name"], $text);
        $title = str_replace('name', $qr["child_name"], $title);
        $this->title = $title;
        $this->text = $text;
        $this->qrcode = ($qr["qr"]) ?: self::TEST_QRCODE;
    }


    /**
     * @return string
     */
    public function getTitle(){
        return $this->title;
    }


    /**
     * @return string
     */
    public function getText(){
        return $this->text;
    }


    /**
     * @return string
     */
    public function getQrcode(){
        return $this->qrcode;
    }
}