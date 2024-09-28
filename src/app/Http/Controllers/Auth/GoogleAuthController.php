<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Util\GoogleAuthClient;
use App\Http\Controllers\Controller;
use App\Http\Services\Auth\CreateGoogleUser;
use Illuminate\Support\Facades\Auth;

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

    public function handleOAuthCallback(CreateGoogleUser $createGoogleUser)
    {
        $code = request()->input('code');

        # 1.コールバックされたトークンを元にアクセストークンを取得
        $getTokenResponse = $this->googleAuthClient->getToken($code);
        
        if(array_key_exists('error', $getTokenResponse, )){
            $this->redirectToLogin('認証中にエラーが発生しました');
        }

        # 2. 1で受け取ったアクセストークンを元にOAuthからユーザー情報を取得
        $getUserInfoResponse = $this->googleAuthClient->getUserInfo($getTokenResponse);
        
        # 3. 2で受け取ったユーザー情報をusersテーブルにも登録
        $user = $createGoogleUser->execute($getUserInfoResponse);
        
        # 4. ユーザーを自動的にログインさせる
        Auth::login($user);

        return redirect(route('home'));
    }

    private function redirectToLogin($errorMessage)
    {
        return redirect('login')->with([
            'result' => $errorMessage,
            'bgColor' => 'warning'
        ]);
    }
}