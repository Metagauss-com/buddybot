<?php

namespace MetagaussOpenAI;

class MoRoot
{
    protected $config;

    protected function setConfig()
    {
        $this->config = \MetagaussOpenAI\Config::getInstance();
    }

    protected function setAll()
    {
        foreach ($this as $prop => $value) {
            $method = str_replace('-', '', $prop);
            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }
    }

    public function __construct()
    {
        $this->setAll();
    }
}