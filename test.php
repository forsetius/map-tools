#!/usr/bin/env php
<?php
$base = realpath(dirname(__FILE__)) .'/';
require_once $base . 'test/' . $argv[1] . '-test.php';
require_once $base . 'lib/autoload.php';
use pl\forseti\reuse\Config;

$cfg = new Config(realpath(dirname(__FILE__)).'/lib/config.php');
$libs  = $cfg->capGfxLibs;

$result = array();
$i = 0;
foreach ($libs as $lib) {
    foreach ($tasks as $task) {
        $task = str_replace('#g#', $lib, $task);
        $task = str_replace('#i#', ++$i, $task);
        $status = 0;
        echo "\e[93m". $argv[1] .'.php '. $task . "\e[0m\n";
        system($argv[1] .'.php '. $task, $status);
        $result[] = sprintf("%3d: ". (($status == 0) ? "\e[42m" : "\e[41m") ."%03d\e[0m > %s\n", $i, $status, $task);
        error_log($i .';'. $status . ';'. $task. "\n", 3, $argv[1] . '.log');
    }
}
//system('clear');
foreach ($result as $row) {
    echo $row ."\n";
}
?>
