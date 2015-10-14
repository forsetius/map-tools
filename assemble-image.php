#!/usr/bin/env php
<?php
namespace pl\forseti\maptools;
require_once realpath(dirname(__FILE__)).'/lib/autoload.php';

use pl\forseti\cli\ProgressBar;
use pl\forseti\reuse\Benchmark;
use pl\forseti\cli\Parameter;
use pl\forseti\reuse\ExternalDataException;
use pl\forseti\reuse\Config;
use pl\forseti\cli\Requisite;

$cfg = new Config(realpath(dirname(__FILE__)).'/lib/config.php');
$bm = Benchmark::getInstance();

function setupCLA()
{
    $s = new Requisite('s');
    $s->setValid(['class'=>'filepath'])->setAlias('source');
    $s->setHelp('source-folder', <<<EOH
                Path to source map's folder (the one containing the levels).
                Required parameter.
EOH
        );
    
    $lambda = function ($val) {
        return \str_replace('?', pow(2, $GLOBALS['level']), $val);
    };
    $o = new Parameter('o',$GLOBALS['cfg']->defOutputMapName);
    $o->setValid(['class'=>'filepath'])->setAlias('output')->setTransform($lambda);
    $o->setHelp('output-image', <<<EOH
                A path and filename for output image
                Optional parameter - if not provided,
                `{$GLOBALS['cfg']->defOutputMapName}` is used.
EOH
        );
    
    $l = new Parameter('l', 255);
    $l->setValid(['class'=>'uint', 'max'=>255])->setAlias('level');
    $l->setHelp('level', <<<EOH
                Virtual texture's level to assemble into one image
                Optional parameter - if not given, the highest level
                found will be used.
EOH
        );
    
    return [$s, $o, $l];
}

$cla = (new ImageCLA(setupCLA()))->parse();
extract($cla->postproc());

$bm->setEcho($cla->v);
if ($cla->v == 3) $bm->recTime('After parsing CLI');

$level = -1;
while (\file_exists($cla->s . DIRECTORY_SEPARATOR . 'level'. ($level+1))) {
    $level++;
}
if ($level < 0) throw new ExternalDataException("Directory: $cla->s doesn't contain any virtual texture levels.", 100);
if ($level > $cla->l) {
    $level = $cla->l;
} else {
    if ($cla->l != 255 && $cla->v > 0) echo "Requested texture level $cla->l not found. Using max found level: $level";
}
$srcPath = $cla->s . DIRECTORY_SEPARATOR . 'level' . $level . DIRECTORY_SEPARATOR;

// stwórz pusty obrazek docelowy
$destImg = aImage::make(pow(2,$level)*1024,pow(2,$level)*512);

$tileImg = aImage::make(512, 512);
for ($x=0;$x<pow(2,$level+1);$x++) {
    for ($y=0;$y<pow(2,$level);$y++) {
        $tileImg->load($srcPath . 'tx_'. $x .'_'. $y .'.png');
        $tileImg->copyTo(0, 0, 512, 512, $destImg->get(), 512*$x, 512*$y);
        $tileImg->destroy();
    }
}
$tileImg = null;

$destImg->write($cla->o);
$destImg->destroy();

exit(0);
?>
