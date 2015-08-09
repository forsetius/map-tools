<?php
ini_set('memory_limit','2048M');



$level = 4;
$sourcePath = '/home/celestia/extras/Sol/4 Mars/textures/hires/Mars/level' . $level . '/';
$targetImg = imagecreatetruecolor(pow(2,$level)*1024,pow(2,$level)*512);
$targetFile = 'map'.$level.'.png';
#for ($i=0; $i<2^(2*$level+1); $i++) {
for ($x=0;$x<pow(2,$level+1);$x++) {
	for ($y=0;$y<pow(2,$level);$y++) {
		$sourceImg = imagecreatefrompng($sourcePath . 'tx_'. $x .'_'. $y .'.png');
		echo($sourcePath . 'tx_'. $x .'_'. $y .'.png'. chr(13));
		#echo($sourceImg);

		imagecopy($targetImg, $sourceImg, 512*$x, 512*$y, 0, 0, 512, 512);
		imagedestroy($sourceImg);


	}
}
imagepng($targetImg, $targetFile, 9);
imagedestroy($targetImg);
?>
