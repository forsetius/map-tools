<?php
namespace forsetius\cli;

use forsetius\cli\Help;
use forsetius\reuse\Collection;
use forsetius\reuse\LogicException;
use forsetius\reuse\GlobalPool as Pool;

/**
 * Command Line Arguments
 */
class CLA
{
    protected $namedArgs;
    protected $posArgs;

    public function __construct(array $namedArgs = [], $posArgs = [])
    {
        $this->namedArgs = new Collection('forsetius\cli\Argument\AbstractNamedArgument');
        $this->posArgs = new Collection('forsetius\cli\Argument\PositionalParameter');
        
        $v = new Argument\Option('v', Pool::getConf()->get("default:verbosity") + 1);
        $v->setValid(['class'=>'uint', 'max'=>3])->setAlias('verbose');
        $v->setHelp('level',<<<EOH
Verbosity level:
 0. Quiet    - won't issue any messages. Exit status will be available
 1. Alert    - not used
 2. Critical - not used
 3. Errors   - crash reasons
 4. Warnings - information on important assumptions taken on non-conformant images
 5. Notices  - important info on operations with successful outcome
 6. Info     - progress reports
 7. Debug    - timings and memory benchmarks will be available
EOH
        );

        $version = new Argument\Flag('version');
        $version->setHelp('',"Print this script suite's version");

        $help = new Argument\Flag('help');
        $help->setHelp('',"Print this help");

        $test = new Argument\Option('test', Pool::getConf()->get("default:testMode"));
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

        $this->addArgs(array_merge([$v, $version, $help, $test], $namedArgs));
    }

    public function addArg(Argument\AbstractArgument $arg)
    {
        $this->namedArgs[$arg->getName()] = $arg;

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
     * Note: switch-type arguments that do not accept values (are either set or not set, like -v or --verbose)
     * are set to 'arg'=>true if specified as command line argument to the script.
     * They should have 'false' as default value, meaning they were not specified in command line.
     * This is opposite of getopt behavior but is more logical.
     * @return void Parsed arguments are set into $this->opt and are accessible by $claObject->argName (using __get())
     * @throws \SyntaxException
     *
     */
    public function parse()
    {
    	//
    	// positional arguments processed first
    	//
    	$cla = $GLOBALS['argv'];
    	
    	// we could use 'for' loop to iterate like this:
    	// $this->posArgs[$i]->setValue($args[$i]);
    	// but this forces continuous indices in $this->posArgs
    	// or testing for non-existing indices and skipping it - inefficient
    	$this->posArgs->ksort();
    	foreach ($this->posArgs as $posArg) {
    		if (\count($cla) < 1 || $cla[$i][0] == '-') break;
    		$cla = $posArg->parse($cla);
    	}
    	
    	//
    	// processing named arguments
    	//
    	$allowedArgs = array();
    	foreach ($this->namedArgs as $argName=>&$arg) {
    		$names = $arg->getAlias();
    		array_unshift($names, $arg->getName());
    		$allowedArgs = array_merge($allowedArgs, array_fill_keys($names, $arg));
    	}
    	
    	while (\count($cla) > 0) {
    		$arg = array_shift($cla);
    		// Disallow arguments like '---any', '--s' and '-long'
    		$argName = $this->getArgumentName($arg);

    		if ($argName !== false) {
    			// Allow only previously defined arguments
    			if (\array_key_exists($argName, $allowedArgs)) {
					$cla = $allowedArgs[$argName]->parse($args);
    			} else throw new SyntaxException("Unexpected argument `$argName`", SyntaxException::UNEXPECTED_ARGUMENT);
    		} else throw new SyntaxException("Wrong syntax of `$arg` argument", SyntaxException::BAD_SYNTAX);
    	}
    	
    	$this->postproc();
    	return $this;
    }
    
    public function postproc()
    {
    	if ($this->v) {
    		$log->setLogLevelThreshold(constant('Psr\Log\LogLevel::'. \strtoupper($this->v)));
    	}
    	
    	if ($this->help)
    		throw new SyntaxException('Help requested');
    	
    	if ($this->version) {
    	    echo 'map-tools ' . Pool::getConf()->get("app:version") . "\n";
    	    exit(0);
    	}
    	
    	if ($this->test !== false) {
    	    $test = new Test();
    	    $test->addTasks(require \dirname($_SERVER['PHP_SELF']) . '/test/common-test.php');
    	    $test->addTasks(require \dirname($_SERVER['PHP_SELF']) . '/test/image-tools-test.php');
    	    $test->addTasks(require \dirname($_SERVER['PHP_SELF']) . '/test/'. \pathinfo($_SERVER['PHP_SELF'],PATHINFO_FILENAME) . '-test.php');

    	    if ($this->test === 'dry') {
    	        $test->dryRun();
    	    } else {
    	        $test->run()->output($this->test === 'errors');
    	    }
    	    exit(0);
    	}
    	return array();
    }

    /**
     * Validate the named argument name and if it's valid
     * return the name stripped of '-'
     *
     * @param string $str
     * @throws LogicException if error in processing regex
     * @return boolean|mixed name stripped of leading '-' or false in invalid argument name
     * 		(for example: -long or --s or ---any)
     */
    protected function getArgumentName($str)
    {
    	$name = \preg_replace('/(?:^-([\w\d])$)|(?:^--([\w\d][\w\d_?!.:-]+)$)/', '$1$2', $str,1);
    	if (\is_null($name)) {
    		throw new LogicException("Error in regexp with `$str` string");
    	}
    	return ($str == $name) ? false : $name;
    }
    
    public function hasArgument($name)
    {
    	return (key_exists($name, $this->namedArgs));
    }
    
    public function __get($name)
    {
    	// TODO exception
    	return $this->namedArgs[$name]->getValue();
    }
    
    public function __set($name, $val)
    {
    	$this->namedArgs[$name]->setValue($val);
    	return $this;
    }
}

 ?>
