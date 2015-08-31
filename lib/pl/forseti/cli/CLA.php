<?php
namespace pl\forseti\cli;

/**
 * Command Line Arguments
 */
class CLA
{
    protected $args = array();

    public function __construct(array $args = array())
    {
        $this->addArg(new Binary('v'));  	// verbose?
        $this->addArg(new Binary('help'));// print help and exit
        $this->args = $args + $this->args;
    }

    public function addArg(aArgument $a)
    {
        $this->args[$a->getName()] = $a;
    }

    /**
     * Parse the command line arguments supplied to the CLI script.
     * Note: switch-type arguments that do no accept values (are either set or not set, like -v or --verbose)
     * are set to 'arg'=>true if specified as command line argument to the script.
     * They should have 'false' as default value, meaning they were not specified in command line.
     * This is opposite of getopt behavior but is more logical.
     * @return void Parsed arguments are set into $this->opt and are accessible by $claObject->argName (using __get())
     * @throws \Exception
     *
     */
    public function parse()
    {
        $sOpt = ''; $lOpt = array();
        foreach ($this->args as $key => $arg) {
            if (\strlen($key)>1) {
                $lOpt[] = $arg->getNameV();
            } else {
                $sOpt .= $arg->getNameV();
            }
        }

        $cla = \getopt($sOpt, $lOpt);
        
        if ($cla === false)
        	throw new \Exception('Invalid syntax. See: `'. \basename($GLOBALS['argv'][0]) .' -- help` for more information');
       
		foreach ($cla as $name => $value) {
			$this->args[$name]->setValue($value);
		}
    }

    public function __get($name)
    {
    	return $this->args[$name]->getValue();
    }
    
    public function postproc() {
    	if ($this->help) Help::printTerm($GLOBALS['argv'][0]);
    	
    	return array();
    }
}

 ?>
