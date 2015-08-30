#!/usr/bin/php5
<?php
namespace pl\forseti\maptools;
use \pl\forseti\cli\Option;

$options = array(
                new Option('w', false, Option::NO),
                new Option('l', 0),
                new Option('t', 0),
                new Option('r', 0),
                new Option('b', 0),
                );
$cla = new ImageCLA($options);
$cla->parse();
extract($cla->postproc());

if ($cla->v) echo "Clipping\n";
$srcImg->clip($cla->l, $cla->t, $cla->r, $cla->b);

if ($cla->v) echo "Writinging\n";
$srcImg->write($cla->o);
$srcImg->destroy();
if ($cla->v) echo "Done\n";

 ?>
