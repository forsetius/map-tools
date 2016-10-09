<?php
namespace forsetius\maptools\Command\Map;
use forsetius\cli\Command\AbstractCommand;
use forsetius\cli\Parameter;
use forsetius\cli\ProgressBar;
use forsetius\reuse\FilesystemException as FSe;
use forsetius\maptools\Image\AbstractImage;
use forsetius\maptools\CapabilityException;
use forsetius\reuse\GlobalPool as Pool;


class Texturize  extends AbstractCommand
{
    public function execute()
    {
        $cla = $this->cla;
        if ($cla->v > 1) echo "Loading image\n";
        $srcImg = AbstractImage::make($cla->s);
        $w = $srcImg->getWidth();
        $h = $srcImg->getHeight();
        if ($cla->v > 1) echo "Loaded $w x $h image\n";

        if ($w != 2 * $h)
            throw new CapabilityException("Error! Map's width must be 2 * height.", CapabilityException::FAILED_REQUIREMENT);
        if ($h < 1024)
            throw new CapabilityException("Error! Map's resolution is too low. Should be 2048*1024 or greater", CapabilityException::FAILED_REQUIREMENT);

        // ustal docelowe wymiary i poziom mapy
        $dim = 1024; $level = 0;
        while ($dim*2 <= $w) {
            $dim *= 2; $level++;
        }

        if ($cla->v > 1) echo "Max level: $level, resolution: $dim x ". $dim/2 ."\n";

        // załóż katalog na addon
        if (! file_exists($cla->o)) {
            mkdir($cla->o);
            if (! file_exists($cla->o)) {
                throw new FSe("Couldn't create add-on's folder $cla->o. Permission issue?", FSe::ACCESS_DENIED);
            }
        }

        // załóż podkatalogi textures/hires/map$level/
        $ds = DIRECTORY_SEPARATOR;
        $vtPath = $cla->o . "{$ds}textures{$ds}hires{$ds}" . $cla->t;
        if ($cla->v > 1) echo "Creating folders in $cla->t\n";

        if (file_exists($vtPath)) {
        	if ($cla->test) {
        		$i = 0;
        		while (file_exists($vtPath . $i)) $i++;
        		$vtPath .= $i;
            } else {
            	throw new FSe("Texture folder $vtPath already exists.", FSe::FILE_EXISTS);
            }
        }
        mkdir($vtPath, 0777, true);
        $this->createSSC($cla->o, $cla->t);
        $this->createCTX($vtPath, $cla->t);

        // dla każdego poziomu mapy od bieżącego do 1 stwórz kafelki
        $tileImg = AbstractImage::make();
        while ($level > -1) {
            if ($cla->v > 1) echo "Level $level\n";
            mkdir($vtPath . '/level' . $level);

            if ($cla->v > 1) echo "    Scaling the map to $dim x ". $dim/2 ."\n";
            $srcImg->scale($dim, $dim/2);
            $tileDim = ($level == 0) ? 1024 : 512;

            // Potnij na obrazki 512*512 i zapisz je w katalogu level$snr
            if ($cla->v > 1) $pb = new ProgressBar(pow(2,2*$level+1), '    Slicing the map: ');

            for ($x=0;$x<pow(2,$level+1);$x++) {
            	for ($y=0;$y<pow(2,$level);$y++) {
                    $tileImg->set($srcImg->copy($tileDim*$x, $tileDim*$y, $tileDim, $tileDim));
                    $tileImg->write($vtPath . '/level' . $level . '/tx_' . $x . '_' . $y . '.png', true);
                    if ($cla->v > 1) $pb->progress();
                }
            }

            // przeskaluj mapę do 50%*50%
            $dim /= 2;
            $level--;
            if ($cla->v > 1) echo "\n\n";
        } // koniec pętli, w której tworzymy kafelki

        $tileImg->destroy();
        $tileImg = null;
    }

    protected function createSSC($addomName, $vtName) {
        $data = <<<EOF
AltSurface "$vtName" "Sol/???"
{
	Texture "$vtName.ctx"
}
EOF;
        file_put_contents($addomName . '/' . \pathinfo($addomName, PATHINFO_FILENAME) . '.ssc', $data);
    }

    protected function createCTX($vtPath, $vtName) {
        $data = <<<EOF
VirtualTexture
{
        ImageDirectory "$vtName"
        BaseSplit 0
        TileSize 512
        TileType "png"
}
EOF;
        file_put_contents($vtPath . '.ctx', $data);
    }

    protected function setup()
    {
        $o = new Parameter('o','Addon');
        $o->setValid(['class'=>'filepath'])->setAlias('addon');
        $o->setHelp('output-addon-name', <<<EOH
                    Name of addon that will include the VT to be created.
                    Optional parameter - if not provided, default 'Addon' is used.
EOH
        );

        $tx = Pool::getConf()->get("default:outputTxName");
        $t = new Parameter('t', $tx);
        $t->setValid(['class'=>'alnum'])->setAlias('texture');
        $t->setHelp('texture-name', <<<EOH
                    Name of Virtual Texture within the addon
                    Optional parameter -  if not provided, default '$tx' is used.
                    If name contains ? character, it will be substituted with map size.
EOH
        );

        return [$t, $o];
    }
}
