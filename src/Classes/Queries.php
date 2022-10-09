<?php

namespace MrConnect\Classes;

class Queries
{
    public static string $LOGIN = 'mutation Login($user: String!, $pass: String!) {
            login(email: $user, password: $pass)
        }';

    public static string $TEST = 'query {
        users(id: "5677f026-b5c6-474b-a927-6e90afd12d16"){
            id,
            name,
            roles {
                name
            }
        }
    }';
}