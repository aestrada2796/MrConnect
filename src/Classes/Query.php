<?php

namespace MrConnect\Classes;

class Query extends Base
{
    protected $users = null;

    public function __construct($url)
    {
        parent::__construct($url);

        $this->query = "query { ";
    }

    /**
     * @autor Adrian Estrada
     * @param string $mediaId
     * @return static
     */
    public static function make($url): static
    {
        return app(static::class, ["url" => $url]);
    }

    /**
     * @autor Adrian Estrada
     * @return array[]|mixed|void|null
     */
    public function send()
    {
        $this->query .= " }";
        return $this->sendRequest();
    }

    /**
     * @autor Adrian Estrada
     * @return array[]|mixed|void|null
     */
    public function login()
    {
        $this->query .= " }";
        return $this->sendLogin();
    }

    /**
     * @autor Adrian Estrada
     * @param $var
     * @return $this
     */
    public function query($query): static
    {
        $this->query .= $query;
        return $this;
    }

    /**
     * @autor Adrian Estrada
     * @param $var
     * @return $this
     */
    public function function ($name, $fields, $filter = null): static
    {
        $this->query .= $name;
        if (!empty($filter)) {
            $this->query .= "($filter)";
        }
        $this->query .= "{ ";
        $this->query .= $fields;
        $this->query .= " }";
        return $this;
    }
}
