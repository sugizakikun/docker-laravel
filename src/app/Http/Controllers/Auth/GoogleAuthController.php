<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Util\GoogleAuthClient;
use App\Http\Controllers\Controller;

class GoogleAuthController extends Controller
{
    private $googleAuthClient;

    public function __construct(GoogleAuthClient $googleAuthClient)
    {
       $this->googleAuthClient = $googleAuthClient;
    }

    public function redirectToGoogle()
    {
        $url = $this->googleAuthClient->getAuthUrl();
        return redirect($url);
    }

    public function handleOAuthCallback()
    {
        $code = request()->input('code');

        $getTokenResponse = $this->googleAuthClient->getToken($code);
        
        if(array_key_exists('error', $getTokenResponse, )){
            $this->redirectToLogin('認証中にエラーが発生しました');
        }

        $getUserInfoResponse = $this->googleAuthClient->getUserInfo($getTokenResponse);
        dd($getUserInfoResponse);
    }

    private function redirectToLogin($errorMessage)
    {
        return redirect('login')->with([
            'result' => $errorMessage,
            'bgColor' => 'warning'
        ]);
    }
}