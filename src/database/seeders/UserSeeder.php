<?php

namespace Database\Seeders;

use App\Util\HttpClient;
use App\Cognito\CognitoClient;
use Aws\CognitoIdentityProvider\CognitoIdentityProviderClient;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    public function __construct(CognitoClient $cognitoClient, HttpClient $httpClient)
    {
        $this->cognitoClient = $cognitoClient;
        $this->httpClient = $httpClient;
        $this->clientId = env('AWS_COGNITO_CLIENT_ID');
        $this->clientSecret = env('AWS_COGNITO_CLIENT_SECRET');
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $names = $this->callNameGenerateApi(10);

        // Cognitoクライアントをインスタンス化
        $cognitoClient = new CognitoIdentityProviderClient([
            'region'  => env('AWS_COGNITO_REGION'),
            'version' => 'latest',
            'credentials' => [
                'key'    => env('AWS_ACCESS_KEY_ID'),
                'secret' => env('AWS_SECRET_ACCESS_KEY'),
            ],
        ]);

        for($i=0; $i<10; $i++){
            $email = 'example' . $i . '@company.com';
            $password = 'mikiAWS'. str_pad($i, 3, 0, STR_PAD_LEFT);

            $attributes['email'] = $email;

            $response = $cognitoClient->signUp([
                'ClientId' =>  $this->clientId ,
                'Password' => $password,
                'SecretHash' => $this->cognitoSecretHash($email),
                'UserAttributes' => $this->formatAttributes($attributes),
                'Username' => $email
            ]);

            $gender = $this->callGenderizeApi($names[$i][2]);

            DB::table('users')->insert([
                'cognito_username' => $response['UserSub'],
                'email'            => $email,
                'name'             => $names[$i][2]
            ]);
        }
    }

    protected function callNameGenerateApi(int $num=1)
    {
        // APIアクセスURL
        $prefix = 'https://green.adam.ne.jp/roomazi/cgi-bin/randomname.cgi';
        $suffix = '/roomazi/cgi-bin/randomname.cgi?n='. $num;
        $url = $prefix . $suffix;

        $rawData = $this->httpClient->get($url);
        $jsonString = preg_replace('/^callback\(|\);?$/', '',  $rawData);
        $jsonArray = json_decode($jsonString, true);

        return $jsonArray["name"];
    }

    protected function callGenderizeApi(string $name)
    {
        $suffix = 'https://api.genderize.io/?name=';
        $url = $suffix . str_replace(' ', '%20', $name);

        $rawData = $this->httpClient->get($url);
        $jsonArray = json_decode($rawData, true);

        return $jsonArray["gender"];
    }

    /**
     * Creates the Cognito secret hash
     * @param string $username
     * @return string
     */
    protected function cognitoSecretHash($username)
    {
        return $this->hash($username . $this->clientId);
    }

    /**
     * Creates a HMAC from a string
     *
     * @param string $message
     * @return string
     */
    protected function hash($message)
    {
        $hash = hash_hmac(
            'sha256',
            $message,
            $this->clientSecret,
            true
        );

        return base64_encode($hash);
    }

    /**
     * Format attributes in Name/Value array
     *
     * @param  array $attributes
     * @return array
     */
    protected function formatAttributes(array $attributes)
    {
        $userAttributes = [];

        foreach ($attributes as $key => $value) {
            $userAttributes[] = [
                'Name' => $key,
                'Value' => $value,
            ];
        }

        return $userAttributes;
    }

}
