<?php

namespace App\Http\Services\Common;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

trait ImageUploaderTrait
{
    /**
     * @param UploadedFile $uploadedFile
     * @return array
     */
    public function storeImage(UploadedFile $uploadedFile):array
    {
        $path = $uploadedFile->store('public/img');
        $fileContents = Storage::get($path);

        $randomStr = base_convert(md5(uniqid()), 16,36);
        $ext = $uploadedFile->guessExtension();
        $fileName = "$randomStr.$ext";

        Storage::disk('s3')->put($fileName, $fileContents);

        return [
            'url'  => Storage::disk('s3')->url($fileName),
            'key' => $fileName,
            'local_path' => $path,
        ];
    }

    /**
     * @param UploadedFile[]
     * @return array
     */
    protected function batchStoreImages(array $uploadedFiles):array
    {
        $files = [];

        foreach ($uploadedFiles as $uploadedFile) {
            $files[] = $this->storeImage($uploadedFile);
        }

        return $files;
    }

    /**
     * @param string $imageKey
     * @return void
     */
    public function deleteUploadedImage(string $imageKey) :void
    {
        Storage::disk('s3')->delete($imageKey);
    }
}
