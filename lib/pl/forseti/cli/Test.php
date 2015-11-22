<?php
namespace pl\forseti\cli;

use pl\forseti\reuse\Collection;

class Test
{
    protected $tasks;
    protected $checks;
    
    public function __construct(Collection $tasks = null)
    {
        $this->tasks = new Collection('\pl\forseti\cli\TestTask');
        if ($tasks != null)
            $this->addTasks($tasks);
    }
    
    public function addTasks($tasks)
    {
        foreach ((array) $tasks as $task) {
            $this->tasks[$task->getName()] = $task;
        }
        return $this;
    }
    
    public function dryRun()
    {
        $this->parseTasks();
        $scriptName = \pathinfo($_SERVER['PHP_SELF'], PATHINFO_FILENAME);
        foreach ($this->checks as $check=>$code) {
            $color = ($code == 0) ? '92' : '91'; 
            echo "      \e[1m\e[{$color}m$code\e[0m: $scriptName \e[1m$check\e[21m\n";
        }
        exit(0);
    }
    
    public function run()
    {
        $this->parseTasks();
        $scriptName = \pathinfo($_SERVER['PHP_SELF'], PATHINFO_FILENAME);
        $outcome = 0;
        $i = 0;
        
        \ob_start();
        foreach ($this->checks as $check=>&$code) {
            $i++;
            $code = [$code, null];
            echo "CHECK $i > $scriptName.php $check\n";
            \system("$scriptName.php $check", $code[1]);
            echo "\n\n";
        }
        \file_put_contents("$scriptName-output.log", \ob_get_contents());
        \ob_end_clean();
    }
    
    protected function parseTasks()
    {
        $jobs = function ($subjects, $search, $replaces) {
            $result = array();
            $replaces = (array) $replaces;
            foreach ((array) $subjects as $case=>$cCode) 
                foreach ($replaces as $val=>$vCode) 
                    $result[\str_replace("@$search@", $val, $case)] = ($cCode == 0) ? $vCode : $cCode;

            return $result;
        };
        $input = array();
        $checks = array();
        foreach ($this->tasks as $task) {
            $input['defaults']['#'. $task->getName() .'#'] = $task->getDefault();
            $input['vars'][$task->getName()] = $task->getVars();
        }
        
        // for each task (command-line argument to test), like: '--verbose'
        foreach ($this->tasks as $task) {
            
            // for each test case for CLA tested, like: '-s #s# -v @v@'
            foreach ($task->getCases() as $case=>$cCode) {
                $case = \str_replace(\array_keys($input['defaults']), $input['defaults'], $case);
                
                $vars = array();
                \preg_match_all('/@([\w]+)@/', $case, $vars);
                $vars = (array) $vars[1];
                
                $toTest = [$case=>$cCode];
                // for each @var@ in test case
                foreach ($vars as $var) 
                    $toTest = $jobs($toTest, $var, $input['vars'][$var]);

                $checks = array_merge($checks, $toTest);
            }
        }
        $this->checks = $checks;
    }
    
    public function output($onlyErrors = true)
    {
        $i = 0;
        foreach ($this->checks as $check=>list($targetResult, $actualResult)) {
            $i++;
            if (! ($onlyErrors && ($actualResult == $targetResult))) {
                $outcome = (($actualResult == $targetResult) ? "\e[42m  OK" : "\e[41m NOK") . "\e[0m";
                $targetFormat = (($targetResult == 0) ? "\e[42m" : "\e[41m") . "%03d\e[0m";
                $actualFormat = (($status == 0) ? "\e[42m" : "\e[41m") . "%03d\e[0m";
                \printf(" %4d. $outcome: $actualFormat/$targetFormat > %s", $i, $actualResult, $targetResult, $check);
            }
        }
        return $this;
    }
}