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
    
    public function __construct($name, $valueAbsent, $optionAbsent = false)
    {
        parent::__construct( $name, $optionAbsent);
        $this->default = $valueAbsent;
    }
    
    public function setValue($val)
    {
        if (\is_bool($val)) {
            $this->value = $this->default;
        } else {
            $this->validate($val);
            $this->value = $val;
        }
    }
}