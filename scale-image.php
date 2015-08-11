#!/usr/bin/php5
<?php
require_once 'lib/class_Image.php';

$cla = getopt("s:x:y:o:hv");
if (! $cla) exit("Incorrect syntax. See `scale-image.php -h` for help.\n");

# Inicjalizacja parametrów z linii poleceń
// Tylko wypisz pomoc
if (array_key_exists('h', $cla)) printHelp(); // Zawiera exit
// Tylko wypisz wersję
$verbose = (array_key_exists('v', $cla)) ? true : false;

// Weź nazwę pliku i załaduj obrazek
if (! array_key_exists('s', $cla)) exit("No image's filename given. See `scale-image.php -h` for help.\n");
if ($verbose) echo "Loading image\n";
$sourceImg = new Image($cla['s']);
if ($verbose) echo 'Loaded '. $sourceImg->getWidth() .'x'. $sourceImg->getHeight() ."image\n";

if (array_key_exists('x', $cla)) {
    $x = $cla['x'];
} else {
    $x = 2;
    while ($x*2 <= $sourceImg->getWidth()) $x *= 2;
}

if (array_key_exists('y', $cla)) {
    $y = $cla['y'];
} else {
    $y = $x/2;
}

if ($verbose) echo "Scaling\n";
$sourceImg->scale($x, $y);
if ($verbose) echo "Writing\n";
$destFile = (array_key_exists('o', $cla)) ? $cla['o'] : 'resized-' . $cla['s'];
$sourceImg->write($destFile);
if ($verbose) echo "Done\n";

?>
