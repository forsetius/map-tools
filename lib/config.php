<?php
$capabilities = array(
    'GfxLibs' => array(
        'gd',
        'imagick'
    ),
    'ImgFormats' =>array(
        'jpeg',
        'png'
    )
);

$connections = array();

$defaults = array(
    'OutputImgName' => '../modified-?',
    'OutputMapName' => 'map?k',
    'GfxLib' => 'gd',
    'TileSize' =>   1024,
    'CutToTiles' => false,
    'Verbosity' => 2,
);
 ?>