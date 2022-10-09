<?php

namespace MrConnect\Classes;

class Queries
{
    public static string $LOGIN = "mutation {
            login(email:'%s',password:'%s')
        }";
}