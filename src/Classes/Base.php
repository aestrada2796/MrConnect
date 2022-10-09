<?php

namespace MrConnect\Classes;

class Base
{
    private string|null $routeBase;

    public function __construct()
    {
        $this->routeBase = config('mrconnect.api_route');
    }

    /**
     * @autor Adrian Estrada
     * @param $url
     * @param $query
     */
    public function sendRequest($url, $query, $var = [])
    {
        if (!session()->has('token')) {
            $this->login();
        }

        $token = session('token');

        if (empty($token)) {
            return ["data" => [
                'error' => true,
                'message' => "Authentication Error",
                'code' => 401
            ]
            ];
        }

        $error = $this->checkConfig();
        if (!empty($error)) {
            return $error;
        }

        $curl = curl_init();

        $time = time();
        $body = [
            "query" => $query,
            "variables" => json_encode($var)
        ];

        $signature = hash_hmac('sha256', json_encode($body), $time . $token);

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->routeBase . $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($body),
            CURLOPT_HTTPHEADER => array(
                'x-api-time: ' . $time,
                'x-api-signature: ' . $signature,
                'Authorization: Bearer ' . $token,
                'accept:  application/json',
                'Content-Type: application/json',
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return json_decode($response, true);
    }

    /**
     * @autor Adrian Estrada
     */
    public function login()
    {
        $error = $this->checkConfig();
        if (!empty($error)) {
            return $error;
        }

        $curl = curl_init();
        $url = $this->routeBase . "/login";

        $body = [
            "query" => Queries::$LOGIN,
            "variables" => [
                "user" => config('mrconnect.api_user'),
                "pass" => config('mrconnect.api_pass'),
            ]
        ];

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($body),
            CURLOPT_HTTPHEADER => array(
                'accept:  application/json',
                'Content-Type: application/json',
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);


        $login = json_decode($response, true);

        if (isset($login["data"])) {
            if (isset($login["data"]["login"])) {
                if ($login["data"]["login"] != "Incorrect user or password") {
                    session(["token" => $login["data"]["login"]]);
                } else {
                    session()->forget('token');
                    return null;
                }
            }
        }
        return $login;
    }

    /**
     * @autor Adrian Estrada
     * @return array[]|null
     */
    private function checkConfig(): array|null
    {
        $error = [
            "data" => [
                'error' => true,
                'message' => [

                ],
                'code' => 401
            ]
        ];
        $response = false;

        if (empty(config('mrconnect.api_route'))) {
            $response = true;
            $error["data"]["message"][] = 'API path not found';
        }

        if (empty(config('mrconnect.api_user'))) {
            $response = true;
            $error["data"]["message"][] = 'User not found';
        }

        if (empty(config('mrconnect.api_pass'))) {
            $response = true;
            $error["data"]["message"][] = 'Password not found';
        }

        return $response ? $error : null;
    }
}