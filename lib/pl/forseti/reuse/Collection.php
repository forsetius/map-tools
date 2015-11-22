<?php
namespace pl\forseti\reuse;

class Collection extends \ArrayObject
{
    protected $type;
    
    /**
     * Description
     * @param type collection contains only objects of that type
     * @return
     * @throws
     */
    public function __construct($type, $array = []) {
        $this->type = $type;
        parent::__construct($array);
    }
    
    /**
     * Description
     * @param value
     * @return
     * @throws
     */
    public function append($value) {
        $this->offsetSet(NULL, $value);
    }
    
    public function appendAll(array $arr) {
        foreach ($arr as $el)
            $this->append($el);
    }
    
    public function merge(array $arr) {
        foreach ($arr as $key=>$el)
            $this->offsetSet($key, $el);
    }
    
     /**
      * {@inheritDoc}
      * @see ArrayObject::offsetSet()
      */
    public function offsetSet($index, $newval) {
        $this->checkType($newval);
        parent::offsetSet($index, $newval);
    }

    
    public function getType()
    {
        return $this->type;
    }
 
    protected function checkType($var) {
        if (! $var instanceof $this->type) {
            $type = '`' . is_object($var) ? \get_class($var) . '` object' : \gettype($var) .'`';
            throw new LogicException("Object of type `{$this->type}` expected, $type found", LogicException::INVALID_TYPE);
        }
    }
    
    public function __toString()
    {
        return $this->type . 'Collection';
    }
    
    public function __call($func, $args)
    {
        if (\in_array($func, array('array_flip' )))
            throw new \LogicException("Tried to call `$func` function that would set incorrect types for elements of {$this->getType()} Collection", LogicException::BAD_METHOD_CALL);
        
        if (!is_callable($func) || substr($func, 0, 6) !== 'array_')
            throw new \LogicException("Function `$func` can't be called on collection", LogicException::BAD_METHOD_CALL);
        
        if (\in_array($func, array('array_fill', 'array_fill_keys', 'array_combine', 'array_map',
            'array_merge', 'array_merge_recursive', 'array_pad', 'array_push',
            'array_replace', 'array_replace_recursive', 'array_splice',
            'array_unshift', 'array_walk', 'array_walk_recursive'))) {
             
            throw new \LogicException("Function `$func` can set incorrect types for elements of {$this->getType()} Collection", LogicException::BAD_METHOD_CALL + LogicException::WARNING);
        }
            
        $result = call_user_func_array($func, array_merge(array($this->getArrayCopy()), $args));
            
        return $result;
    }
}

?>