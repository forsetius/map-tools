<?php
/**
 * @package forseti.pl\maptools
 */
namespace pl\forseti\maptools;

class ImagickImage extends aImage
{

    public static function imageTypeFunction($extension) {
        
    }
    
    public function create($w, $h)
    {
        $img = new \Imagick();
        $this->set($img->newImage($w, $h, 'none'));
    }
 
    public function load($filename)
    {
        parent::load($filename);
        $this->set(new \Imagick(realpath($filename)));
    }
    
    public function get()
    {
        return $this->image;
    }
    
    public function set(\Imagick $res)
    {
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
    
    public function crop($l, $t, $r, $b)
    {
        $w = $this->getWidth()-$l-$r;
        $h = $this->getHeight()-$t-$b;
        $this->image->cropimage($w, $h, $l, $t);
    }
    
    public function scale($w, $h)
    {
        if ($w > $this->getWidth()) {
            $interpolation = FILTER_MITCHELL;
            $blur = 1.1;
        } else {
            $interpolation = FILTER_LANCZOS;
            $blur = 0.9;
        }

        $this->image->resizeimage($w, $h, $interpolation, $blur);
    }
    
    public function copyTo($x, $y, $w, $h, $destImg, $dx = 0, $dy = 0)
    {
        $this->image->getimageregion($w, $h, $x, $y);
        $destImg->compositeImage($this->image, \Imagick::COMPOSITE_DEFAULT, $dx, $dy);
        $destImg->setImagePage(0, 0, 0, 0);
    }
    
    public function copy($x, $y, $w, $h)
    {
        $this->image->getimageregion($w, $h, $x, $y);
    }

    public static function dump($res, $path, $quality = 9)
    {
        
    }
    
    public function write($filename, $type = 'png', $quality = 9)
    {
        // TODO Auto-generated method stub
    }
    
    public function destroy()
    {
        if ($this->image instanceof \Imagick)
            $this->image->clear();
    }
}