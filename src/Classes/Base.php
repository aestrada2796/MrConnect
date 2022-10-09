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
     * @return string
     */
    protected function login(): string
    {
        $this->checkConfig();

        $curl = curl_init();
        $url = $this->routeBase . "/login";

        $body = [
            "query" => printf(Queries::$LOGIN, [config('mrconnect.api_user'), config('mrconnect.api_pass')])
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
        return $response;
    }

    /**
     * @autor Adrian Estrada
     * @return void
     */
    private function checkConfig(): void
    {
        if (empty(config('mrconnect.api_route'))) {
            response()->json([
                "data" => [
                    'error' => true,
                    'message' => 'API path not found',
                    'code' => 401
                ]
            ], 401);
            return;
        }

        if (empty(config('mrconnect.api_user'))) {
            response()->json([
                "data" => [
                    'error' => true,
                    'message' => 'User not found',
                    'code' => 401
                ]
            ], 401);
            return;
        }

        if (empty(config('mrconnect.api_pass'))) {
            response()->json([
                "data" => [
                    'error' => true,
                    'message' => 'Password not found',
                    'code' => 401
                ]
            ], 401);
        }
    }

    /**
     * @autor Adrian Estrada
     * @param $url
     * @param array $query
     * @return string|bool
     */
    protected function sendRequest($url, array $query): string|bool
    {
        $this->checkConfig();

        $curl = curl_init();

        $time = time();
        $token = "123";
        $signature = hash_hmac('sha256', json_encode($query), $time . $token);
        $body = [
            "query" => $query
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
                'x-api-time: ' . $time,
                'x-api-signature: ' . $signature,
                'Authorization: Bearer ' . $token,
                'accept:  application/json',
                'Content-Type: application/json',
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }
}