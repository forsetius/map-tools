#!/usr/bin/php5
<?php
namespace pl\forseti\maptools;
require_once realpath(dirname(__FILE__)).'/lib/autoload.php';

use pl\forseti\cli\ProgressBar;
use pl\forseti\reuse\Benchmark;
use pl\forseti\cli\Option;

$bm = Benchmark::getInstance();

$cla = new ImageCLA();
$cla->addOption(new Option('s','.'));
$cla->addOption(new Option('o','map?.png'));
$cla->addOption(new Option('l',-1));                // level VT do scalenia. Domyślnie - najwyższy
$cla->parse();
extract($cla->postproc());

$bm->setEcho($cla->v);
$bm->recTime('After parsing CLI');

$w = $srcImg->getWidth();
$h = $srcImg->getHeight();

//TODO znajdź największy poziom VT i ścieżkę do niego
$level;
$srcPath;

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
$destImg->write($cla->o);
$destImg->destroy();

$level = 4;
$sourcePath = '/home/celestia/extras/Sol/4 Mars/textures/hires/Mars/level' . $level . '/';
$targetImg = imagecreatetruecolor(pow(2,$level)*1024,pow(2,$level)*512);
$targetFile = 'map'.$level.'.png';
#for ($i=0; $i<2^(2*$level+1); $i++) {
for ($x=0;$x<pow(2,$level+1);$x++) {
	for ($y=0;$y<pow(2,$level);$y++) {
		$sourceImg = imagecreatefrompng($sourcePath . 'tx_'. $x .'_'. $y .'.png');
		echo($sourcePath . 'tx_'. $x .'_'. $y .'.png'. chr(13));
		#echo($sourceImg);

		imagecopy($targetImg, $sourceImg, 512*$x, 512*$y, 0, 0, 512, 512);
		imagedestroy($sourceImg);


	}
}
imagepng($targetImg, $targetFile, 9);
imagedestroy($targetImg);
?>
