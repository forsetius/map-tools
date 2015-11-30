<?php
namespace pl\forseti\cli;

use pl\forseti\reuse\Help;
use pl\forseti\reuse\Collection;

/**
 * Command Line Arguments
 */
class CLA
{
    protected $args;
    
    public function __construct(array $args = array())
    {
        $this->args = new Collection('pl\forseti\cli\aArgument');
        
        # verbosity: 0 - quiet, 1 - warnings only, 2 - info (verbose), 3 - debug (detailed time and memory)
        $v = new Option('v', $GLOBALS['cfg']->defVerbosity);
        $v->setValid(['class'=>'uint', 'max'=>3])->setAlias('verbose');
        $v->setHelp('level',<<<EOH
Verbosity level:
 0. Quiet: won't issue any messages. Exit status is still available
 1. Severe (default): only errors and warning messages will be issued
 2. Notices: progress reporting messages will be printed
 3. Benchmark: also timings and memory readings will be available
EOH
        );
        
        $version = new Flag('version');
        $version->setHelp('',"Print this script suite's version");
        
        $help = new Flag('help');
        $help->setHelp('',"Print this help");
        
        $test = new Option('test', $GLOBALS['cfg']->defTestMode);
    	$test->setHelp('', <<<EOH
Enter developer mode suitable for batch-testing. Options:
--test
      see `all`
--test dry
      dry test, only print checks to be executed with their expected exit codes
--test errors
      do the checks and print exit codes only in case of error
--test all
      do the checks and print exit codes (default)
EOH
    	);

        $this->addArgs(array_merge([$v, $version, $help, $test], $args));
    }

    public function addArg(aArgument $arg)
    {
        $name = $arg->getName();
        $this->args[$name] = $arg;

        return $this;
    }

    public function addArgs(array $args)
    {
        foreach ($args as $arg) {
            $this->addArg($arg);
        }

        return $this;
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
        $allowedArgs = array();
        foreach ($this->args as $argName=>&$arg) {
            $allowedArgs[$argName] = $arg;
            
            foreach ((array) $arg->getAlias() as $alias) {
                $allowedArgs[$alias] = $arg;
            }
        }
        
        $i = 1;
        $max = count($GLOBALS['argv'])-1;
        while ($i <= $max) {
            if ($GLOBALS['argv'][$i][0] == '-') {
                $argName = \str_replace('-', '', $GLOBALS['argv'][$i]);
                if (\strlen($argName)==0 ||
                    ($GLOBALS['argv'][$i][1] != '-' && \strlen($argName)!=1) ||
                    ($GLOBALS['argv'][$i][1] == '-' && \strlen($argName)==1)
                   ) throw new SyntaxException("Wrong syntax of `$argName` argument", SyntaxException::BAD_SYNTAX);
                if (\array_key_exists($argName, $allowedArgs)) {
                    if (($i < $max) && empty($GLOBALS['argv'][$i+1])) {
                        throw new SyntaxException("Empty values are not allowed", SyntaxException::BAD_SYNTAX);
                    } elseif (($i == $max) || empty($GLOBALS['argv'][$i+1]) || $GLOBALS['argv'][$i+1]{0} == '-') {
                        $allowedArgs[$argName]->setValue(true);
                        $i++;
                    } else {
                        $allowedArgs[$argName]->setValue($GLOBALS['argv'][$i+1]);
                        $i+=2;
                    }
                } else throw new SyntaxException("Unexpected argument `$argName`", SyntaxException::UNEXPECTED_ARGUMENT);
            } else throw new SyntaxException("Bad syntax", SyntaxException::BAD_SYNTAX);
        }
        
        return $this;
    }

    public function __get($name)
    {
    	return $this->args[$name]->getValue();
    }
    
    public function __set($name, $val)
    {
        $this->args[$name]->setValue($val);
        return $this;
    }
    
    public function postproc()
    {
    	if ($this->help) Help::printTerm($GLOBALS['argv'][0]);
    	if ($this->version) {
    	    echo \pathinfo($GLOBALS['argv'][0], PATHINFO_FILENAME) . ' from map-tools ' . $GLOBALS['cfg']->appVersion . "\n";
    	    exit(0);
    	}
    	if ($this->test !== false) {
    	    $test = new Test();
    	    $test->addTasks(require \dirname($_SERVER['PHP_SELF']) . '/test/common-test.php');
    	    $test->addTasks(require \dirname($_SERVER['PHP_SELF']) . '/test/image-tools-test.php');
//    	    $test->addTasks(require \dirname($_SERVER['PHP_SELF']) . '/test/'. \pathinfo($_SERVER['PHP_SELF'],PATHINFO_FILENAME) . '-test.php');
    	    
    	    if ($this->test === 'dry') {
    	        $test->dryRun();
    	    } else {
    	        $test->run()->output($this->test === 'errors');
    	    }
    	    exit(0);
    	}
    	return array();
    }
    
    /*
     * TODO reporting capability
     */
    
}

 ?>
