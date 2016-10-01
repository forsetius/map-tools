<?php
namespace pl\forseti\maptools\Command;

use pl\forseti\reuse\Benchmark;
use pl\forseti\maptools\ImageCLA;

abstract class AbstractCommand
{
    protected $bm;
    protected $cla;
    protected $conf;

    public function __construct($conf)
    {
        $this->bm = Benchmark::getInstance();
        $this->conf = $conf;
        $this->cla = (new ImageCLA(setup()))->parse();
    }

    public function getCLA()
    {
        return $this->cla;
    }

    abstract public function execute();
    abstract protected function setup();
}
