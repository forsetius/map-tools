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
        # verbosity: 0 - quiet, 1 - warnings only, 2 - info (verbose), 3 - debug (detailed time and memory)
        $this->addArg(new Option('v', $GLOBALS['cfg']->defVerbosity));
        $this->addArg(new Binary('help'));// print help and exit
        foreach ($args as $arg) {
            $this->addArg($arg);
        }
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
     * @throws \SyntaxException
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
        	throw new SyntaxException();
       
		foreach ($cla as $name => $value) {
			$this->args[$name]->setValue($value);
		}
    }

    public function __get($name)
    {
    	return $this->args[$name]->getValue();
    }
    
    public function postproc() {
    	if ($this->help) Help::printTerm();
    	
    	return array();
    }
}

 ?>
