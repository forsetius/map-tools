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
		$this->addArg(new CLI\Parameter('s'));                     	// source file name
        $this->addArg(new CLI\Parameter('o', 'modified-?'));        // output file name
        $this->addArg(new CLI\Parameter('g', 'gd'));                // graphics library
        parent::__construct($options);
	}
	
	// TODO: Timer at -v 2
	public function postproc() {
		$arr = parent::postproc();
		
		// output file name: '?' zamienia na nazwę pliku źródłowego
		if (\strpos($this->o, '?') !== false)
		    $this->o = \str_replace('?', $this->s, $this->o);
		
		// graphics library: GD, ImageMagick czy GMagick
		// TODO ImageMagick czy GMagick
		aImage::setLibrary($this->g);
		return $arr;
	}
}