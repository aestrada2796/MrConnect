<?php

namespace MrConnect\Classes;

class Query extends Base
{
    /**
     * @autor Adrian Estrada
     * @param string $mediaId
     * @return static
     */
    public static function make($url): static
    {
        return app(static::class, ["url" => $url]);
    }

    public function send()
    {
        return $this->sendRequest();
    }

    public function setQuery()
    {
        $this->query = Queries::$TEST;
        return $this;
    }

    public function setVariables($var)
    {
        $this->var = $var;
        return $this;
    }
}
