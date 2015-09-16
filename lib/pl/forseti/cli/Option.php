<?php
namespace pl\forseti\cli;

/**
 * Command-line switch that can enable some feature and also
 * user can optionally provide value.
 * Default: false (off) and another default must be provided
 * in case if user specifies argument but omits the value.
 * @author forseti
 *
 */
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