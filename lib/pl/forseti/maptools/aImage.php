<?php
/**
 * @package forseti.pl\maptools
 */
namespace pl\forseti\maptools;

abstract class aImage
{
    protected static $library = 'Gd';
    
    /**
     * Set the graphics library to use
     *
     * @param string $library Choose from 'Gd', 'Imagemagick', 'Gmagick'
     * @throws \Exception if any other choice is made
     * @return void
     */
    public static function setLibrary($library)
    {
        $library = \ucfirst(\strtolower($library));
        if (! \in_array($library, array('Gd', 'Imagemagick', 'Gmagick')))
            throw new \Exception("Unsupported Image library: $library\n");
        
        static::$library = 'pl\forseti\maptools\\' . $library . 'Image';
    }
    
    /**
     * Factory method. Makes an instance of Image subclass.
     * Concrete type is chosen based on value of self::$library. Use aImage::setLibrary(string) to set it. 'Gd' is assumed.
     * @param mixed ...$args O to 2 arguments. If 0 - only create new object. If 1 - create new object and load the picture given in 1st parameter into it. If 2 - create new object and create new picture with dimensions given in 1st and 2nd parameter.
     * @throws \Exception If number of parameters is greater than 2
     * @return aImage
     */
    public final static function make(...$args) {
        $obj = new self::$library;
        
        switch (count($args)) {
            case 0: break;                          // tylko stwórz obiekt
            case 1: $obj->load($args[0]); break;
            case 2: $obj->create($args[0], $args[1]); break;
            default: throw new \Exception('Invalid number of parameters.');
        }
        return $obj;
    }
    
    
    /**
     * Image resource created by image library
     * @var resource
     */
    protected $image = null;
    
    /**
     * Create new image resource of dimensions $w x $h
     * @param integer $w Width
     * @param integer $h Height
     * @return void
     */
    abstract public function create($w, $h);
    
    /**
     * Load an image into object.
     * Takes image's filename and creates image resource stored in the object. If you overload this method use parent::Load($filename) at the beginning of your method.
     * @param string $filename Path and name of file to load
     * @return void
     * @throws \Exception if file not found
     */
    public function load($filename) {
        if(! file_exists($filename)) throw new \Exception("Incorrect map's filename. File $filename doesn't exist\n");
    }
    
    /**
     * Get the image resource
     * @return resource
     * @throws \Exception if no resource stored - either not created yet or already destroyed.
     */
    public function get() {
        if (\is_null($this->image)) throw new \Exception('No resource stored.');
        return $this->image;
    }
    
    /**
     * Store the image resource in the object
     * @param resource $imgRes
     * @return void
     * @throws \Exception if parameter is not Resource
     */
    public function set($imgRes) {
        if (! \is_resource($imgRes)) throw new \Exception('Passed parameter is '. \gettype($imgRes) .'  - should be '. aImage::$library .' resource.');
        $this->image = $imgRes;
    }
    
    /**
     * Get the image's width
     * @return integer Width
     */
    abstract public function getWidth();
    
    /**
     * Get the image's height
     * @return integer height
     */
    abstract public function getHeight();
    
    /**
     * Crop the image by specified number of pixels.
     * Cropped image replaces original one.
     * @param integer $l Number of pixels to the left to cut
     * @param integer $t Number of pixels to the top to cut
     * @param integer $r rumber of pixels to the right to cut
     * @param integer $b Number of pixels to the bottom to cut
     * @return void
     */
    abstract public function crop($l, $t, $r, $b);
    
    /**
     * Scale the image to dimentions given in arguments.
     * Scaled image replaces original one.
     * @param integer $w New width
     * @param integer $h New height
     * @return void
     */
    abstract public function scale($w, $h);
    
    /**
     * Copy a rectangular fragment of image onto another image.
     * @param integer $x X-coordinate of source point
     * @param integer $y Y-coordinate of source point
     * @param integer $w Source width
     * @param integer $h Source height
     * @param aImage $destImg Destination image
     * @param integer $dx Destination image's X-coordinate
     * @param integer $dy Destination image's Y-coordinate
     * @return aImage Destination image object
     */
    abstract public function copyTo($x, $y, $w, $h, $destImg, $dx = 0, $dy = 0);
    
    abstract public function copy($x, $y, $w, $h);
    
    public static function dump($res, $path, $quality = 9)
    {
        $class = self::$library;
        $class::dump($res, $path, $quality);
    }
    
    /**
     * Write image resource to specified file.
     * If you overload this method use parent::Load($filename) at the beginning of your method.
     * @param string $filename Path and name of file to write
     * @param string $type Type: 'jpeg' or 'png'
     * @param integer $quality Image's quality (for Jpegs) or compression (PNGs). Range (1..9) - for Jpegs *10=quality. Default: 9 (max)
     * @return boolean True if success, false otherwise
     * @throws \Exception if $type not in ('jpeg', 'png')
     * @throws \Exception if $quality not in range (1..9)
     */
    abstract public function write($path, $quality = 9);
    
    /**
     * Destroy the image resource and null it to free the memory
     * @return void
     */
    abstract public function destroy();
}
 ?>