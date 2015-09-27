<?php
namespace pl\forseti\reuse;

class aException extends \Exception
{
    public function getName()
    {
        return \array_search($this->getCode(), (new \ReflectionClass($this))->getConstants());
    }
}