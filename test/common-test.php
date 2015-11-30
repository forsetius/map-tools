<?php
use pl\forseti\cli\TestTask;
use pl\forseti\cli\SyntaxException as SEx;

$help = new TestTask('help');
$help->setVarsNok([['incorrect',SEx::VALUE_NOT_ALLOWED]]);
$help->setCases([['--help',0], ['-help',SEx::BAD_SYNTAX], ['--help @help@',SEx::INVALID_VALUE], ['-s #s# --help',0]]);

$v = new TestTask('v');
$v->setVarsOk(['','3','0']);
$v->setVarsNok([['-1',SEx::INVALID_VALUE], ['error',SEx::INVALID_VALUE], ['2-',SEx::INVALID_VALUE], ['+',SEx::INVALID_VALUE], ['true',SEx::INVALID_VALUE]]);
$v->setCases([['-s #s# -v @v@',0], ['-s #s# --verbose @v@',0], ['-s #s# --v #v#',SEx::BAD_SYNTAX], ['-s #s# -verbose #v#',SEx::BAD_SYNTAX]]);

$version = new TestTask('version');
$version->setVarsNok([['incorrect',SEx::INVALID_VALUE]]);
$version->setCases([['--version',0], ['-version',SEx::BAD_SYNTAX] , ['--version @version@',SEx::INVALID_VALUE], ['-s #s# --version',0]]);

return [$help, $v, $version];
?>