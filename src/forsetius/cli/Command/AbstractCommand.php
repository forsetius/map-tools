<?php
namespace forsetius\cli\Command;

use forsetius\reuse\GlobalPool as Pool;

abstract class AbstractCommand
{
    protected $cla;

    public function __construct()
    {
    	$claClass = Pool::getConf()->get('app:module:'. Pool::getModule(). ':cla');
        $this->cla = (new $claClass($this->setup()))->parse(); // CHECK
    }

    public function getCLA()
    {
        return $this->cla;
    }

    abstract public function execute();
    abstract protected function setup();
}
