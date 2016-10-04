<?php
namespace forsetius\maptools\Command;

use forsetius\maptools\ImageCLA;

abstract class AbstractCommand
{
    protected $cla;

    public function __construct()
    {
        $this->cla = (new ImageCLA($this->setup()))->parse();
    }

    public function getCLA()
    {
        return $this->cla;
    }

    abstract public function execute();
    abstract protected function setup();
}
