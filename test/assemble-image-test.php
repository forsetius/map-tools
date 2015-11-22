<?php
use pl\forseti\cli\TestTask;

$s = new TestTask('s');
$s->setVarsOk(['mars','../test/mars']);
$s->setVarsNok(['nosuch'=>255, ''=>255, '!nosuch'=>255, '*'=>255, '/'=>255]);
$s->setCases(['-s @s@ -g @g@'=>0, '-s "@s@"'=>0, '--source @s@'=>0, '-s'=>255, '--s'=>255, '-source #s#'=>255]);

$l = new TestTask('l');
$l->setVarsOk(['','3','0',3]);
$l->setVarsNok(['-1'=>255, -1=>255, 7=>255, 'error'=>255, '256'=>255, '2-'=>255, '+'=>255, 'true'=>255]);
$l->setCases(['-s #s# -l @l@'=>0, '-s #s# --level @l@'=>0, '-s #s# --l #l#'=>255, '-s #s# -level #l#'=>255]);

return [$s, $l];

?>
