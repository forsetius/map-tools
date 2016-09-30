#!/usr/bin/env php
<?php
namespace pl\forseti\maptools;
require_once __DIR__.'/lib/autoload.php';

use \pl\forseti\cli\Option;
use pl\forseti\reuse\Config;
use pl\forseti\reuse\Benchmark;
use pl\forseti\cli\Parameter;

$cfg = new Config(__DIR__.'/lib/config.php');
$bm = Benchmark::getInstance();

function setupCLA()
{
    $w = new Parameter('w', -1);
    $w->setValid(['class'=>'uint', 'min'=>1])->setAlias('width');
    $w->setHelp('target-width', <<<EOH
                Output image's width
                Optional parameter - if not given, the image will be scaled down
                to nearest power of 2. For example, image of <u>&lt;width&gt;</u>=10000px
                will be reduced to 8192px.
EOH
    );
    
    $h = new Parameter('h', -1);
    $h->setValid(['class'=>'uint', 'min'=>1])->setAlias('height');
    $h->setHelp('target-height', <<<EOH
                Output image's height
                Optional parameter - if not given, the image will have
                <u>&lt;height&gt;</u> = <u>&lt;width&gt;</u> / 2
EOH
    );
    
    return [$w, $h];
}

$cla = (new ImageCLA(setupCLA()))->parse();
extract($cla->postproc());

if ($cla->v > 1) echo "Loading image\n";
$srcImg = AbstractImage::make($cla->s);
$w = $srcImg->getWidth();
$h = $srcImg->getHeight();
if ($cla->v > 1) echo "Loaded $w x $h image\n";

if ($cla->w < 0) {
    $t = 2;
    while ($t*2 <= $w) $t *= 2;
    $cla->w = $t;
}
if ($cla->h < 0)
    $cla->h = $cla->w / 2;

if ($cla->v > 1) echo "Scaling to $cla->w x $cla->h\n";
$srcImg->scale($cla->w, $cla->h);

if ($cla->v > 1) echo "Writing\n";
$srcImg->write($cla->o);
$srcImg->destroy();
if ($cla->v > 1) echo "Done\n". $cla->v;
exit(0);

?>
