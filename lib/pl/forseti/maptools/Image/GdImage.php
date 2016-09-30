<?php
/**
 * @package forseti.pl\maptools
 */
namespace pl\forseti\maptools\Image;

use pl\forseti\reuse\LogicException;

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
        //TODO greyscale
        $this->set(imagecreatetruecolor($w, $h));
    }

    public function load($filename)
    {
        parent::load($filename);
        @$imgType = exif_imagetype($filename);
        if ($imgType === false)
            throw new CapabilityException("Unsupported file type. Supported types: Jpeg and PNG.", CapabilityException::UNSUPPORTED_FORMAT);

        $callFunc = 'imagecreatefrom' . self::imageTypeFunction($imgType);

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

    public function sampleColor($x, $y) {
        return $this->getColorIndex($x, $y);
    }

    public function getColorRGBA($x, $y)
    {
        $color = $this->getColorIndex($x, $y);
        return array('r'=>($color & 0xFF0000) >> 16,
                     'g'=>($color & 0x00FF00) >> 8,
                     'b'=>($color & 0x0000FF),
                     'a'=>($color & 0x7F000000) >> 24
                    );
    }

    public function getColorIndex($x, $y)
    {
        return imagecolorat($this->image, $x, $y);
    }

    public function crop($l, $t, $r, $b)
    {
        $w = $this->getWidth()-$l-$r;
        $h = $this->getHeight()-$t-$b;
        $tempImg = imagecrop($this->image, array('x'=>$l, 'y'=>$t, 'width'=>$w, 'height'=>$h));
        $this->destroy();
        $this->set($tempImg);
    }

    public function scale($w, $h)
    {
        // TODO: non-default interpolation options cause segfault $interpolation =  ($w > $this->getWidth()) ? IMG_BICUBIC_FIXED : IMG_SINC; // IMG_GENERALIZED_CUBIC, IMG_QUADRATIC
        $this->image = imagescale($this->image, $w, $h); //, IMG_CATMULLROM); // core dump with non-default interpolation modes TODO: GD fault or mine?
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

	public static function dump($res, $path, $isQuick = false, $isAlpha = false)
	{
	    $format = self::imageTypeFunction(\strtolower(\pathinfo($path, PATHINFO_EXTENSION)));
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
