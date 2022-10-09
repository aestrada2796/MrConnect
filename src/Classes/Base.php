<?php

namespace MrConnect\Classes;

class Base
{
    protected string|null $routeBase;
    protected string|null $fullUrl;
    protected array|string|null $query;
    protected array $var = [];

    public function __construct($url)
    {
        $this->routeBase = config('mrconnect.api_route');
        $this->fullUrl = $this->routeBase . $url;
    }

    /**
     * @autor Adrian Estrada
     * @param $var
     * @return $this
     */
    public function variables($var): static
    {
        $this->var = $var;
        return $this;
    }

    /**
     * @autor Adrian Estrada
     */
    protected function sendRequest()
    {
        if (!cache()->has('token')) {
            return $this->sendLogin('sendRequest');
        }

        $token = cache('token');

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
            "query" =>
                $this->query
            ,
            "variables" => json_encode($this->var, JSON_PRETTY_PRINT)
        ];

        $signature = hash_hmac('sha256', json_encode($body, JSON_PRETTY_PRINT), $time . $token);

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->fullUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($body, JSON_PRETTY_PRINT),
            CURLOPT_HTTPHEADER => array(
                'x-api-time: ' . $time,
                'x-api-signature: ' . $signature,
                'Authorization: Bearer ' . $token,
                'accept:  application/json',
                'Content-Type: application/json',
            ),
        ));

        $response = curl_exec($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($httpcode == 401) {
            cache()->forget('token');
            return $this->sendLogin('sendRequest');
        }

        return json_decode($response);
    }

    /**
     * @autor Adrian Estrada
     */
    public function sendLogin($function = null)
    {
        $error = $this->checkConfig();
        if (!empty($error)) {
            return $error;
        }

        $curl = curl_init();
        $url = $this->routeBase . "/login";

        $body = [
            "query" => Variables::$LOGIN,
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
            CURLOPT_POSTFIELDS => json_encode($body, JSON_PRETTY_PRINT),
            CURLOPT_HTTPHEADER => array(
                'accept:  application/json',
                'Content-Type: application/json',
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        $login = json_decode($response);

        if (isset($login->data)) {
            if (isset($login->data->login)) {
                if ($login->data->login != "Incorrect user or password") {
                    cache(["token" => $login->data->login]);
                    if (!empty($function)) {
                        return $this->$function();
                    }
                    return cache('token');
                } else {
                    cache()->forget('token');
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
