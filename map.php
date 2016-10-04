#!/usr/bin/env php
<?php
require_once __DIR__.'/src/autoload.php';
use forsetius\reuse\GlobalPool as Pool;
use forsetius\maptools\Command as cmd;

Pool::setConf($conf = new forsetius\reuse\Config(__DIR__.'/config.php'));
Pool::setLog($log = new Katzgrau\KLogger\Logger(__DIR__.'/logs', constant('Psr\Log\LogLevel::'. $conf->defVerbosity)));

$fallback = 'forsetius\maptools\Command\Help';
$command = 'forsetius\maptools\Command\\' . ((\count($argv) > 1) ? 'Map\\'. ucfirst($argv[1]) : $fallback);
try {
    $cmd = new $command($conf);
} catch (\Exception $e) {
    $cmd = new $fallback();
}
Pool::setCla($cmd->getCLA());

$cmd->execute();

$log->info('Done');
exit(0);
