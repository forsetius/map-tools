#!/usr/bin/env php
<?php
require_once __DIR__.'/lib/autoload.php';

use pl\forseti\reuse\Config;
use pl\forseti\reuse\Benchmark;
use pl\forseti\maptools\Command as cmd;

$conf = new Config(__DIR__.'/config.php');
$bm = Benchmark::getInstance();

$command = ucfirst($argv[1]);
try {
    $cmd = new cmd/Map/$command($conf);
} catch (\Exception $e) {
    $cmd = new cmd/Help();
}
$cla = $cmd->getCLA();

$bm->setEcho($cla->v);
if ($cla->v == 3) $bm->recTime('After parsing CLI');

$cmd->execute();

$bm->rec('Done');
exit(0);
