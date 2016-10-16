<?php
namespace forsetius\cli\Argument;
use forsetius\cli\SyntaxException;

/**
 * Command-line parameter used to introduce some value.
 * Must have default that's used if user omits it.
 * If user specifies it, they must obligatorily provide value.
 * @author forseti
 *
 */
class Verb extends AbstractArgument
{
	public function __construct()
	{
		parent::__construct('command', null);
	}

	public function getValue()
	{
		if ($this->value === null)
			throw new SyntaxException('Required value for parameter "'. $this->name .'" not supplied', SyntaxException::REQUIRED_VALUE);

		return parent::getValue();
	}
	
	public function getHelp()
	{
		$paramName = (empty($this->help['param']) ? '' : "<u>&lt;{$this->name}&gt;</u>");
		return \sprintf("        %s\n%s", $paramName, $this->help['desc']);
	}
}