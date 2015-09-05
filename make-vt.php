#!/usr/bin/php5
<?php
namespace pl\forseti\maptools;

use pl\forseti\cli\Parameter;
use pl\forseti\cli\ProgressBar;
use pl\forseti\reuse\FilesystemException as FSe;

$cla = new ImageCLA();
$cla->addOption(new Parameter('a','Addon'));
$cla->addOption(new Parameter('o','map?k'));
$cla->parse();
extract($cla->postproc());

$srcImg = aImage::make($cla->s);

//   - sprawdź czy szerokość = 2 * wysokość. Nie? wyrzuć błąd
if ($srcImg->getWidth() != 2 * $srcImg->getHeight())
    throw new CapabilityException("Error! Map's width must be 2 * height.", CapabilityException::PARAM_OUT_OF_RANGE);
if ($srcImg->getHeight() < 1024)
    throw new CapabilityException("Error! Map's resolution is too low. Should be 2048*1024 or greater", CapabilityException::PARAM_OUT_OF_RANGE);

// ustal docelowe wymiary i poziom mapy
$dim = 1024; $level = 0;
while ($dim*2 <= $srcImg->getWidth()) {
    $dim *= 2; $level++;
}

if ($cla->v) echo "Max level: $level, resolution: $dim x ". $dim/2 ."\n";
// załóż katalog na addon o nazwie $addonName
if (! file_exists($cla->a)) mkdir($cla->a);
if (! file_exists($cla->a))
    throw new FSe("Couldn't create add-on's folder $cla->a. Permission issue?", FSe::ACCESS_DENIED);

// załóż podkatalogi textures/hires/map$level/
$vtName = str_replace('?', $dim/1024 , $cla->o);
$vtPath = $cla->a . '/textures/hires/' . $vtName;
if ($cla->v) echo "Creating folders in $vtPath\n";
mkdir($vtPath, 0777, true);
createSSC($cla->a, $vtName);
createCTX($vtPath, $vtName);

// dla każdego poziomu mapy od bieżącego do 1 stwórz kafelki
for ($level; $level > 0 ; $level--) {
    // załóż katalog level$nr
    if ($cla->v) echo "Level $level\n";
    mkdir($vtPath . '/level' . $level);
    
    if ($cla->v) echo "    Scaling the map to $dim x ". $dim/2 ."\n";
    $srcImg->scale($dim, $dim/2);

    // Potnij na obrazki 512*512 i zapisz je w katalogu level$nr
    if ($cla->v) $pb = new ProgressBar(pow(2,$level+1)*1.5, '    Slicing the map: ');
    for ($x=0;$x<pow(2,$level+1);$x++) {
    	for ($y=0;$y<pow(2,$level);$y++) {
            $srcImg->writeRect(512*$x, 512*$y, 512, 512, $vtPath . '/level' . $level . '/tx_' . $x . '_' . $y . '.png');
            if ($cla->v) $pb->progress();
        }
    }

    // przeskaluj mapę do 50%*50%
    $dim /= 2;
} // koniec pętli, w której tworzymy kafelki

// poziom 0
$srcImg->scale(2048, 1024);
if ($cla->v) echo "Level 0\n";
mkdir($vtPath . '/level0');
$srcImg->writeRect(0, 0, 1024, 1024, $vtPath . '/level0/tx_0_0.png');
$srcImg->writeRect(1024, 0, 1024, 1024, $vtPath . '/level0/tx_1_0.png');

if ($cla->v) echo "Done\n";

function createSSC($addomName, $vtName) {
    $data = <<<EOF
AltSurface "$vtName" "Sol/???"
{
	Texture "$vtName.ctx"
}
EOF;
    file_put_contents($addomName . '/' . $addomName . '.ssc', $data);
}

function createCTX($vtPath, $vtName) {
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

 ?>
