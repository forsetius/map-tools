#!/usr/bin/php5
<?php
namespace pl\forseti\maptools;
use \pl\forseti\cli\Parameter;
use pl\forseti\cli\Binary;
use pl\forseti\cli\Option;

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

if ($cla->d === true) {
    // check only
    if ($cla->v) echo "Detecting border\n";
    $w = $srcImg->getWidth();
    $h = $srcImg->getHeight();
    
    $l = 0;
    $pc= $srcImg->getPixelColor(0, 0);
    for ($x=0;$x<$w;$x++) {
        for ($y=0;$y<$h;$y++) {
            if ($pc != $srcImg->getPixelColor($x, $y)) break 2;
        }
        $l++;
    }
    
    $r = 0;
    $pc= $srcImg->getPixelColor($w-1, 0);
    for ($x=$w-1; $x>$l; $x--) {
        for ($y=0;$y<$h;$y++) {
            if ($pc != $srcImg->getPixelColor($x, $y)) break 2;
        }
        $r++;
    }
    
    $t = 0;
    $rm = $w-$r;
    $pc= $srcImg->getPixelColor(0, 0);
    for ($y=0;$y<$h;$y++)  {
        for ($x=$l; $x<$rm; $x++) {
            if ($pc != $srcImg->getPixelColor($x, $y)) break 2;
        }
        $t++;
    }

    $b = 0;
    $pc= $srcImg->getPixelColor($w-1, $h-1);
    for ($y=$h-1, $bm=$h-$t;$y<$bm;$y--)  {
        for ($x=$l; $x<$rm; $x++) {
            if ($pc != $srcImg->getPixelColor($x, $y)) break 2;
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
        
    }
    $srcImg->destroy();
    
    if ($cla->v) echo "Done\n";
}

 ?>
