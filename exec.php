<?php
require_once __DIR__.'/src/autoload.php';

use forsetius\reuse\GlobalPool as Pool;
use forsetius\maptools\Command as cmd;
use forsetius\cli\SyntaxException;

Pool::setModule($module);
Pool::setConf($conf = new forsetius\reuse\Config(__DIR__.'/config.json'));
Pool::setLog($log = new Katzgrau\KLogger\Logger(__DIR__.'/logs', constant('Psr\Log\LogLevel::'. $conf->get("default:verbosity"))));

try {
	if ($argc == 1 || $conf->get("app:module:$module") == null || !in_array($argv[1], $conf->get("app:module:$module:command")))
		throw new SyntaxException('llll');
	
	$cmd = $conf->get("app:module:$module:ns") . ucfirst($argv[1]);
	$cmd = new $cmd();
} catch (\Exception $e) {
	$cmd = new forsetius\cli\Command\Core();
}
Pool::setCla($cmd->getCLA());

$cmd->execute();