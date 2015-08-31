<?php
namespace pl\forseti\cli;

class Option extends aArgument
{

    private $default;
    
    public function __construct($name, $default)
    {
        parent::__construct( $name, false);
        $this->default = $default;
    }
    
    public function getNameV()
    {
        return $this->name . '::';
    }
    
    public function setValue($val)
    {
        $this->value = ($val === false) ? $this->default : $val;
    }
    
    public function isRequired()
    {
        return false;
    }
}