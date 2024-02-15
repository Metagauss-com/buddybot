<?php

namespace MetagaussOpenAI\Traits;

trait Singleton
{
	protected $data;
	
    final public static function getInstance($data = null)
    {
        static $instances = array();
        $calledClass = get_called_class();

        if (!isset($instances[$calledClass]))
        {
            $instances[$calledClass] = new $calledClass($data);
        }

        return $instances[$calledClass];
    }
    
    final private function __clone()
    {
    }
    
    protected function __construct($data)
    {
    	$this->data = $data;
    }
}