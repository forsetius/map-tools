<?php
namespace forsetius\maptools\Command\Map;
use forsetius\cli\Command\AbstractCommand;
use forsetius\cli\ProgressBar;
use forsetius\cli\Argument\Option;
use forsetius\reuse\FilesystemException as FSe;
use forsetius\maptools\Image\AbstractImage;
use forsetius\reuse\GlobalPool as Pool;


class Swap  extends AbstractCommand
{
    public function execute()
    {
    	$cla = $this->cla;
        if ($cla->v > 1) echo "Loading image\n";
        $srcImg = AbstractImage::make($cla->s);
        $w = $srcImg->getWidth();
        $h = $srcImg->getHeight();
        if ($cla->v > 1) echo "Loaded $w x $h image\n";
        $this->bm->rec('Loaded image');

        $destImg = ($cla->c === true) ?
            $this->executeTiledSwap($srcImg) :
            $this->executeBigSwap($srcImg);

        $this->bm->rec('Writing1');
        $destImg->write($cla->o);
        $destImg->destroy();
    }

    protected function executeTiledSwap($srcImg)
    {
        if (! mkdir($tempDir = 'temp'. date("YmdHis")))
            throw new FSe("Couldn't create add-on's folder $tempDir. Permission issue?", FSe::ACCESS_DENIED);

        $nw = ceil($w/(2*$cla->c))*2; // ilość kawałków w poziomie. Niech mają max 1024px i niech ich będzie parzysta ilość
        $nh = ceil($h/$cla->c);   // ilość kawałków w pionie

        $tileImg = AbstractImage::make();
        $this->bm->recMemory('After creation of empty tile object');

        if ($cla->v > 1) $pb = new ProgressBar($nw*$nh, '    Slicing the map: ');
        $this->bm->recMemory('After new ProgressBar');
        for ($x = 0; $x < $nw; $x++) {
            for ($y = 0; $y < $nh; $y++) {
                $tw = ceil($w/$nw);                              // szerokość środkowych kafelków
                if ($x == 0 || $x == $nw) {
                    //Robimy tak, żeby nie ciąć pikseli na pół a żeby skrajne kafelki były równej szerokości
                    //(z dokładnością do 1px bo inaczej się nie da jeśli szerokość źródłowego obrazka ma nieparzystą ilość px
                    $tw = ($w - ($nw-2)*$tw)/2;                  // szerokość skrajnego kafelka
                    $tw = ($x == 0) ? ceil($tw) : floor($tw);    // szerokość lewych : prawych kafelków.
                }
                $th = ceil($h/$nh);
                if ($y == $nh) {
                    $th = $h - ($nh-1)*$th;                      // wysokość dolnych kafelków
                }

                $tileImg->set($srcImg->copy($x*$tw, $y*$th, $tw, $th));
                $tileImg->write("$tempDir/tile-$x-$y.png", true);
                $tileImg->destroy();
                if ($cla->v > 1) $pb->progress();
            }
        }
        $this->bm->recMemory("\nPo zapisaniu wszystkich kafelków");
        $srcImg->destroy();
        $this->bm->recMemory('After destroying source image');
        $srcImg = null;
        $this->bm->recMemory('After null on source object');

        $this->bm->rec('Reassembling');
        if ($cla->v > 1) $pb = new ProgressBar($nw*$nh, '    Reassembling swapped map: ');
        $this->bm->recMemory('After new ProgressBar');
        $destImg = AbstractImage::make($w, $h);
        $this->bm->recMemory('After creation of target image');

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
                if ($cla->v > 1) $pb->progress();
            }
            $dx += $tw;
        }

        $this->bm->recMemory('Bef null on tile object');
        $tileImg = null;
        if ($cla->v > 1) echo "\n";

        $this->bm->recMemory('After null on tile object');
        if (substr(strtolower(php_uname('s')),0,3) == 'win') {
            exec("DEL /S $tempDir");
        } else {
            exec("rm -rf $tempDir");
        }
        $this->bm->recMemory('After null on tile object');

        return $destImg;
    }

    protected function executeBigSwap($srcImg)
    {
        $destImg = AbstractImage::make($w, $h);
        $this->bm->recMemory('After creation of target image');
        $srcImg->copyTo(0, 0, ceil($w/2), $h, $destImg->get(), floor($w/2));
        $srcImg->copyTo(ceil($w/2), 0, floor($w/2), $h, $destImg->get());
        $this->bm->recMemory('After swapping, before destroying source image');
        $srcImg->destroy();
        $this->bm->recMemory('After destroying source image');

        $srcImg = null;
        unset($srcImg);
        $this->bm->recMemory('After null on source object');
        $this->bm->recTime('After swapping');

        return $destImg;
    }

    protected function setup()
    {
    	$tile = Pool::getConf()->get("default:tileSize");
        $c = new Option('c', $tile);
        $c->setValid(['class'=>'uint','min'=>64])->setAlias('cut');
        $c->setHelp('tile-size', <<<EOH
        Swap halves of the image by cutting it into smaller pieces
            and reassembling swapped afterwards.
            Option. If not used, image is swapped in one piece (requires
            more memory but is quicker). If used, by default the image is
            cut into tiles not bigger than $tile*$tile px. Optional value
            can be specified if different tile dimensions are required.
EOH
        );
        return [$c];
    }
}
