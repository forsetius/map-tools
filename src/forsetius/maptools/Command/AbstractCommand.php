<?php
namespace forsetius\maptools\Command;

use forsetius\reuse\Benchmark;
use forsetius\maptools\ImageCLA;

abstract class AbstractCommand
{
    protected $bm;
    protected $cla;
    protected $conf;

    public function __construct($conf)
    {
        $this->bm = Benchmark::getInstance();
        $this->conf = $conf;
        $this->cla = (new ImageCLA($conf, $this->setup()))->parse();
    }

    public function getCLA()
    {
        return $this->cla;
    }

    abstract public function execute();
    abstract protected function setup();
}
