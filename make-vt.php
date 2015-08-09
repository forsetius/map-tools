<?php
define("VERSIOM", 0.1);
define("DEFAULT_ADDON_NAME", "Forseti");
define("DEFAULT_VT_FOLDER_NAME", "map?k");

$cla = getopt("s:a:o:hv");
if (! $cla) exit('Incorrect syntax. See `make-vt.php -h` for help.');

# Inicjalizacja parametrów z linii poleceń
// Tylko wypisz pomoc
if (array_key_exists('h', $cla)) printHelp(); // Zawiera exit
// Tylko wypisz wersję
if (array_key_exists('v', $cla)) printVersion(); // Zawiera exit

// Weź nazwę pliku z mapą
if (! array_key_exists('s', $cla)) exit('No map\'s filename given. See `make-vt.php -h` for help.');
$mapName = $cla['s'];

// Weź nazwę dodatku jeśli podana. Nie? użyj domyślnej
if (array_key_exists('a', $cla)) {
    if (preg_match('/\w+/', $cla['a']) == 1) {
        $addonName = $cla['a'];
    } else {
        exit('Error! Add-on\'s name contains illegal characters. Use letters, numbers and underscores only');
    }
} else $addonName = DEFAULT_ADDON_NAME;

// Weź nazwę folderu na VT jeśli podana. Nie? użyj domyślnej
if (array_key_exists('o', $cla)) {
    if (preg_match('/\w+/', $cla['o']) == 1) {
        $vtName = $cla['o'];
    } else {
        exit('Error! Virtual texture\'s name contains illegal characters. Use letters, numbers and underscores only');
    }
} else $vtName = DEFAULT_VT_FOLDER_NAME;

// Wczytaj obrazek
if(! file_exists($cla)) exit('Incorrect map\'s filename. File doesn\'t exist');
switch (exif_imagetype($argv[1])) {
	case IMAGETYPE_JPEG :
		$imType = 'Jpeg';
		break;
	case IMAGETYPE_PNG :
		$imType = 'Png';
		break;
	default :
		exit("Error! Unsupported file type. Supported types: Jpeg and PNG.");
}
$callFunc = 'imageCreateFrom' . $imType;
$sourceImg = $callFunc($cla['s']);

// Ustal wymiary obrazka
$sourceWidth = imagesx($sourceImg);
$sourceHeight = imagesy($sourceImg);

//   - sprawdź czy szerokość = 2 * wysokość. Nie? wyrzuć błąd
if ($sourceWidth != $sourceHeight) exit('Error! Map\'s width must be 2 * height.');
if ($sourceHeight < 1024) exit('Error! Map\'s resolution is too low. Should be 2048*1024 or greater');

// ustal docelowe wymiary i poziom mapy
$dim = 1024; $level = 0;
while ($dim*2 <= $sourceWidth) {
    $dim *= 2; $level++;
}

// załóż katalog na addon o nazwie $addonName
if (! file_exists($addonName)) mkdir($addonName);
if (! file_exists($addonName)) exit('Error! Couldn\'t create add-on\'s folder. Permission issue?' . $addonName);

// załóż podkatalogi textures/hires/map$level/
$vtName = str_replace('?', $dim/2/1024 , $vtName);
$vtPath = $addonName . '/textures/hires/' . $vtName;
if (file_exists($vtPath)) exec('rm -rf ' . $vtName);
mkdir($vtPath, 0777, true);
createSSC($addonName);
createCTX($vtPath, $vtName);

// dla każdego poziomu mapy od bieżącego do 1 stwórz kafelki
for ($level; $level > 0 ; $level--) {
    // załóż katalog level$nr
    mkdir($vtName . '/level' . $level);
    $sourceImg = scale($sourceImg, $dim);

    // Potnij na obrazki 512*512 i zapisz je w katalogu level$nr
    for ($x=0;$x<pow(2,$level+1);$x++) {
    	for ($y=0;$y<pow(2,$level);$y++) {
            createTile($sourceImg, 512, $x, $y, $vtName . '/level' . $level);
        }
    }

    // przeskaluj mapę do 50%*50%
    $dim /= 2;
} // koniec pętli, w której tworzymy kafelki

// załóż katalog level0
mkdir($vtName . '/level0');

// potnij na 2 obrazki 1024*1024 i zapisz je w katalogu level0
for ($x=0; $x < 1; $x++) createTile($sourceImg, 1024, $x, 0, $vtName . '/level0');
imagedestroy($targetImg);


function scale($sourceImg, $dim) {
    $scaledImg = imagecreatetruecolor($dim, $dim/2);
    imagecopyresampled($scaledImg, $sourceImg, 0, 0, 0, 0, $dim-1, $dim/2-1, imagesx($sourceImg)-1, imagesy($sourceImg)-1);
    imagedestroy($sourceImg);
    return $scaledImg;
}

function createTile($sourceImg, $res, $x, $y, $path) {
    $targetImg = imagecreatetruecolor($res, $res);
    imagecopy($targetImg, $sourceImg, 0, 0, $res*$x, $res*$y, $res, $res);
    imagepng($targetImg, $path . '/tx_' . $x . '_' . $y . '.png', 9);
    imagedestroy($targetImg);
}

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

function printHelp()
{
    echo <<<EOH
This utility creates a Virtual Texture (VT) out of map provided.

Syntax: make-vt.php -s <source-map-filename> [-a <addon-name>] []-o <output-texture-name>]
        make-vt.php -h
        make-vt.php -v

Notes:
        1. source map must have dimensions:  width = 2 * height
        2. source map must have at least 1024px height

Switches:
       -s : (required) filename of source map
       -a : (optional) name of addon that will include the VT to be created.
            If not provided, default {DEFAULT_ADDON_NAME} is used
       -o : (optional) name of VT within the addon
            If not provided, default {DEFAULT_VT_FOLDER_NAME} is used.
            If name contains ? character, it will be substituted with map size.
EOH;
    exit;
}

function printVersion()
{
    echo 'Version: ' . VERSION;
    exit;
}

 ?>
