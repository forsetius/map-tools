<?php
namespace forsetius\cli\Argument;

class PositionalParameter extends AbstractArgument
{
	protected $index;

	public function __construct($index, $name, $default)
	{
		$this->index = $index;
		parent::__construct($name, $default);
	}

	public function addClAs() {

	}

}