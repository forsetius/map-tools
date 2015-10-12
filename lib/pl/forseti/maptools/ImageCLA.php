<?php
/**
 * @package forseti.pl\maptools
 */
namespace pl\forseti\maptools;
use \pl\forseti\cli as CLI;

class ImageCLA extends CLI\CLA
{
	public function __construct(array $options = array())
	{
	    $s = new CLI\Requisite('s');
	    $s->setValid(['class'=>'filepath'])->setAlias('source');
	    $s->setHelp('source-path', 'Path and filename to source image');
	    
	    $o = new CLI\Parameter('o', $GLOBALS['cfg']->defOutputImgName);
	    $o->setValid(['class'=>'filepath'])->setAlias('output');
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
	    
        parent::__construct(array_merge([$s, $o, $g], $options));
	}
	
	public function postproc() {
		$arr = parent::postproc();
		
		// output file name: '?' zamienia na nazwę pliku źródłowego
		if (\strpos($this->o, '?') !== false) {
		    $out = \pathinfo($this->o, PATHINFO_DIRNAME);
		    if (! empty($out)) $out .= DIRECTORY_SEPARATOR;
		    $out .= \str_replace('?', \pathinfo($this->s, PATHINFO_FILENAME), \pathinfo($this->o, PATHINFO_FILENAME)) .'.';
		    if (\strpos(\substr($this->o,\strrpos($this->o,DIRECTORY_SEPARATOR)), '.') === false) {
		        $out .= \pathinfo($this->s, PATHINFO_EXTENSION);
		    } else {
		        $out .= \pathinfo($this->o, PATHINFO_EXTENSION);
		    }
		    $this->o = $out;
		}
		// graphics library: GD, ImageMagick czy GMagick
		aImage::setLibrary($this->g);
		return $arr;
	}
}