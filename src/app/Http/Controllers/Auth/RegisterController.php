<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
// 追加
use App\Cognito\CognitoClient;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Registered;

class RegisterController extends Controller
{
    use RegistersUsers;

    protected $redirectTo = '/home';

    private $cognitoClient; // 追加

    public function __construct(CognitoClient $cognitoClient)
    {
        $this->middleware('guest');
        // CognitoのGuardを読み込む
        $this->cognitoClient = $cognitoClient;
    }

    public function register(Request $request)
    {
        #DB::beginTransaction();

        $data = $request->all();
        $this->validator($data)->validate();

        $this->create($data, $data['username']);

        /*
        // Cognito側の新規登録
        $username = $this->cognitoClient->register(
            $data['email'],
            $data['password'],
            [
                'email' => $data['email'],
            ]
        );

        // Laravel側の新規登録
        $user = $this->create($data, $username);
        event(new Registered($user));

        DB::commit();*/

        return redirect()->route('home');
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users', 'cognito_user_unique'],
            'password' => [
                'required', 'string', 'min:8', 'confirmed',
                'regex:/\A(?=.*?[a-z])(?=.*?[A-Z])(?=.*?\d)[a-zA-Z\d]{8,100}+\z/'
            ],
        ]);
    }

    protected function create(array $data, $username)
    {
        return User::create([
            'cognito_username' => $username,
            'email'            => $data['email'],
        ]);
    }
}
