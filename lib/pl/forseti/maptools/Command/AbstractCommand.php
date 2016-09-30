<?php
/**
 * @package forseti.pl\maptools
 */
namespace pl\forseti\maptools\Command;

abstract class AbstractCommand
{
    protected $cla;
    protected $conf;

    public function __construct($conf)
    {
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
