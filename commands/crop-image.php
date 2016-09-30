#!/usr/bin/env php
<?php
namespace pl\forseti\maptools;
require_once __DIR__.'/lib/autoload.php';

use \pl\forseti\cli\Parameter;
use pl\forseti\cli\Flag;
use pl\forseti\cli\Option;
use pl\forseti\reuse\FilesystemException as FSe;
use pl\forseti\cli\ProgressBar;
use pl\forseti\reuse\Config;
use pl\forseti\reuse\Benchmark;

$cfg = new Config(__DIR__.'/lib/config.php');
$bm = Benchmark::getInstance();

// Helper function. Exist just for gathering the setup visually together
// and to be sure that variables' symbols get unset and recycled :)
function setupCLA()
{
    $d = new Flag('d');
    $d->setAlias('detect');
    $d->setHelp('', <<<EOH
Attempt to automatically determine amount of pixels to cut.
Intended to help in removing the border around the map. The algorithm
checks if all the pixels of leftmost column have the same color,
then checks the column to the right and so on until column is found
to contain another color. Same process is repeated on the right, then
top and bottom. Number of same-color rows and columns is reported and
script ends
EOH
        );

    $uint = ['class'=>'uint'];
    $l = new Parameter('l', 0);
    $l->setValid($uint)->setAlias('left')->setHelp('left-margin',"Left margin");
    $t = new Parameter('t', 0);
    $t->setValid($uint)->setAlias('top')->setHelp('top-margin',"Top margin");
    $r = new Parameter('r', 0);
    $r->setValid($uint)->setAlias('right')->setHelp('right-margin',"Right margin");
    $b = new Parameter('b', 0);
    $b->setValid($uint)->setAlias('bottom')->setHelp('bottom-margin',"Bottom margin");
    
    $tile = $GLOBALS['cfg']->defTileSize;
    $c = new Option('c', $tile);
    $c->setValid($uint + ['min'=>64])->setAlias('cut');
    $c->setHelp('tile-size',<<<EOH
Crop the image by cutting it into smaller pieces and reassembling
If not used, image is cropped in one piece (requires
more memory but is quicker). If used, by default the image is
cut into $tile x $tile px tiles - except on right and bottom border
where tiles can be slimmer or lower if image's dimensions aren't
multiplies of $tile. Optional value `tile-size` can be specified if
different tile dimensions are required.
EOH
        );
    return [$d, $l, $t, $r, $b, $c];
}

$cla = (new ImageCLA(setupCLA()))->parse();
extract($cla->postproc());

if ($cla->v > 1) echo "Loading image\n";
$srcImg = AbstractImage::make($cla->s);
$w = $srcImg->getWidth();
$h = $srcImg->getHeight();

if ($cla->d === true) {
    // check only
    if ($cla->v > 1) echo "Detecting border\n";
    
    $l = 0;
    $pc= $srcImg->sampleColor(0, 0);
    for ($x=0;$x<$w;$x++) {
        for ($y=0;$y<$h;$y++) {
            if ($pc != $srcImg->sampleColor($x, $y)) break 2;
        }
        $l++;
    }
    
    $r = 0;
    $pc= $srcImg->sampleColor($w-1, 0);
    for ($x=$w-1; $x>$l; $x--) {
        for ($y=0;$y<$h;$y++) {
            if ($pc != $srcImg->sampleColor($x, $y)) break 2;
        }
        $r++;
    }
    
    $t = 0;
    $rm = $w-$r;
    $pc= $srcImg->sampleColor(0, 0);
    for ($y=0;$y<$h;$y++)  {
        for ($x=$l; $x<$rm; $x++) {
            if ($pc != $srcImg->sampleColor($x, $y)) break 2;
        }
        $t++;
    }

    $b = 0;
    $pc= $srcImg->sampleColor($w-1, $h-1);
    for ($y=$h-1;$y>$t;$y--)  {
        for ($x=$l; $x<$rm; $x++) {
            if ($pc != $srcImg->sampleColor($x, $y)) break 2;
        }
        $b++;
    }
    
    echo "-l $l -t $t -r $r -b $b\n";
    
} else {
    // got the actual values from user? crop it!
    if ($cla->c === false) {
        //crop in one piece
        if ($cla->v > 1) echo "Cropping\n";
        $srcImg->crop($cla->l, $cla->t, $cla->r, $cla->b);
        
        if ($cla->v > 1) echo "Writing\n";
        $srcImg->write($cla->o);
    } else {
        //TODO cut into tiles to crop
        if (! mkdir($tempDir = 'temp'. date("YmdGis")))
            throw new FSe("Couldn't create add-on's folder $tempDir. Permission issue?", FSe::ACCESS_DENIED);
        
        $w = $w-$cla->l-$cla->r;
        $h = $h-$cla->t-$cla->b;
        $nw = ceil($w/$cla->c); // ilość kawałków w poziomie.
        $nh = ceil($h/$cla->c);   // ilość kawałków w pionie
        
        $tileImg = AbstractImage::make();
        $bm->recMemory('After creation of empty tile object');
        
        if ($cla->v) $pb = new ProgressBar($nw*$nh, '    Slicing the image: ');
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
        if ($cla->v) $pb = new ProgressBar($nw*$nh, '    Reassembling cropped: ');
        $bm->recMemory('After new ProgressBar');
        $destImg = AbstractImage::make($w, $h);
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
        
        $bm->recMemory('Before null on tile object');
        $tileImg = null;
        if ($cla->v) echo "\n";
        
        $bm->recMemory('After null on tile object');
        if (substr(strtolower(php_uname('s')),0,3) == 'win') {
            exec("DEL /S $tempDir");
        } else {
            exec("rm -rf $tempDir");
        }
        $bm->recMemory('After null on tile object');
        
        if ($cla->v > 1) echo "Writing\n";
        $destImg->write($cla->o);
        $destImg->destroy();
    }
    
    if ($cla->v > 1) echo "Done\n";
}

exit(0);

 ?>
