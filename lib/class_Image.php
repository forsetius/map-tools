<?php

/**
 *
 */
class Image
{
    private $image;

    public function __construct($filename)
    {
        if(! file_exists($filename)) exit("Incorrect map's filename. File doesn't exist\n");
        switch (exif_imagetype($filename)) {
        	case IMAGETYPE_JPEG :
        		$imType = 'Jpeg';
        		break;
        	case IMAGETYPE_PNG :
        		$imType = 'Png';
        		break;
        	default :
        		exit("Error! Unsupported file type. Supported types: Jpeg and PNG.\n");
        }

        $callFunc = 'imageCreateFrom' . $imType;
        $this->image = $callFunc($filename);
    }

    public function get()
    {
        return $this->image;
    }

    public function getWidth()
    {
        return imagesx($this->image);
    }

    public function getHeight()
    {
        return imagesy($this->image);
    }

    public function scale($width, $height)
    {
        $tempImg = imagecreatetruecolor($width, $height);
        imagecopyresampled($tempImg, $this->image, 0, 0, 0, 0, $width-1, $height-1, $this->getWidth()-1, $this->getHeight()-1);
        $this->image = $tempImg;
    }

    public function write($path, $type = 'png', $img = '')
    {
        if ($img=='') $img = $this->image;
        if ($type == 'png') {
            imagepng($img, $path, 9);
        } elseif ($type == 'jpg') {
            imagejpeg($img, $path, 90);
        } else {
            throw new Exception("Unsupported image type $type. Only PNG and Jpeg are supported.");
        }
    }

    public function writeRect($x, $y, $w, $h, $path, $type = 'png') {
        $rect = imagecreatetruecolor($w, $h);
        imagecopy($rect, $this->image, 0, 0, $x, $y, $w, $h);
        $this->write($path, $type, $rect);
        imagedestroy($rect);
    }
}

 ?>
