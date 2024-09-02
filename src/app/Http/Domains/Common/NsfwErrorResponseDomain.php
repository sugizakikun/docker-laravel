<?php

namespace App\Http\Domains\Common;

class NsfwErrorResponseDomain
{
    public function __construct(
        int $errorCode,
        string $errorMessage,
        string $url
    ){
        $this->errorCode = $errorCode;
        $this->errorMessage = $errorMessage;
        $this->url = $url;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'code' => $this->errorCode,
            'message' => $this->errorMessage,
            'url' => $this->url,
            'alertBgColor' => 'danger'
        ];
    }
}
