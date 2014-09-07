<?php
/**
 * Created by PhpStorm.
 * User: daghostman
 * Date: 07/09/14
 * Time: 18:52
 */

namespace Model;


class HoroscopeAPIModel {
    private $sign = null;

    public function __construct($sign)
    {
        $this->sign = $sign;

    }

    public function fetch()
    {
        if (is_null($this->sign)) {
            throw new \InvalidArgumentException('Invalid zodiac sign specified.');
        }
        $request = new \Wave\Framework\Http\Curl\Request();
        $r = $request->setUrl(sprintf('http://widgets.fabulously40.com/horoscope.json?sign=%s', $this->sign))
            ->setMethod('GET')
            ->setUA('WaveFramework/2.0 Http\Curl\Request Client')
            ->send();

        return (!is_null($r) ? json_decode($r->getData(), true) : $r);
    }
} 
