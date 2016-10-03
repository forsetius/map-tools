<?php
namespace forsetius\reuse;

class SimpleSet implements \Countable, \Iterator
{
    protected $store;
    
    public function __construct($array = array())
    {
        $this->store = (array) $array;
    }
    
    public function add($val)
    {
        $this->checkValid($val);
            
        $c = $this->count();
        $this->store[$val] = 1;
        return ($c != $this->count());
    }
    
    public function addAll(array $arr)
    {
        $c = $this->count();
        foreach ($arr as $el) {
            $this->checkValid($el);
            $this->store[$el] = 1;
        }
        
        return ($c != $this->count());
    }
    
    public function clear()
    {
        $this->store = array();
    }
    
    public function contains($val)
    {
        return \array_key_exists($val, $this->store);
    }
    
    public function containsAll(array $arr)
    {
        foreach ($arr as $el) {
            if (! $this->contains($el))
                return false;
        }
        return true;
    }
    
    public function count ($mode = COUNT_NORMAL)
    {
        return \count($this->store, $mode);
    }
    
    public function current()
    {
        return key($this->store);
    }
    
    public function isEmpty()
    {
        return (\count($this->store) == 0);
    }
    
    public function key()
    {
        return false;
    }
    
    public function next()
    {
        next($this->store);
        return $this->current();
    }
    
    public function remove($val)
    {
        if ($this->contains($val)) {
            unset($this->store[$val]);
            return true;
        }
        return false;
    }
    
    public function removeAll(array $arr)
    {
        $c = $this->count();
        foreach ($arr as $el)
            if ($this->contains($el))
                unset($this->store[$el]);
        
        return ($c != $this->count());
    }
    
    public function retainAll(array $arr)
    {
        $c = $this->count();
        $nArr = array();
        foreach ($arr as $el)
            if ($this->contains($el))
                $nArr[$el] = 1;
        
        $this->store = $nArr;
        
        return ($c != $this->count());
    }
    
    public function rewind()
    {
        \reset($this->store);
    }
    
    public function toArray()
    {
        return \array_keys($this->store);
    }
    
    public function valid()
    {
        return \current();
    }
    
    protected function checkValid($val)
    {
        if (! (\is_int($val) || \is_string($val) || \is_bool($val)) )
            throw new \LogicException('SimpleSet cannot accept a value of '. \gettype($val),LogicException::INVALID_TYPE);
    }
}