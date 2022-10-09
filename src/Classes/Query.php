<?php

namespace MrConnect\Classes;

class Query extends Base
{
    protected $users = null;

    public function __construct($url)
    {
        parent::__construct($url);

        $this->query = "query %s { ";
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
     * @param $name
     * @param $var
     * @return $this
     */
    public function name($name, $vars = []): static
    {
        $val = "";
        $int = 0;
        foreach ($vars as $key => $item) {
            $val .= $key . ":" . $item;
            if ($int != 0) {
                $val .= ",";
            }
            $int++;
        }

        $this->query = sprintf($this->query, $int > 0 ? ($name . " (" . $val . ") ") : "");
        return $this;
    }

    /**
     * @autor Adrian Estrada
     * @return array[]|mixed|void|null
     */
    public function send()
    {
        $this->query .= " }";
        $this->query = sprintf($this->query, "");
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
