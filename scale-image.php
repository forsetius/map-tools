#!/usr/bin/env php
<?php
namespace pl\forseti\maptools;
require_once realpath(dirname(__FILE__)).'/lib/autoload.php';

use \pl\forseti\cli\Option;

$cla = new ImageCLA();
$cla->addArg(new Option('w', function() use (&$srcImg) {
               	$w = 2;
               	while ($w*2 <= $srcImg->getWidth()) $w *= 2;
        		return $w;
        	}));
$cla->addArg(new Option('h', function() use (&$cla) {
               	return $cla->w /2;
			}));
				
$cla->parse();
extract($cla->postproc());

if ($cla->v) echo "Loading image\n";
$srcImg = aImage::make($cla->s);
$w = $srcImg->getWidth();
$h = $srcImg->getHeight();
if ($cla->v) echo "Loaded $w x $h image\n";

if ($cla->v) echo "Scaling to {$cla->w} x {$cla->h}\n";
$srcImg->scale($cla->w, $cla->h);

if ($cla->v) echo "Writing\n";
$srcImg->write($cla->o);
$srcImg->destroy();
if ($cla->v) echo "Done\n";
exit(0);

?>
