#!/usr/bin/env php
<?php
$base = realpath(dirname(__FILE__)) .'/';
require_once $base . 'lib/autoload.php';
use pl\forseti\reuse\Config;

require_once $base . 'test/' . $argv[1] . '-test.php';
$cfg = new Config($base.'lib/config.php');

$result = array();
$i = 0;

foreach ($tasks as $task) {
    $gfxLibs = (strpos($task[0], '#g#') !== false) ? $cfg->capGfxLibs : array(1);
    foreach ($gfxLibs as $lib) {
        $t = $task[0];
        $t = str_replace('#g#', $lib, $t);
        $t = str_replace('#i#', ++$i, $t);
        $status = 0;
        echo "\e[93m". $argv[1] .'.php '. $t . "\e[0m\n";
        system($argv[1] .'.php '. $t, $status);
        
        $outcome = (($status == $task[1]) ? "\e[42m  OK" : "\e[41m NOK") . "\e[0m";
        $targetResult = (($task[1] == 0) ? "\e[42m" : "\e[41m") . "%03d\e[0m";
        $actualResult = (($status == 0) ? "\e[42m" : "\e[41m") . "%03d\e[0m";
        $result[] = sprintf(" %3d. $outcome: $actualResult/$targetResult > %s", $i, $status, $task[1], $t);
        error_log($i .';'. $status . ';'. $t . "\n", 3, $argv[1] . '.log');
    }
}
    
foreach ($result as $row) {
    echo $row ."\n";
}
?>
