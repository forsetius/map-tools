<?php
namespace forsetius\cli;

use forsetius\reuse\LogicException;
use forsetius\reuse\iNamed;
use forsetius\reuse\FilesystemException;

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
abstract class aArgument implements iNamed
{
    protected $name;
    protected $alias;
    protected $value;
    protected $transform;
    protected $valid = array();
    protected $help;
    
    static protected $classes = array(
        'filepath' => '~^([a-zA-Z]:)?[\w \-\.()\\/]*[\w\-\.()]$~',
        'dirpath' => '~^([a-zA-Z]:)?[\w \-\.()\\/]*[\w\-\.()]$~',
        'uint'     => '/^[\d?]+$/',
        'alnum'    => '/[[:alnum:]_?]+/'
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
        $this->transform = null;
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
        if (! is_null($this->transform)) {
            $closure = $this->transform;
            $this->value = $closure($this->value);
            $this->validate($this->value);
        }
        
        return $this->value;
    }
    
    public function setValue($val)
    {
        if (is_null($this->transform))
            $this->validate($val);
        
    	$this->value = $val;
    	return $this;
    }

    public function setTransform(\Closure $c) {
        $this->transform = $c;
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
        if (\is_bool($val)) {
            throw new SyntaxException("Argument `{$this->name}` should have a value", SyntaxException::REQUIRED_VALUE);
        }
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
                    throw new SyntaxException("Value of parameter `{$this->name} is too low. Minimal value is: ". $this->valid['min'], SyntaxException::INVALID_VALUE);
            }
            if (\array_key_exists('max', $this->valid)) {
                if ($val > $this->valid['max'])
                    throw new SyntaxException("Value of parameter `{$this->name} is too high. Maximal value is: ". $this->valid['max'], SyntaxException::INVALID_VALUE);
            }
            if (\array_key_exists('set', $this->valid)) {
                if (! \in_array($this->value, $this->valid['set']))
                    throw new SyntaxException("Value of parameter `{$this->name} must be one of: ". \implode(', ', $this->valid['set']), SyntaxException::INVALID_VALUE);
            }
            
            if (\array_key_exists('class', $this->valid)) {
                switch ($this->valid['class']) {
                    case 'filepath' :
                        if (! \file_exists($val))
                            throw new FilesystemException("File `$val` not found", FilesystemException::FILE_NOT_FOUND);
                        break;
                    case 'dirpath' :
                        if (! \file_exists(\pathinfo($val,PATHINFO_DIRNAME)))
                            throw new FilesystemException('Directory `'.\pathinfo($val,PATHINFO_DIRNAME).'` not found', FilesystemException::FOLDER_NOT_FOUND);
                    default: ;
                }
            }
        }
        
    }
}

 ?>
