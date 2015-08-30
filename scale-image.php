#!/usr/bin/php5
<?php
namespace pl\forseti\maptools;
use \pl\forseti\cli\Option;

$cla = new ImageCLA();
$cla->addOption(new Option('w', function() use (&$srcImg) {
                	$w = 2;
                	while ($w*2 <= $srcImg->getWidth()) $w *= 2;
        			return $w;
        		}));
$cla->addOption(new Option('h', function() use (&$cla) {
                	return $cla->w /2;
				}));
				
$cla->parse();
extract($cla->postproc());


if ($cla->v) echo "Scaling to {$cla->w} x {$cla->h}\n";
$srcImg->scale($cla->w, $cla->h);

if ($cla->v) echo "Writing\n";
$srcImg->write($cla->o);
$srcImg->destroy();
if ($cla->v) echo "Done\n";

?>
