<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Util\GoogleAuthClient;

class GoogleAuthController extends Controller
{
    private $googleAuthClient;

    public function __construct(GoogleAuthClient $googleAuthClient)
    {
       $this->googleAuthClient = $googleAuthClient;
    }

    public function handleOAuthCallback()
    {
        $code = request()->input('code');

        $getTokenResponse = $this->googleAuthClient->getToken($code);
        
        if(array_key_exists('error', $getTokenResponse, )){
            $this->redirectToLogin('認証中にエラーが発生しました');
        }

        $getUserInfoResponse = $this->googleAuthClient->getUserInfo($getTokenResponse);
    }

    private function redirectToLogin($errorMessage)
    {
        return redirect('login')->with([
            'result' => $errorMessage,
            'bgColor' => 'warning'
        ]);
    }
}