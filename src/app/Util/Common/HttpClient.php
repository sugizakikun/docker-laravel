<?php

namespace App\Util\Common;

class HttpClient
{
    /**
     * @param string $url
     * @param srting $headers
     * @return false|string
     */
    public static function get(
        string $url, 
        string $header = 'Content-type: application/json; charset=UTF-8'
    ){
        $options = [
            'http' => [
                'method'=> 'GET',
                'header'=> $header
            ]
        ];

        $context = stream_context_create($options);
        return file_get_contents($url, false, $context);
    }

    /**
     * @param string $url
     * @param array $data
     * @param string $header
     * @param bool $isJson
     * @return false|string
     */
    public static function post(
        string $url, 
        array $data, 
        string $header = 'Content-type: application/json; charset=UTF-8',
        bool $isJson = true
    ){
        $content = $isJson 
            ? json_encode($data)
            : http_build_query($data);

        // ストリームコンテキストオプションの設定
        $options = [
            'http' => [
                'method'  => 'POST',
                'header'  => $header,
                'content' => $content, // データを送信
                'ignore_errors' => true // エラーを無視してレスポンスを取得する
            ]
        ];

        $context = stream_context_create($options);

        return  file_get_contents($url, false, $context);
    }
}
