<?php
namespace forsetius\cli\Command;
use forsetius\reuse\GlobalPool as Pool;
use forsetius\reuse\FilesystemException as FSe;
use forsetius\cli\Argument\Parameter;

class Core extends AbstractCommand
{
	public function __construct()
	{
		Pool::setModule('_core');
		parent::__construct();
	}

    public function execute()
    {
    	$help = $this->getFile($this->cla->t);
    	foreach ($this->getTermSubsts() as $tag=>$subst) {
    		$help = str_ireplace($tag, $subst, $help);
    	}
    	echo $help . "\e[0m\n";
    }

    protected function getFile($sn)
    {
    	$helpFile = dirname($sn).'/help/'. substr(basename($sn),0,strripos(basename($sn), '.php')) . '.help';
    	if (file_exists($helpFile)) {
    		return \file_get_contents("$helpFile");
    	} else throw new FSe("Help file `$helpFile` not found", FSe::FILE_NOT_FOUND);
    }

    protected function getTermSubsts() {
    	return array(
    			'<b>'=>"\e[1m",
    			'</b>'=>"\e[21m",
    			'<i>'=>"\e[3m",
    			'</i>'=>"\e[23m",
    			'<u>'=>"\e[4m",
    			'</u>'=>"\e[24m",
    			'<code>'=>"\e[96m",
    			'</code>'=>"\e[39m",
    			'<cite>'=>"\e[37m",
    			'</cite>'=>"\e[39m",
    			'<h1>'=>"\e[93m\e[4m",
    			'</h1>'=>"\e[39m\e[24m",
    			'<h2>'=>"\e[1m\e[4m",
    			'</h2>'=>"\e[21m\e[24m",
    			'<h3>'=>"\e[4m",
    			'</h3>'=>"\e[24m",
    			'&gt;'=>'>',
    			'&lt;'=>'<',
    	);
    }

    protected function getMdSubsts() {
    	return array(
    			'<b>'=>'**',
    			'</b>'=>'**',
    			'<i>'=>'*',
    			'</i>'=>'*',
    			'<u>'=>'`',
    			'</u>'=>'`',
    			'<code>'=>'```',
    			'</code>'=>'```',
    			'<cite>'=>'',
    			'</cite>'=>'',
    			'<h1>'=>'#',
    			'</h1>'=>'',
    			'<h2>'=>'##',
    			'</h2>'=>'',
    			'<h3>'=>'###',
    			'</h3>'=>'',
    			'&gt;'=>'>',
    			'&lt;'=>'<',
    	);
    }


    protected function setup()
    {
    	$t = new Parameter('t', ($GLOBALS['argc'] > 1) ? $GLOBALS['argv'][1] : $GLOBALS['argv'][0]);
    	$t->setValid(['class'=>'alnum'])->setAlias('level');
    	$t->setHelp('topic', "Help topic. If omitted, help for whole module is printed.");
    	return [$t];
    }
}
