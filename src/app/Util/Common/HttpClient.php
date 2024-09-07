<?php

namespace App\Util\Common;

class HttpClient
{
    /**
     * @param string $url
     * @param array $headers
     * @return false|string
     */
    public static function get(string $url)
    {
        $options = [
            'http' => [
                'method'=> 'GET',
                'header'=> 'Content-type: application/json; charset=UTF-8'
            ]
        ];

        $context = stream_context_create($options);
        return file_get_contents($url, false, $context);
    }

    /**
     * @param string $url
     * @param array $data
     * @return false|string
     */
    public static function post(string $url, array $data)
    {
        // ストリームコンテキストオプションの設定
        $options = [
            'http' => [
                'method'  => 'POST',
                'header'  => "Content-Type: application/json;" .
                    "charset=UTF-8",
                'content' => json_encode($data), // JSONエンコードされたデータを送信
                'ignore_errors' => true // エラーを無視してレスポンスを取得する
            ]
        ];

        $context = stream_context_create($options);

        return  file_get_contents($url, false, $context);
    }
}
