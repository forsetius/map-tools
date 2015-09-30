#!/usr/bin/env php
<?php
$base = realpath(dirname(__FILE__)) .'/';
require_once $base . 'test/' . $argv[1] . '-test.php';
require_once $base . 'lib/autoload.php';
use pl\forseti\reuse\Config;

$cfg = new Config($base.'lib/config.php');
$libs  = $cfg->capGfxLibs;

$result = array();
$i = 0;
//system('cd test;dir');
//system('dir');exit;
foreach ($libs as $lib) {
    foreach ($tasks as $task) {
        $task[0] = str_replace('#g#', $lib, $task[0]);
        $task[0] = str_replace('#i#', ++$i, $task[0]);
        $status = 0;
        echo "\e[93m". $argv[1] .'.php '. $task[0] . "\e[0m\n";
        system($argv[1] .'.php '. $task[0], $status);
        $result[] = sprintf(" %3d: ". (($status == $task[1]) ? "\e[42m  OK" : "\e[41m NOK") . "\e[0m | " . (($status == 0) ? "\e[42m" : "\e[41m") ."%03d\e[0m > %s\n", $i, $status, $task[0]);
        error_log($i .';'. $status . ';'. $task[0] . "\n", 3, $argv[1] . '.log');
    }
}
//system('clear');
foreach ($result as $row) {
    echo $row ."\n";
}
?>
