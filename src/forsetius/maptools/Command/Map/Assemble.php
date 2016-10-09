<?php
namespace forsetius\maptools\Command\Map;
use forsetius\cli\Command\AbstractCommand;
use forsetius\maptools\CapabilityException;
use forsetius\maptools\Image\AbstractImage;
use forsetius\cli\Argument\Parameter;
use forsetius\cli\Argument\Requisite;
use forsetius\cli\ProgressBar;
use forsetius\reuse\GlobalPool as Pool;

class Assemble extends AbstractCommand
{
    public function execute()
    {
        $cla = $this->cla;
        $level = -1;
        while (\file_exists($cla->s . DIRECTORY_SEPARATOR . 'level'. ($level+1))) {
            $level++;
        }
        if ($level < 0) throw new CapabilityException("Directory `$cla->s` doesn't contain any virtual texture levels.", CapabilityException::BAD_DATA);
        if ($level > $cla->l) {
            $level = $cla->l;
        } else {
            if ($cla->l != 255 && $cla->v > 0) echo "Requested texture level $cla->l not found. Using max found level: $level";
        }
        $srcPath = $cla->s . DIRECTORY_SEPARATOR . 'level' . $level . DIRECTORY_SEPARATOR;

        // stwórz pusty obrazek docelowy
        $destImg = AbstractImage::make(pow(2,$level)*1024,pow(2,$level)*512);

        $tileImg = AbstractImage::make(512, 512);
        if ($cla->v > 1) $pb = new ProgressBar(pow(2,2*$level+1), '    Slicing the map: ');
        for ($x=0;$x<pow(2,$level+1);$x++) {
            for ($y=0;$y<pow(2,$level);$y++) {
                $tileImg->load($srcPath . 'tx_'. $x .'_'. $y .'.png');
                $tileImg->copyTo(0, 0, 512, 512, $destImg->get(), 512*$x, 512*$y);
                $tileImg->destroy();
                if ($cla->v > 1) $pb->progress();
            }
        }
        $tileImg = null;
        if ($cla->v > 1) echo "\n";

        $destImg->write($cla->o);
        $destImg->destroy();
        unset($destImg);
    }

    protected function setup()
    {
        $s = new Requisite('s');
        $s->setValid(['class'=>'filepath'])->setAlias('source');
        $s->setHelp("source-folder", <<<EOH1
                    Path to source map's folder (the one containing the levels).
                    Required parameter.
EOH1
        );

        $lambda = function ($val) {
            return \str_replace('?', pow(2, $GLOBALS['level']), $val);
        };
        $defO = Pool::getConf()->get("default:outputMapName");
        $o = new Parameter('o', $defO);
        $o->setValid(['class'=>'dirpath'])->setAlias('output')->setTransform($lambda);
        $o->setHelp('output-image', <<<EOH2
                    A path and filename for output image
                    Optional parameter - if not provided,
                    `$defO` is used.
EOH2
        );

        $l = new Parameter('l', 255);
        $l->setValid(['class'=>'uint', 'max'=>255])->setAlias('level');
        $l->setHelp('level', <<<EOH3
                    Virtual texture's level to assemble into one image
                    Optional parameter - if not given, the highest level
                    found will be used.
EOH3
        );

        return [$s, $o, $l];
    }
}
