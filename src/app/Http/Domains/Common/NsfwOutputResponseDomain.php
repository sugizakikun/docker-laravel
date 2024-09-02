<?php

namespace App\Http\Domains\Common;

class NsfwOutputResponseDomain
{
    /**
     * @param float $score
     * @param string $url
     */
    public function __construct(
        float $score,
        string $url
    ){
        $this->score = $score;
        $this->url = $url;
    }

    public function toArray(): array
    {
        return [
            'score' => $this->score,
            'url' => $this->url,
            'message' => $this->getMessage(),
            'alertBgColor' => $this->getAlertBgColor()
        ];
    }

    /**
     * @return string
     */
    private function getMessage() :string
    {
        if($this->score >= 0.8){
            return '不適切な画像のアップロードが検知されました。';
        } elseif($this->score < 0.8 && $this->score >= 0.3) {
            return '画像のアップロードが完了しました。(※ただしサムネイルにぼかし処理が入ります。)';
        } else {
            return '画像のアップロードが完了しました。';
        }
    }

    /**
     * @return string
     */
    private function getAlertBgColor() :string
    {
        return $this->score >= 0.8 ? 'danger' : 'success';
    }
}
