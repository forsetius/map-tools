<?php
namespace pl\forseti\cli;

use pl\forseti\reuse\LogicException;
/**
 * Command-line argument.
 * Every argument must have a value - either specified by the user
 * when invoking the script or default one. There are three types of
 * arguments:
 * - Flag - a switch used to turn option on or off. Default: false (off)
 * - Parameter - argument intended to provide value. Must have default used
 * if user omits it. If user specifies it, they must provide value.
 * - Option - the cross of above two. It's a switch that can enable some
 * feature and user can optionally provide value. Default: false (off) and
 * also another default must be provided in case if user specifies argument
 * without value
 *
 * @author forseti
 *
 */
abstract class aArgument
{
    protected $name;
    protected $alias;
    protected $value;
    protected $valid = array();
    protected $help;
    
    static protected $classes = array(
        'filepath' => '~^([a-zA-Z]:)?[\w \-\.()\\/]+$~',
        'uint'     => '/^\d*$/',
    );

    /**
     * Constructor.
     * Defines new argument
     * @param string $name One-letter names are preferred. After parsing they will be referred like $claObject->v
     * @param string|integer|boolean $default Default value - used if user haven't specified the argument when calling the script
     */
    public function __construct($name, $default)
    {
        $this->name = $name;
        $this->value = $default;
    }

    public function setValid(array $valid)
    {
        $this->valid = $valid;
        return $this;
    }
    
    /**
     * name
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    
    public function getAlias()
    {
        return $this->alias;
    }
    
    public function setAlias($alias)
    {
        $this->alias = (array) $alias;
        return $this;
    }
    
    public function getValue()
    {
        return $this->value;
    }
    
    public function setValue($val)
    {
        $this->validate($val);
    	$this->value = $val;
    	return $this;
    }
    
    public function getHelp()
    {
        $aliases = '';
        foreach ($this->alias as $alias) {
            $aliases .= " <b>{$this->getArgName($alias)}</b>";
        }
        
        $paramName = (empty($this->help['param']) ? '' : "<u>&lt;{$this->help['param']}&gt;</u>");
        return \sprintf("        <b>%s</b>%s %s\n%s", $this->getArgName($this->name), $aliases, $paramName, $this->help['desc']);
    }
    
    protected function getArgName($argName)
    {
        return (\strlen($argName) == 1) ? "-$argName" : "--$argName";
    }
    
    public function setHelp($paramName, $help)
    {
        $this->help = ['param'=>$paramName, 'desc'=>$help];
        return $this;
    }

    protected function validate($val) {
        if (!empty($this->valid)) {
            if (\array_key_exists('class', $this->valid)) {
                if (\array_key_exists($this->valid['class'], static::$classes)) {
                    $this->valid['re'] = static::$classes[$this->valid['class']];
                } else throw new LogicException("Bad validation class `{$this->valid['class']}`", LogicException::FAULTY_LOGIC);
            }
            if (\array_key_exists('re', $this->valid)) {
                $result = \preg_match($this->valid['re'], $val);
                if ($result === false)
                    throw new LogicException("Error in regular expression: `{$this->valid}'");
                if ($result === 0)
                    throw new SyntaxException("Incorrect value of parameter `{$this->name}`", SyntaxException::INVALID_VALUE);
            }
            if (\array_key_exists('min', $this->valid)) {
                if ($val < $this->valid['min'])
                    throw new SyntaxException("Value of parameter `{$this->name} is too low. Minimal value is: ". $this->valid['min'], SyntaxException::VALUE_OUT_OF_BOUNDS);
            }
            if (\array_key_exists('max', $this->valid)) {
                if ($val > $this->valid['max'])
                    throw new SyntaxException("Value of parameter `{$this->name} is too high. Maximal value is: ". $this->valid['max'], SyntaxException::VALUE_OUT_OF_BOUNDS);
            }
            if (\array_key_exists('set', $this->valid)) {
                if (! \in_array($this->value, $this->valid['set']))
                    throw new SyntaxException("Value of parameter `{$this->name} must be one of: ". \implode(', ', $this->valid['set']), SyntaxException::VALUE_OUT_OF_BOUNDS);
            }
        }
        
    }
}

 ?>
