<?php

namespace MrConnect\Classes;

class Variables
{
    /**
     * @autor Adrian Estrada
     * @var string
     */
    public static string $LOGIN = 'mutation Login($user: String!, $pass: String!) {
            login(email: $user, password: $pass)
        }';
}
