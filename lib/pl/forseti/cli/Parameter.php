<?php
namespace pl\forseti\cli;

class Parameter extends aArgument
{
    const REQ = '#REQ';
    
    public function __construct($name, $default = self::REQ)
    {
        parent::__construct($name, $default);
    }
    
    public function getNameV()
    {
        return $this->name . ':';
    }
    
    public function getValue()
    {
        if ($this->value == self::REQ) throw new SyntaxException('Required value for parameter "'. $this->name .'" not supplied');
        return $this->value;
    }
    
    public function isRequired()
    {
        return $this->value == self::REQ;
    }
}