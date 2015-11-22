<?php
namespace pl\forseti\reuse;

class aException extends \Exception
{
    const WARNING = 1000;
    
    protected $warning = false;
    
    /**
     * {@inheritDoc}
     * @see Exception::__construct()
     */
    public function __construct($message = null, $code = null, $previous = null)
    {
        if ($code > aException::WARNING) {
            $this->warning = true;
            $code = $code - aException::WARNING;
        }
        
        parent::__construct($message, $code, $previous);
    }
    
    public function isWarning()
    {
        return $this->warning;
    }
    
    public function getName()
    {
        return \array_search($this->getCode(), (new \ReflectionClass($this))->getConstants());
    }
    


}