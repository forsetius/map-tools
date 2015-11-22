<?php
use pl\forseti\cli\TestTask;

$s = new TestTask('s');
$s->setVarsOk(['mars','../test/mars']);
$s->setVarsNok(['nosuch'=>255, ''=>255, '!nosuch'=>255, '*'=>255, '/'=>255]);
$s->setCases(['-s @s@ -g @g@'=>0, '-s "@s@"'=>0, '--source @s@'=>0, '-s'=>255, '--s'=>255, '-source #s#'=>255]);

$o = new TestTask('o');
$o->setVarsOk(['mars.png','mars.jpg','../test/mars.png']);
$o->setVarsNok(['noext'=>255, ''=>255, 'test.bmp'=>255, 'test.txt'=>255, '!badname.png'=>255, '*'=>255, '/'=>255]);
$o->setCases(['-s #s# -o @o@ -g @g@'=>0, '-s #s# -o "@o@"'=>0, '-s #s# --output @o@'=>0,
    '-s #s# -o'=>255, '-s #s# --o #o#'=>255, '-s #s# -output #o#'=>255]);
    
$g = new TestTask('g');
$g->setVarsOk(['gd', 'imagick']);
$g->setVarsNok(['imagemagick'=>255, ''=>255, '-'=>255, '*'=>255]);
$g->setCases(['-s #s# -g @g@'=>0, '-s #s# --gfx @g@'=>0, '-s #s# -g'=>255, '-s #s# --g #g#'=>255, '-s #s# -gfx #g#'=>255]);

return [$s, $o, $g];
?>