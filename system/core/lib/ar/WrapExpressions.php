<?php
namespace core\lib\ar;
/**
 * Class WrapExpressions 
 */
class WrapExpressions extends Expressions {
    public function __toString() {
        return ($this->start ? $this->start: '('). implode(($this->delimiter ? $this->delimiter: ','), $this->target). ($this->end?$this->end:')');
    }
}