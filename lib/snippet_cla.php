<?php

    $cla = getopt($switches . 's:o:v', array('help'));
    $reqd .=  's';
    if (!$cla || count(array_diff_key(array_flip(str_split($reqd)), $cla))>0)
        exit("Incorrect syntax. See `{$argv[0]} --help` for help.\n");

    # Inicjalizacja parametrów z linii poleceń
    // Tylko wypisz pomoc
    if (array_key_exists('help', $cla)) printHelp(); // Zawiera exit
    foreach (str_split($reqd) as $val) {
        $$val = $cla[$val];
    }
    $verbose = (array_key_exists('v', $cla)) ? true : false;
    $destFileName = (array_key_exists('o', $cla)) ? $cla['o'] : 'modified-' . $cla['s'];

    // Weź nazwę pliku i załaduj obrazek
    if ($verbose) echo "Loading image\n";
    $sourceImg = new Image($cla['s']);
    if ($verbose) echo 'Loaded '. $sourceImg->getWidth() .'x'. $sourceImg->getHeight() ." image\n";

 ?>
