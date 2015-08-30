<?php
namespace pl\forseti\cli;

/**
 * Command Line Arguments
 */
class CLA
{
    protected $opts = array();

    public function __construct(array $options = array())
    {
        $this->addOption(new Option('v', false, Option::NO));  	// verbose?
        $this->addOption(new Option('help', false, Option::NO));// print help and exit
        $this->opts = $options + $this->opts;
    }

    public function addOption(Option $o)
    {
        $this->opts[$o->getName()] = $o;
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
        $sopt = ''; $lopt = array();
        foreach ($this->opts as $key => $option) {
            if (strlen($key)>1) {
                $lopt[] = $option->getNameV();
            } else {
                $sopt .= $option->getNameV();
            }
        }

        $cla = getopt($sopt, $lopt);
        
        if ($cla === false)
        	throw new \Exception('Invalid syntax. See: `'. basename($GLOBALS['argv'][0]) .' -- help` for more information');
       
		foreach ($cla as $name => $value) {
			if ($value === false) $value = true;
			$this->opts[$name]->setValue($value);
		}
    }

    public function __get($name)
    {
    	return $this->opts[$name]->getValue();
    }
    
    public function postproc() {
    	if ($this->help) Help::printTerm($GLOBALS['argv'][0]);
    	
    	return array();
    }
}

 ?>
