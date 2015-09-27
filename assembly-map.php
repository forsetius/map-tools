#!/usr/bin/env php
<?php
namespace pl\forseti\maptools;
require_once realpath(dirname(__FILE__)).'/lib/autoload.php';

use pl\forseti\cli\ProgressBar;
use pl\forseti\reuse\Benchmark;
use pl\forseti\cli\Parameter;
use pl\forseti\reuse\ExternalDataException;

$bm = Benchmark::getInstance();

$cla = new ImageCLA();
$cla->addArg(new Parameter('s','.'));
$cla->addArg(new Parameter('o','map.png'));
$cla->addArg(new Parameter('l',255));                // level VT do scalenia. Domyślnie - najwyższy
$cla->parse();
extract($cla->postproc());

$bm->setEcho($cla->v);
$bm->recTime('After parsing CLI');

$level = -1;
while (\file_exists($cla->s .'\level'. ($level+1))) {
    $level++;
}
if ($level < 0) throw new ExternalDataException("Directory: $cla->s doesn't contain any virtual texture levels.", 100);
if ($level > $cla->l) {
    $level = $cla->l;
} else {
    if ($cla->l != 255 && $cla->v > 0) echo "Requested texture level $cla->l not found. Using max found level: $level";
}
$srcPath = $cla->s . $level;

// stwórz pusty obrazek docelowy
$destImg = aImage::make(pow(2,$level)*1024,pow(2,$level)*512);

$tileImg = aImage::make();
for ($x=0;$x<pow(2,$level+1);$x++) {
    for ($y=0;$y<pow(2,$level);$y++) {
        $tileImg->load($srcPath . 'tx_'. $x .'_'. $y .'.png');
        $tileImg->copyTo(0, 0, 512, 512, $destImg, 512*$x, 512*$y);
        $tileImg->destroy();
    }
}
$tileImg = null;

$destImg->write($cla->o);
$destImg->destroy();
exit(0);

?>
