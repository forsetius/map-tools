#!/usr/bin/php5
<?php
require_once 'lib/class_Image.php';

define("DEFAULT_ADDON_NAME", "Forseti");
define("DEFAULT_VT_FOLDER_NAME", "map?k");

$cla = getopt("s:a:o:hv");
if (! $cla) exit("Incorrect syntax. See `make-vt.php -h` for help.\n");

# Inicjalizacja parametrów z linii poleceń
// Tylko wypisz pomoc
if (array_key_exists('h', $cla)) printHelp(); // Zawiera exit
// Tylko wypisz wersję
$verbose = (array_key_exists('v', $cla)) ? true : false;

// Weź nazwę pliku z mapą
if (! array_key_exists('s', $cla)) exit("No map's filename given. See `make-vt.php -h` for help.\n");

// Weź nazwę dodatku jeśli podana. Nie? użyj domyślnej
if (array_key_exists('a', $cla)) {
    if (preg_match('/\w+/', $cla['a']) == 1) {
        $addonName = $cla['a'];
    } else {
        exit("Error! Add-on's name contains illegal characters. Use letters, numbers and underscores only\n");
    }
} else $addonName = DEFAULT_ADDON_NAME;

// Weź nazwę folderu na VT jeśli podana. Nie? użyj domyślnej
if (array_key_exists('o', $cla)) {
    if (preg_match('/\w+/', $cla['o']) == 1) {
        $vtName = $cla['o'];
    } else {
        exit("Error! Virtual texture's name contains illegal characters. Use letters, numbers and underscores only\n");
    }
} else $vtName = DEFAULT_VT_FOLDER_NAME;

// Wczytaj obrazek
if ($verbose) echo "Loading map\n";
$sourceImg = new Image($cla['s']);
if ($verbose) echo 'Loaded '. $sourceImg->getWidth() .'x'. $sourceImg->getHeight() ."image\n";

//   - sprawdź czy szerokość = 2 * wysokość. Nie? wyrzuć błąd
if ($sourceImg->getWidth() != 2 * $sourceImg->getHeight()) exit("Error! Map's width must be 2 * height.\n");
if ($sourceImg->getHeight() < 1024) exit("Error! Map's resolution is too low. Should be 2048*1024 or greater\n");

// ustal docelowe wymiary i poziom mapy
$dim = 1024; $level = 0;
while ($dim*2 <= $sourceImg->getWidth()) {
    $dim *= 2; $level++;
}

if ($verbose) echo "Max level: $level, resolution: $dim x ". $dim/2 ."\n";
// załóż katalog na addon o nazwie $addonName
if (! file_exists($addonName)) mkdir($addonName);
if (! file_exists($addonName)) exit("Error! Couldn't create add-on's folder $addonName. Permission issue?\n");

// załóż podkatalogi textures/hires/map$level/
$vtName = str_replace('?', $dim/1024 , $vtName);
$vtPath = $addonName . '/textures/hires/' . $vtName;
if (file_exists($vtPath)) exec('rm -rf ' . $vtPath);
if ($verbose) echo "Creating folders in $vtPath\n";
mkdir($vtPath, 0777, true);
createSSC($addonName, $vtName);
createCTX($vtPath, $vtName);

// dla każdego poziomu mapy od bieżącego do 1 stwórz kafelki
if ($verbose) echo "Slicing the map\n";
for ($level; $level > 0 ; $level--) {
    // załóż katalog level$nr
    if ($verbose) echo "Level $level\n";
    mkdir($vtPath . '/level' . $level);
    $sourceImg->scale($dim, $dim/2);

    // Potnij na obrazki 512*512 i zapisz je w katalogu level$nr
    for ($x=0;$x<pow(2,$level+1);$x++) {
    	for ($y=0;$y<pow(2,$level);$y++) {
            $sourceImg->writeRect(512*$x, 512*$y, 512, 512, $vtPath . '/level' . $level . '/tx_' . $x . '_' . $y . '.png');
        }
    }

    // przeskaluj mapę do 50%*50%
    $dim /= 2;
} // koniec pętli, w której tworzymy kafelki

// poziom 0
$sourceImg->scale(2048, 1024);
if ($verbose) echo "Level 0\n";
mkdir($vtPath . '/level0');
$sourceImg->writeRect(0, 0, 1024, 1024, $vtPath . '/level0/tx_0_0.png');
$sourceImg->writeRect(1024, 0, 1024, 1024, $vtPath . '/level0/tx_1_0.png');

if ($verbose) echo "Done\n";

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

\e[1mSYNTAX\e[0m
        make-vt.php -s \e[4m<source-map-filename>\e[0m [-a \e[4m<addon-name>\e[0m] [-o \e[4m<output-texture-name>\e[0m] [-v]
        make-vt.php -h

\e[1mNOTES\e[0m:
        1. source map must have dimensions:  width = 2 * height
        2. source map must have at least 1024px height
        3. it can be PNG or Jpeg type

\e[1mSWITCHES\e[0m:
        \e[1m-s\e[0m \e[4m<source-map-filename>\e[0m
                (required) filename of source map

        \e[1m-a\e[0m \e[4m<addon-name>\e[0m
                (optional) name of addon that will include the VT to be created.
                If not provided, default '{DEFAULT_ADDON_NAME}' is used

        \e[1m-o\e[0m \e[4m<output-texture-name>\e[0m
                (optional) name of VT within the addon
                If not provided, default '{DEFAULT_VT_FOLDER_NAME}' is used.
                If name contains ? character, it will be substituted with map size.

        \e[1m-h\e[0m
                This help information.

        \e[1m-v\e[0m
                Verbose mode. The script will issue reports on its progress.

EOH;
    exit;
}

 ?>
