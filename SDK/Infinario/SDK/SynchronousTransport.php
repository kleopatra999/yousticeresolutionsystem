<?php
/**
 * cURL implementation of transport class
 *
 * @author    Youstice
 * @copyright (c) 2015, Youstice
 * @license   http://www.apache.org/licenses/LICENSE-2.0.html  Apache License, Version 2.0
 */

class SynchronousTransport implements Transport
{
    public function postAndForget(Environment $environment, $url, $payload)
    {
        $this->post($environment, $url, $payload);
    }

    public function post(Environment $environment, $url, $payload)
    {
        $ch = curl_init($url);
        if ($ch === false) {
            $environment->exception(new Exception('Failed to init curl handle'));
            return false;
        }
        $payload = Tools::jsonEncode($payload);
        $environment->debug('posting to ' . $url, array('body' => $payload));
        $headers = array('Content-Type:application/json');
        if (curl_setopt($ch, CURLOPT_POSTFIELDS, $payload) === false) {
            $environment->exception(new Exception('failed setting payload'));
            curl_close($ch);
            return false;
        }
        if (curl_setopt($ch, CURLOPT_HTTPHEADER, $headers) === false) {
            $environment->exception(new Exception('failed setting headers'));
            curl_close($ch);
            return false;
        }
        if (curl_setopt($ch, CURLOPT_RETURNTRANSFER, true) === false) {
            $environment->exception(new Exception('failed setting returntransfer'));
            curl_close($ch);
            return false;
        }
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    public function check(Environment $environment)
    {
        if (!function_exists('curl_init')) {
            $environment->exception(new Exception('php curl module not available'));
        }
    }
}