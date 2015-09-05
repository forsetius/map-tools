<?php
/**
 * @package forseti.pl\maptools
 */
namespace pl\forseti\maptools;

class GdImage extends aImage
{
    public function create($w, $h)
    {
        $this->image = imagecreatetruecolor($w, $h);
    }
    
    public function load($filename)
    {
        parent::load($filename);
        switch (exif_imagetype($filename)) {
        	case IMAGETYPE_JPEG :
        		$imType = 'jpeg';
        		break;
        	case IMAGETYPE_PNG :
        		$imType = 'png';
        		break;
        	default :
        		 throw new CapabilityException("Unsupported file type. Supported types: Jpeg and PNG.", CapabilityException::UNSUPPORTED_FORMAT);
        }
        
        $callFunc = 'imagecreatefrom' . $imType;
        $this->image = $callFunc('./'.$filename);
    }

    public function getWidth()
    {
        return imagesx($this->image);
    }

    public function getHeight()
    {
        return imagesy($this->image);
    }

    public function crop($l, $t, $r, $b) {
        $w = $this->getWidth()-$l-$r;
        $h = $this->getHeight()-$t-$b;
        $this->image = imagecrop($this->image, array('x'=>$l, 'y'=>$t, 'width'=>$w, 'height'=>$h));
        
//         $tempImg = imagecreatetruecolor($w, $h);
//         imagecopy($tempImg, $this->image, 0, 0, $l, $t, $w, $h);
//         $this->image = $tempImg;
    }
    
    public function scale($w, $h)
    {
        $interpolation =  ($w > $this->getWidth()) ? IMG_BICUBIC_FIXED : IMG_SINC; // IMG_GENERALIZED_CUBIC, IMG_QUADRATIC
        $this->image = imagescale($this->image, $w, $h, $interpolation);
        
        $tempImg = imagecreatetruecolor($w, $h);
        imagecopyresampled($tempImg, $this->image, 0, 0, 0, 0, $w, $h, $this->getWidth(), $this->getHeight());
        $this->image = $tempImg;
    }

    public function copyTo($x, $y, $w, $h, $destImg, $dx = 0, $dy = 0)
    {
        imagecopy($destImg, $this->image, $dx, $dy, $x, $y, $w, $h);
    }

    public function copy($x, $y, $w, $h)
    {
        $destImg = \imagecreatetruecolor($w, $h);
        $this->copyTo($x, $y, $w, $h, $destImg);
        return $destImg;
    }
    
	public function convertToPalette()
	{
		
	}

	public static function dump($res, $path, $quality = 9)
	{
	    $type = \strtolower(\substr($path, \strrpos($path, '.')+1));
	    if (\in_array($type, array('jpg', 'jpeg')))  $type = 'jpeg';
        
	    if ($type == 'jpeg') $quality *= 10;
	    $saveFunc = 'image' . $type;
	    $saveFunc($res, $path, $quality);
	    imagedestroy($res);
	}
	
    public function write($path, $quality = 9)
    {
        GdImage::dump($this->image, $path, $quality);
    }

    public function destroy() {
    	\imagedestroy($this->image);
    	$this->image = null;
    }
    
}

 ?>
