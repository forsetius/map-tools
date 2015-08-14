#!/usr/bin/php5
<?php
require_once 'lib/class_Image.php';

$switches = 'l:t:r:b:';
$reqd = '';
require 'lib/snippet_cla.php';

$l = (array_key_exists('l', $cla)) ? $cla['l'] : 0;
$t = (array_key_exists('t', $cla)) ? $cla['t'] : 0;
$r = (array_key_exists('r', $cla)) ? $cla['r'] : $sourceImg->getWidth() - $l;
$b = (array_key_exists('b', $cla)) ? $cla['b'] : $sourceImg->getHeight() - $t;

if ($verbose) echo "Writing clip\n";
$sourceImg->writeRect($t, $l, $sourceImg->getWidth() - $l - $r, $sourceImg->getHeight() - $t - $b, $destFileName);
if ($verbose) echo "Done\n";

function printHelp()
{
    echo <<<EOH
Clips given image.

\e[1mSYNTAX\e[0m
        clip-image.php -s \e[4m<source-filename>\e[0m [-o \e[4m<output-filename>\e[0m] [-l \e[4m<left-margin>\e[0m] [-t \e[4m<top-margin>\e[0m] [-r \e[4m<right-margin>\e[0m] [-b \e[4m<bottom-margin>\e[0m] [-v]
        make-vt.php --help

\e[1mSWITCHES\e[0m:
        \e[1m-s\e[0m \e[4m<source-filename>\e[0m
                (required) filename of source map

        \e[1m-o\e[0m \e[4m<output-filename>\e[0m
                (optional) a path and filename for output image
                If not provided, 'modified-' + <source-filename> is used.

        \e[1m-l\e[0m \e[4m<x-pos>\e[0m
                Left margin

        \e[1m-t\e[0m \e[4m<y-pos>\e[0m
                Top margin

        \e[1m-r\e[0m
                Right margin

        \e[1m-b\e[0m
                Bottom margin

        \e[1m-v\e[0m
                Verbose mode. The script will issue reports on its progress.

        \e[1m--help\e[0m
                This help information.

\e[1mNOTE\e[0m:
        If l, t, r or b is not provided then 0 margin on that side is assumed.

EOH;
    exit;
}
 ?>
