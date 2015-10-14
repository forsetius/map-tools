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
    'OutputImgName' => "modified-?",
    'OutputMapName' => "map?k.png",
    'GfxLib' => 'gd',
    'TileSize' =>   1024,
    'CutToTiles' => false,
    'Verbosity' => 2,
);
echo '';
 ?>