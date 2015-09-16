#!/usr/bin/php5
<?php
namespace pl\forseti\maptools;
require_once realpath(dirname(__FILE__)).'/lib/autoload.php';

use pl\forseti\cli\ProgressBar;
use pl\forseti\reuse\Benchmark;
use pl\forseti\cli\Option;
use pl\forseti\reuse\FilesystemException as FSe;

$bm = Benchmark::getInstance();

$cla = new ImageCLA();
$cla->addArg(new Option('t', 1024));
$cla->parse();
extract($cla->postproc());

$bm->setEcho($cla->v);
$bm->rec('After parsing CLI, before loading image');

if ($cla->v) echo "Loading image\n";
$srcImg = aImage::make($cla->s);
$w = $srcImg->getWidth();
$h = $srcImg->getHeight();
if ($cla->v) echo "Loaded $w x $h image\n";

$bm->rec('Loaded image');
// swapować za jednym zamachem, oba obrazki (źródłowy i wynikowy) zmieszczą się w pamięci bez bólu
if ($cla->t === false) {
    $destImg = aImage::make($w, $h);
    $bm->recMemory('After creation of target image');
    $srcImg->copyTo(0, 0, ceil($w/2), $h, $destImg->get(), floor($w/2));
    $srcImg->copyTo(ceil($w/2), 0, floor($w/2), $h, $destImg->get());
    $bm->recMemory('After swapping, before destroying source image');
    $srcImg->destroy();
    $bm->recMemory('After destroying source image');
    
    $srcImg = null;
    unset($srcImg);
    $bm->recMemory('After null on source object');
    $bm->recTime('After swapping');
    
// pokroić na parzystą w poziomie ilość mniejszych kawałków i w drugim kroku kopiować je na obrazek wynikowy
} else {
    if (! mkdir($tempDir = 'temp'. date("YmdGis")))
        throw new FSe("Couldn't create add-on's folder $tempDir. Permission issue?", FSe::ACCESS_DENIED);
    
    $nw = ceil($w/(2*$cla->t))*2; // ilość kawałków w poziomie. Niech mają max 1024px i niech ich będzie parzysta ilość
    $nh = ceil($h/$cla->t);   // ilość kawałków w pionie
    
    $tileImg = aImage::make();
    $bm->recMemory('After creation of empty tile object');
    
    if ($cla->v) $pb = new ProgressBar($nw*$nh, '    Slicing the map: ');
    $bm->recMemory('After new ProgressBar');
    for ($x = 0; $x < $nw; $x++) {
        for ($y = 0; $y < $nh; $y++) {
            $tw = ceil($w/$nw);                              // szerokość środkowych kafelków
            if ($x == 0 || $x == $nw) {
                //Robimy tak, żeby nie ciąć pikseli na pół a żeby skrajne kafelki były równej szerokości
                //(z dokładnością do 1px bo inaczej się nie da jeśli szerokość źródłowego obrazka ma nieparzystą ilość px
                $tw = ($w - ($nw-2)*$tw)/2;                  // szerokość skrajnego kafelka
                $tw = ($x == 0) ? floor($tw) : ceil($tw);    // szerokość lewych : prawych kafelków.
            }
            $th = ceil($h/$nh);
            if ($y == $nh) {
                $th = $h - ($nh-1)*$th;                      // wysokość dolnych kafelków
            }

            $tileName = "$tempDir/tile-$x-$y.png";
            $tileImg = aImage::make();
            $tileImg->set($srcImg->copy($x*$tw, $y*$th, $tw, $th));
            aImage::dump($tileImg, $tileName, true);
            $tileImg->destroy();
            $tileImg = null;
            if ($cla->v) $pb->progress();
        }
    }
    $bm->recMemory("\nPo zapisaniu wszystkich kafelków");
    $srcImg->destroy();
    $bm->recMemory('After destroying source image');
    $srcImg = null;
    $bm->recMemory('After null on source object');

    $bm->rec('Reassembling');
    if ($cla->v) $pb = new ProgressBar($nw*$nh, '    Reassembling swapped map: ');
    $bm->recMemory('After new ProgressBar');
    $destImg = aImage::make($w, $h);
    $bm->recMemory('After creation of target image');

    $dx = 0;
    for ($x = 0; $x < $nw; $x++) {
        $dy = 0;
        $sx = ($x+1 > $nw/2) ? $x - $nw/2  : $x + $nw/2;
        for ($y = 0; $y < $nh; $y++) {
            $tileImg->load($tempDir .'/tile-'. $sx .'-'.$y.'.png');
            $tw = $tileImg->getWidth();
            $th = $tileImg->getHeight();
            $tileImg->copyTo(0, 0, $tw, $th, $destImg->get(), $dx, $dy);
            
            $dy += $th;
            $tileImg->destroy();
            if ($cla->v) $pb->progress();
        }
        $dx += $tw;
    }

    $bm->recMemory('Bef null on tile object');
    $tileImg = null;
    if ($cla->v) echo "\n";

    $bm->recMemory('After null on tile object');
    if (substr(strtolower(php_uname('s')),0,3) == 'win') {
        exec("DEL /S $tempDir");
    } else {
        exec("rm -rf $tempDir");
    }
    $bm->recMemory('After null on tile object');
}

$bm->rec('Writing');
$destImg->write($cla->o);
$bm->rec('Done');

?>
