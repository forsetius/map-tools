<?php
use pl\forseti\cli\TestTask;
use pl\forseti\cli\SyntaxException;

$l = new TestTask('l');
$l->setVarsOk(['3','0',3]);
$l->setVarsNok([['',SyntaxException::REQUIRED_VALUE], [-1, SyntaxException::REQUIRED_VALUE], ['error', SyntaxException::INVALID_VALUE], ['256', SyntaxException::INVALID_VALUE], ['2-', SyntaxException::INVALID_VALUE], ['+', SyntaxException::INVALID_VALUE], ['true', SyntaxException::INVALID_VALUE]]);
$l->setCases([['-s #s# -l @l@',0], ['-s #s# --level @l@',0], ['-s #s# --l #l#', SyntaxException::BAD_SYNTAX], ['-s #s# -level #l#', SyntaxException::BAD_SYNTAX]]);

return [$l];

?>
