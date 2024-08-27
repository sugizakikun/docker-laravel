<?php

namespace App\Util;

class HttpClient
{
    /**
     * @param string $url
     * @param array $headers
     * @return false|string
     */
    public static function get(string $url)
    {
        $options = array(
            'http' => array(
                'method'=> 'GET',
                'header'=> 'Content-type: application/json; charset=UTF-8'
            )
        );

        $context = stream_context_create($options);
        return file_get_contents($url, false, $context);
    }
}
