<?php
namespace pl\forseti\maptools\Image;
use pl\forseti\reuse\LogicException;
use pl\forseti\maptools\CapabilityException;

class ImagickImage extends AbstractImage
{
    public static function imageTypeFunction($extension) {
        switch ($extension) {
            case 'jpeg' :
            case 'jpg' :
                $imType = 'jpeg';
                break;
            case 'png' :
                $imType = 'png';
                break;
            default :
                throw new CapabilityException("Unsupported file type. Supported types: Jpeg and PNG.", CapabilityException::UNSUPPORTED_FORMAT);
        }
        return $imType;
    }

    public function create($w, $h)
    {
        $img = new \Imagick();
        $img->newImage($w, $h, 'none');
        $this->set($img);
    }

    public function load($filename)
    {
        parent::load($filename);
        self::imageTypeFunction(\strtolower(\pathinfo($filename, PATHINFO_EXTENSION)));
        $this->set(new \Imagick(realpath($filename)));
    }

    public function get()
    {
        return $this->image;
    }

    public function set($res)
    {
        if (! ($res instanceof \Imagick))
            throw new LogicException('Passed parameter is '. \gettype($res) .'  - should be '. AbstractImage::$library .' resource.', LogicException::INVALID_RESOURCE);

        $this->destroy();
        $this->image = $res;
    }

    public function getWidth()
    {
        return $this->image->getimagewidth();
    }

    public function getHeight()
    {
        return $this->image->getimageheight();
    }

    public function sampleColor($x, $y) {
        return $this->image->getImagePixelColor($x, $y)->getColor(true);
    }

    //TODO test, czy dobrze działa
    public function getColorRGBA($x, $y)
    {
        return \array_walk($this->sampleColor($x, $y), function(&$value, $key) {
                $value = \round($value * 256);
            });
    }

    public function getColorIndex($x, $y)
    {
        $color = $this->sampleColor($x, $y);
        return $color['a']*256*256*256 + $color['r']*256*256 + $color['g']*256 + $color['b'];
    }

    public function crop($l, $t, $r, $b)
    {
        $w = $this->getWidth()-$l-$r;
        $h = $this->getHeight()-$t-$b;
        $this->image->cropimage($w, $h, $l, $t);
    }

    public function scale($w, $h)
    {
        if ($w > $this->getWidth()) {
            $interpolation = \Imagick::FILTER_MITCHELL;
            $blur = 1.1;
        } else {
            $interpolation = \Imagick::FILTER_LANCZOS;
            $blur = 0.9;
        }

        $this->image->resizeimage($w, $h, $interpolation, $blur);
    }

    public function copyTo($x, $y, $w, $h, $destImg, $dx = 0, $dy = 0)
    {
        $tempImg = $this->image->getimageregion($w, $h, $x, $y);
        $destImg->compositeImage($tempImg, \Imagick::COMPOSITE_DEFAULT, $dx, $dy);
        $destImg->setImagePage(0, 0, 0, 0);
        $tempImg->destroy();
    }

    public function copy($x, $y, $w, $h)
    {
        return $this->image->getimageregion($w, $h, $x, $y);
    }

    //TODO deprecated
    public static function dump(&$res, $path, $isQuick = false, $isAlpha = false)
    {
        $format = self::imageTypeFunction(\strtolower(\pathinfo($path, PATHINFO_EXTENSION)));
        if ($format == 'png') {
            $format = ($isAlpha) ? 'png32' : 'png24';
            $compression = ($isQuick) ? 10 : 5;
        } else {
            //JPEG
            $format = 'jpeg';
            $compression = 95;
        }
        echo "test1\n";
        //$res->image->setFormat('PNG');
        echo "test2\n";
        //$res->image->flatten();
        echo "test3\n";
        //$res->image->setImageCompressionQuality($compression);
        echo "test4\n";
        $res->image->writeimage($format .':'. $path);
        echo "test5\n";
    }

    public function write($path, $isQuick = false, $isAlpha = false)
    {
        $format = self::imageTypeFunction(\strtolower(\pathinfo($path, PATHINFO_EXTENSION)));
        if ($format == 'png') {
            $format = ($isAlpha) ? 'png32' : 'png24';
            $compression = ($isQuick) ? 10 : 5;
        } else {
            //JPEG
            $format = 'jpeg';
            $compression = 95;
        }
        $this->image->setFormat($format);
        $this->flatten();
        $this->image->setImageCompressionQuality($compression);
        $this->image->writeimage($format .':'. $path);
    }

    public function destroy()
    {
        if ($this->image instanceof \Imagick)
            $this->image->clear();
    }

    private function flatten()
    {
        /**
         * @see https://github.com/mkoppanen/imagick/issues/45
         */
        try {
            if (method_exists($this->image, 'mergeImageLayers') && defined('Imagick::LAYERMETHOD_UNDEFINED')) {
                $this->imagick = $this->image->mergeImageLayers(\Imagick::LAYERMETHOD_UNDEFINED);
            } elseif (method_exists($this->image, 'flattenImages')) {
                $this->imagick = $this->image->flattenImages();
            }
        } catch (\ImagickException $e) {
            throw new \LogicException('Flatten operation failed', LogicException::FAULTY_LOGIC, $e);
        }
    }
}
