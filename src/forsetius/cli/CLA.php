<?php
namespace forsetius\cli;

use forsetius\reuse\Help;
use forsetius\reuse\Collection;
use forsetius\reuse\LogicException;
use forsetius\reuse\GlobalPool as Pool;

/**
 * Command Line Arguments
 */
class CLA
{
    protected $args;

    public function __construct(array $args = array())
    {
        $this->args = new Collection('forsetius\cli\aArgument');

        $v = new Option('v', Pool::getConf()->defVerbosity);
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

        $version = new Flag('version');
        $version->setHelp('',"Print this script suite's version");

        $help = new Flag('help');
        $help->setHelp('',"Print this help");

        $test = new Option('test', Pool::getConf()->defTestMode);
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
            // Disallow arguments like '---any', '--s' and '-long'
            $argName = $this->getArgumentName($GLOBALS['argv'][$i]);
            if ($argName !== false) {

                // Allow only previously defined arguments
                if (\array_key_exists($argName, $allowedArgs)) {
                    // Disallow arguments with empty values like '-s ""'
                    if (($i < $max) && $GLOBALS['argv'][$i+1]=='') {
                        throw new SyntaxException("Empty values are not allowed", SyntaxException::BAD_SYNTAX);
                    }
                    // if argument without value then set it to true. Classes derived from aArgument will decide if it's correct later
                    elseif ($i == $max || ($this->getArgumentName($GLOBALS['argv'][$i+1]) !== false)) {
                        $allowedArgs[$argName]->setValue(true);
                        $i++;
                    }
                    // if argument with value then assign it the value
                    else {
                        $allowedArgs[$argName]->setValue($GLOBALS['argv'][$i+1]);
                        $i+=2;
                    }
                } else throw new SyntaxException("Unexpected argument `$argName`", SyntaxException::UNEXPECTED_ARGUMENT);
            } else throw new SyntaxException("Wrong syntax of `{$GLOBALS['argv'][$i][0]}` argument", SyntaxException::BAD_SYNTAX);
        }

        return $this;
    }

    protected function getArgumentName($str)
    {
        $name = \preg_replace('/(?:^-([\w\d])$)|(?:^--([\w\d][\w\d_?!.:-]+)$)/', '$1$2', $str,1);
        if (\is_null($name)) {
            throw new LogicException("Error in regexp with `$str` string");
        }
        return ($str == $name) ? false : $name;
    }

    public function __get($name)
    {
    	// TODO exception
    	return $this->args[$name]->getValue();
    }

    public function __set($name, $val)
    {
        $this->args[$name]->setValue($val);
        return $this;
    }

    public function postproc()
    {
    	if ($this->v) {
    		$log->setLogLevelThreshold(constant('Psr\Log\LogLevel::'. \strtoupper($this->v)));
    	}
    	if ($this->help) Help::printTerm($GLOBALS['argv'][0]);
    	if ($this->version) {
    	    echo \pathinfo($GLOBALS['argv'][0], PATHINFO_FILENAME) . ' from map-tools ' . Pool::getConf()->appVersion . "\n";
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

}

 ?>
