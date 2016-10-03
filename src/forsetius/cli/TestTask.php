<?php
namespace forsetius\cli;
use forsetius\reuse\iNamed;

class TestTask implements iNamed
{
    protected $name;
    protected $cases = array();
    protected $vars = array();
    protected $default = null;
    
    public function __construct($name)
    {
        $this->name = $name;
    }
    
    public function setCases(array $cases)
    {
        $this->cases = $cases;
        return $this;
    }
    
    public function setVarsOk(array $varsOk)
    {
        foreach ($varsOk as $arg) {
            $this->vars[] = [$arg,0];
        }
        $this->default =& $varsOk[0];
        return $this;
    }
    
    public function setVarsNok(array $varsNok)
    {
        $this->vars = \array_merge($this->vars, $varsNok);
        return $this;
    }
    
    public function getName()
    {
        return $this->name;
    }
    
    public function getDefault()
    {
        return $this->default;
    }
    
    public function getVars()
    {
        return $this->vars;
    }
    
    public function getCases()
    {
        return $this->cases;
    }
 
    
}