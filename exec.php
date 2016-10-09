<?php
require_once __DIR__.'/src/autoload.php';

use forsetius\reuse\GlobalPool as Pool;
use forsetius\maptools\Command as cmd;

Pool::setModule($module);
Pool::setConf($conf = new forsetius\reuse\Config(__DIR__.'/config.json'));
Pool::setLog($log = new Katzgrau\KLogger\Logger(__DIR__.'/logs', constant('Psr\Log\LogLevel::'. $conf->get("default:verbosity"))));

$command = $conf->get("app:module:$module:ns") .
			(($argc > 1 && $conf->get("app:module:$module") != null && in_array($argv[1], $conf->get("app:module:$module:command"))) ?
			ucfirst($argv[1]) : ucfirst($module));

var_dump(($argc > 1 && $conf->get("app:module:$module") != null && in_array($argv[1], $conf->get("app:module:$module:command"))));
try {
	$cmd = new $command();
} catch (LogicException $le) {
	throw new Exception("No such command: {$argv[1]}");
} catch (\Exception $e) {
	$cmd = new forsetius\cli\Command\Help();
	$cmd->outputTo('console');
	if ($argc > 1)
		$cmd->setTopic($argv[1]);
}
Pool::setCla($cmd->getCLA());

$cmd->execute();