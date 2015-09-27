#!/usr/bin/env php
<?php
namespace pl\forseti\maptools;
require_once realpath(dirname(__FILE__)).'/lib/autoload.php';

use \pl\forseti\cli\Parameter;
use pl\forseti\cli\Binary;
use pl\forseti\cli\Option;
use pl\forseti\reuse\FilesystemException as FSe;
use pl\forseti\cli\ProgressBar;

$options = array(
                new Binary('d'),
                new Parameter('l', 0),
                new Parameter('t', 0),
                new Parameter('r', 0),
                new Parameter('b', 0),
                new Option('c', 1024)
                );
$cla = new ImageCLA($options);
$cla->parse();
extract($cla->postproc());

if ($cla->v) echo "Loading image\n";
$srcImg = aImage::make($cla->s);
$w = $srcImg->getWidth();
$h = $srcImg->getHeight();

if ($cla->d === true) {
    // check only
    if ($cla->v) echo "Detecting border\n";
    
    $l = 0;
    $pc= $srcImg->getColorIndex(0, 0);
    for ($x=0;$x<$w;$x++) {
        for ($y=0;$y<$h;$y++) {
            if ($pc != $srcImg->getColorIndex($x, $y)) break 2;
        }
        $l++;
    }
    
    $r = 0;
    $pc= $srcImg->getColorIndex($w-1, 0);
    for ($x=$w-1; $x>$l; $x--) {
        for ($y=0;$y<$h;$y++) {
            if ($pc != $srcImg->getColorIndex($x, $y)) break 2;
        }
        $r++;
    }
    
    $t = 0;
    $rm = $w-$r;
    $pc= $srcImg->getColorIndex(0, 0);
    for ($y=0;$y<$h;$y++)  {
        for ($x=$l; $x<$rm; $x++) {
            if ($pc != $srcImg->getColorIndex($x, $y)) break 2;
        }
        $t++;
    }

    $b = 0;
    $pc= $srcImg->getColorIndex($w-1, $h-1);
    for ($y=$h-1, $bm=$h-$t;$y<$bm;$y--)  {
        for ($x=$l; $x<$rm; $x++) {
            if ($pc != $srcImg->getColorIndex($x, $y)) break 2;
        }
        $b++;
    }
    
    echo "-l $l -t $t -r $r -b $b\n";
    
} else {
    // got the actual values from user? crop it!
    if ($cla->c === false) {
        //crop in one piece
        if ($cla->v) echo "Clipping\n";
        $srcImg->crop($cla->l, $cla->t, $cla->r, $cla->b);
        
        if ($cla->v) echo "Writing\n";
        $srcImg->write($cla->o);
    } else {
        //TODO cut into tiles to crop
        if (! mkdir($tempDir = 'temp'. date("YmdGis")))
            throw new FSe("Couldn't create add-on's folder $tempDir. Permission issue?", FSe::ACCESS_DENIED);
        
        $w = $w-$cla->l-$cla->r;
        $h = $h-$cla->t-$cla->b;
        $nw = ceil($w/$cla->c); // ilość kawałków w poziomie.
        $nh = ceil($h/$cla->c);   // ilość kawałków w pionie
        
        $tileImg = aImage::make();
        $bm->recMemory('After creation of empty tile object');
        
        if ($cla->v) $pb = new ProgressBar($nw*$nh, '    Slicing the map: ');
        $bm->recMemory('After new ProgressBar');
        for ($x = 0; $x < $nw; $x++) {
            $tw = ($x == $nw) ? $w - ($nw-1)*$cla->c : $cla->c;
            for ($y = 0; $y < $nh; $y++) {
                $th = ($y == $nh) ? $h - ($nh-1)*$cla->c : $cla->c;
        
                $tileImg->set($srcImg->copy($x*$tw, $y*$th, $tw, $th));
                $tileImg->write("$tempDir/tile-$x-$y.png", true);
                $tileImg->destroy();
                if ($cla->v) $pb->progress();
            }
        }
        $bm->recMemory("\nPo zapisaniu wszystkich kafelków");
        $srcImg->destroy();
        $bm->recMemory('After destroying source image');
        $srcImg = null;
        $bm->recMemory('After null on source object');
        
        // and reconstruct
        $bm->rec('Reassembling');
        if ($cla->v) $pb = new ProgressBar($nw*$nh, '    Reassembling swapped map: ');
        $bm->recMemory('After new ProgressBar');
        $destImg = aImage::make($w, $h);
        $bm->recMemory('After creation of target image');
        
        $dx = 0;
        for ($x = 0; $x < $nw; $x++) {
            $dy = 0;
            for ($y = 0; $y < $nh; $y++) {
                $tileImg->load($tempDir .'/tile-'. $x .'-'.$y.'.png');
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
    
    if ($cla->v) echo "Done\n";
}
exit(0);

 ?>
