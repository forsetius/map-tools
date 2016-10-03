<?php
namespace forsetius\cli;
class ProgressBar
{
    private $total;
    private $prefix;
    private $size;
    private $done;
    
    public function __construct($total, $prefix = '') {
        $this->total = $total;
        $this->size = 67-2*(floor(log10($total))+1) - strlen($prefix);
        $this->prefix = $prefix;
        $this->done = 0;
    }
    
    public function progress($progress = 1) {
        $this->done += $progress;
        $pb = ceil(($this->done / $this->total)*$this->size);
        $bar = '[' . ($pb > 0 ? str_repeat('=', $pb) : '');
        $bar .= str_repeat(' ', $this->size - $pb) . '] - '. ceil($this->done / $this->total*100) .'% - ' . $this->done .'/'. $this->total;
        echo "\e[0G". $this->prefix . $bar; // Note the \033[0G. Put the cursor at the beginning of the line
    }
}
