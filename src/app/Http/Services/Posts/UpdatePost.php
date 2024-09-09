<?php

namespace App\Http\Services\Posts;

use App\Http\Services\Common\NgWordMaskingTrait;
use App\Models\Post;
use App\Util\GooLabApiClient;

class UpdatePost
{
    use NgWordMaskingTrait;

    /**
     * @var GooLabApiClient
     */
    protected $gooApiClient;

    /**
     * @param GooLabApiClient $gooApiClient
     */
    public function __construct(GooLabApiClient $gooApiClient)
    {
        $this->gooApiClient = $gooApiClient;
    }

    public function execute(int $postId, string $content)
    {
        $formattedWordList = $this->gooApiClient->morph($content);
        $maskedSentence = $this->maskingProcess($formattedWordList);

        (new Post())
            ->where('id', $postId)
            ->update(['content' => $maskedSentence]);
    }
}
