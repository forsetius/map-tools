#!/usr/bin/env php
<?php
require_once __DIR__.'/src/autoload.php';

use forsetius\reuse\Config;
use forsetius\reuse\Benchmark;
use forsetius\maptools\Command as cmd;

$conf = new Config(__DIR__.'/config.php');
$bm = Benchmark::getInstance();

$fallback = 'forsetius\maptools\Command\Help';
$command = 'forsetius\maptools\Command\\' . ((\count($argv) > 1) ? 'Map\\'. ucfirst($argv[1]) : $fallback);
try {
    $cmd = new $command($conf);
} catch (\Exception $e) {
    $cmd = new $fallback();
}
$cla = $cmd->getCLA();

$bm->setEcho($cla->v);
if ($cla->v == 3) $bm->recTime('After parsing CLI');

$cmd->execute();

$bm->rec('Done');
exit(0);
