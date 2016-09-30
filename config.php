<?php
$app = array(
    'Version' => '2.0-alpha1',
);

$capabilities = array(
    'GfxLibs' => ['gd', 'imagick'],
    'ImgFormats' => ['jpeg', 'png'],
);

$connections = array();

$defaults = array(
    'OutputImgName' => "modified-?",
    'OutputMapName' => "map?k.png",
    'OutputTxName' => "map",
    'GfxLib' => 'gd',
    'TileSize' =>   1024,
    'CutToTiles' => false,
    'Verbosity' => 2,
    'TestMode' => 'all',
);
 ?>
