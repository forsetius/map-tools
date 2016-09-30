<?php
namespace pl\forseti\maptools;
require_once __DIR__.'/lib/autoload.php';

use pl\forseti\reuse\Benchmark;
use pl\forseti\reuse\LogicException;

throw new LogicException('', LogicException::NOT_IMPLEMENTED);
// TODO scheduled in v2.0

$bm = Benchmark::getInstance();

switch ($argv[1]) {
    case '--define' : require '/lib/define.php'; break;
    case '--remove' : require '/lib/remove.php'; break;
    case '--extract' : require '/lib/extract.php'; break;
    case '--merge' : require '/lib/merge.php'; break;
    default : require '/lib/help.php'; break;
}

?>