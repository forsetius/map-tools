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
		$this->addOption(new CLI\Option('s'));                     	// source file name
        $this->addOption(new CLI\Option('o', 'modified-?'));        // output file name
        $this->addOption(new CLI\Option('g', 'gd'));                // graphics library
        parent::__construct($options);
	}
	
	// TODO: Timer at -v 2
	public function postproc() {
		$arr = parent::postproc();
		
		// output file name: '?' zamienia na nazwę pliku źródłowego
		if (\strpos($this->o, '?') !== false)
		    \str_replace('?', $this->s, $this->o);
		
		// graphics library: GD, ImageMagick czy GMagick
		// TODO ImageMagick czy GMagick
		aImage::setLibrary($this->g);
		return $arr;
	}
}