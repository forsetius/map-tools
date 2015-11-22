<?php
use pl\forseti\cli\TestTask;

$help = new TestTask('help');
$help->setVarsNok(['incorrect'=>255]);
$help->setCases(['--help'=>0, '-help'=>255, '--help @help@'=>255, '-s #s# --help'=>0]);

$v = new TestTask('v');
$v->setVarsOk(['','3','0']);
$v->setVarsNok(['-1'=>255, 'error'=>255, '5'=>255, '2-'=>255, '+'=>255, 'true'=>255]);
$v->setCases(['-s #s# -v @v@'=>0, '-s #s# --version @v@'=>0, '-s #s# --v #v#'=>255, '-s #s# -version #v#'=>255]);

$dev = new TestTask('dev');
$dev->setVarsNok(['incorrect'=>255]);
$dev->setCases(['--dev'=>0, '-dev'=>255, '--dev @dev@'=>255]);

$version = new TestTask('version');
$version->setVarsNok(['incorrect'=>255]);
$version->setCases(['--version'=>0, '-version'=>255, '--version @version@'=>255, '-s #s# --version'=>0]);

return [$help, $v, $dev, $version];
?>