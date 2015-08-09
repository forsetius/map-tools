<?php

if (empty($argv[1])) exit("Podaj nazwę pliku mapy!");

switch (exif_imagetype($argv[1])) {
	case IMAGETYPE_JPEG :
		$imType = 'Jpeg';
		break;
	case IMAGETYPE_PNG :
		$imType = 'Png';
		break;
	default :
		exit("Błąd! Nieobsługiwany typ pliku. Obsługiwane są JPG, PNG i BMP");
}
$callFunc = 'imageCreateFrom' . $imType;
$imSrc = $callFunc($argv[1]);
$height = imagesy($imSrc);

$imDst = imagecreatetruecolor(2*$height, $height);

imagecopy($imDst, $imSrc, 0, 0, $height, 0, $height, $height);
imagecopy($imDst, $imSrc, $height, 0, 0, 0, $height, $height);

$callFunc = 'image' . strtolower($imType);

$callFunc($imDst, substr_replace($argv[1], '-swapped', strrpos($argv[1], '.'), 0));

imagedestroy($imSrc);
imagedestroy($imDst);

?>
