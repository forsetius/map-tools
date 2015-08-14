#!/usr/bin/php5
<?php
require_once 'lib/class_Image.php';

$switches = 'w:h:';
$reqd = '';
require 'lib/snippet_cla.php';

if (array_key_exists('w', $cla)) {
    $w = $cla['w'];
} else {
    $w = 2;
    while ($w*2 <= $sourceImg->getWidth()) $w *= 2;
}

$h = (array_key_exists('h', $cla)) ? $cla['h'] : $w/2;

if ($verbose) echo "Scaling to $w x $h\n";
$sourceImg->scale($w, $h);

if ($verbose) echo "Writing\n";
$sourceImg->write($destFileName);
if ($verbose) echo "Done\n";


function printHelp()
{
    echo <<<EOH
Scales given image.

\e[1mSYNTAX\e[0m
        scale-image.php -s \e[4m<source-filename>\e[0m [-o \e[4m<output-filename>\e[0m] [-w \e[4m<width>\e[0m] [-h \e[4m<height>\e[0m] [-v]
        make-vt.php --help

\e[1mSWITCHES\e[0m:
        \e[1m-s\e[0m \e[4m<source-filename>\e[0m
                (required) filename of source map

        \e[1m-o\e[0m \e[4m<output-filename>\e[0m
                (optional) a path and filename for output image
                If not provided, 'modified-' + <source-filename> is used.

        \e[1m-w\e[0m
                Output image's width
                If not given, the image will be scaled down to nearest power of 2
                For example, image of width=10000px will be reduced to 8192px

        \e[1m-h\e[0m
                Output image's height
                If not given, the image will have height = width / 2

        \e[1m-v\e[0m
                Verbose mode. The script will issue reports on its progress.

        \e[1m--help\e[0m
                This help information.


EOH;
    exit;
}
?>
