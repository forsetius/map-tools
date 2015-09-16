<?php
/**
 * @package forseti.pl\maptools
 */
namespace pl\forseti\maptools;

use pl\forseti\reuse\LogicException;
use pl\forseti\cli\SyntaxException;
 
class GdImage extends aImage
{
    public static function imageTypeFunction($extension) {
        switch ($extension) {
            case IMAGETYPE_JPEG :
            case 'jpeg' :
            case 'jpg' :
                $imType = 'jpeg';
                break;
            case IMAGETYPE_PNG :
            case 'png' :
                $imType = 'png';
                break;
            default :
                throw new CapabilityException("Unsupported file type. Supported types: Jpeg and PNG.", CapabilityException::UNSUPPORTED_FORMAT);
        }
        return $imType;
    }
    
    /**
     * Get the image resource
     * @return resource
     * @throws LogicException if no resource stored - either not created yet or already destroyed.
     */
    public function get() {
        if (\is_null($this->image))
            throw new LogicException('No resource stored.', LogicException::INVALID_RESOURCE);
    
        return $this->image;
    }
    
    /**
     * Store the image resource in the object
     * @param resource $imgRes
     * @return void
     * @throws LogicException if parameter is not Resource
     */
    public function set($imgRes) {
        if (! \is_resource($imgRes))
            throw new LogicException('Passed parameter is '. \gettype($imgRes) .'  - should be '. aImage::$library .' resource.', LogicException::INVALID_RESOURCE);
    
        $this->destroy();
        $this->image = $imgRes;
    }
    
    public function create($w, $h)
    {
        $this->set(imagecreatetruecolor($w, $h));
    }
    
    public function load($filename)
    {
        parent::load($filename);
        $callFunc = 'imagecreatefrom' . self::imageTypeFunction(exif_imagetype($filename));

        $this->set($callFunc('./'.$filename));
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
        
/* older implementation
        $tempImg = imagecreatetruecolor($w, $h);
        imagecopy($tempImg, $this->image, 0, 0, $l, $t, $w, $h);
        $this->image = $tempImg;
 */
    }
    
    public function scale($w, $h)
    {
        $interpolation =  ($w > $this->getWidth()) ? IMG_BICUBIC_FIXED : IMG_SINC; // IMG_GENERALIZED_CUBIC, IMG_QUADRATIC
        $this->image = imagescale($this->image, $w, $h, $interpolation);
        
/* older implementation
        $tempImg = imagecreatetruecolor($w, $h);
        imagecopyresampled($tempImg, $this->image, 0, 0, 0, 0, $w, $h, $this->getWidth(), $this->getHeight());
        $this->image = $tempImg;
 */
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

	public static function dump($res, $path, $format, $isQuick = false, $isAlpha = false)
	{
	    if ($format == 'png') {
	        $quality = ($isQuick) ? 1 : 0;
	        \imagealphablending($res, !$isAlpha);
	        \imagesavealpha($res, $isAlpha);
	    } else {
	        // JPEG
	        $quality = 95;
	    }
	    $saveFunc = 'image' . $format;
	    $saveFunc($res, $path, $quality);
	}
	
    public function write($path, $isQuick = false, $isAlpha = false)
    {
        GdImage::dump($this->image, $path, $isQuick, $isAlpha);
    }

    public function destroy()
    {
    	if (\is_resource($this->image))
    	    \imagedestroy($this->image);
    	
    	$this->image = null;
    }
    
}

 ?>
