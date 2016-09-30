<?php
use pl\forseti\cli\TestTask;
use pl\forseti\maptools\CapabilityException;
use pl\forseti\cli\SyntaxException;
use pl\forseti\reuse\FilesystemException;

$s = new TestTask('s');
$s->setVarsOk(['mars','../assemble-image/mars']);
$s->setVarsNok([['nosuch',FilesystemException::FILE_NOT_FOUND], ['',SyntaxException::REQUIRED_VALUE], ['!nosuch',SyntaxException::INVALID_VALUE], ['*',SyntaxException::BAD_SYNTAX], ['/',SyntaxException::INVALID_VALUE]]);
$s->setCases([['-s @s@ -g @g@',0], ['--source @s@',0], ['-s',SyntaxException::REQUIRED_VALUE], ['--s',SyntaxException::BAD_SYNTAX], ['-source #s#',SyntaxException::BAD_SYNTAX]]);

$o = new TestTask('o');
$o->setVarsOk(['mars.png','mars.jpg','../assemble-image/mars.png']);
$o->setVarsNok([['noext',CapabilityException::UNSUPPORTED_FORMAT], ['',CapabilityException::UNSUPPORTED_FORMAT], ['test.bmp',CapabilityException::UNSUPPORTED_FORMAT], ['test.txt',CapabilityException::UNSUPPORTED_FORMAT], ['!badname.png',SyntaxException::INVALID_VALUE], ['*',SyntaxException::BAD_SYNTAX], ['/',SyntaxException::INVALID_VALUE]]);
$o->setCases([['-s #s# -o @o@ -g @g@',0], ['-s #s# -o @o@',0], ['-s #s# --output @o@',0],
    ['-s #s# -o',CapabilityException::UNSUPPORTED_FORMAT], ['-s #s# --o #o#',SyntaxException::BAD_SYNTAX], ['-s #s# -output #o#',SyntaxException::BAD_SYNTAX]]);
    
$g = new TestTask('g');
$g->setVarsOk(['gd', 'imagick']);
$g->setVarsNok([['imagemagick',CapabilityException::UNSUPPORTED_LIBRARY], ['',SyntaxException::REQUIRED_VALUE], ['-',CapabilityException::UNSUPPORTED_LIBRARY], ['*',SyntaxException::BAD_SYNTAX]]);
$g->setCases([['-s #s# -g @g@',0], ['-s #s# --gfx @g@',0], ['-s #s# -g',SyntaxException::REQUIRED_VALUE], ['-s #s# --g #g#',SyntaxException::BAD_SYNTAX], ['-s #s# -gfx #g#',SyntaxException::BAD_SYNTAX]]);

return [$s, $o, $g];
?>