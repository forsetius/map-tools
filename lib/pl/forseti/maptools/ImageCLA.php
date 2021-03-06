<?php
/**
 * @package forseti.pl\maptools
 */
namespace pl\forseti\maptools;
use \pl\forseti\cli as CLI;

class ImageCLA extends CLI\CLA
{
	/**
	 * @return void constructor
	*/
	public function __construct(array $options = array())
	{
	    $s = new CLI\Requisite('s');
	    $s->setValid(['class'=>'filepath'])->setAlias('source');
	    $s->setHelp('source-path', 'Path and filename to source image');
	    
	    $outputName = function($val)
	    {
	        $s = $GLOBALS['cla']->s;
	        if (\strpos($val, '?') !== false) {
	            $out = \pathinfo($val, PATHINFO_DIRNAME);
	            if (! empty($out)) $out .= DIRECTORY_SEPARATOR;
	            $out .= \str_replace('?', \pathinfo($s, PATHINFO_FILENAME), \pathinfo($val, PATHINFO_FILENAME)) .'.';
	            if (\strpos(\substr($val,\strrpos($val,DIRECTORY_SEPARATOR)), '.') === false) {
	                $out .= \pathinfo($s, PATHINFO_EXTENSION);
	            } else {
	                $out .= \pathinfo($val, PATHINFO_EXTENSION);
	            }
	            return $out;
	        }
	        return $val;
	    };
	    
	    $o = new CLI\Parameter('o', $GLOBALS['cfg']->defOutputImgName);
	    $o->setValid(['class'=>'dirpath'])->setAlias('output')->setTransform($outputName);
	    $o->setHelp('output-path', 'Path and filename to output image');
	    
	    $g = new CLI\Parameter('g', $GLOBALS['cfg']->defGfxLib);
	    $g->setValid(['set'=>['gd','imagick']])->setAlias('gfx');
	    $g->setHelp('library-name',<<<EOH
Graphics manipulation library to use. Only libraries installed on your system can be used.
Supported libraries are:
 - gd:      basic lib, uses somewhat less memory. Requires `php5-gd`
 - imagick: ImageMagick, uses more memory but produces better quality especially when scaling.
	        Resulting images tend to be smaller too. Requires `php5-imagick`
EOH
	    );
	    
        parent::__construct(array_merge([$s, $g, $o], $options));
	}
	
	public function postproc() {
		$arr = parent::postproc();
		
		// graphics library: GD, ImageMagick czy GMagick
		aImage::setLibrary($this->g);
		return $arr;
	}
}