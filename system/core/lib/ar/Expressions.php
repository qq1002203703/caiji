<?php
namespace core\lib\ar;
/**
 * Class Expressions, part of SQL.
 * Every SQL can be split into multiple expressions.
 * Each expression contains three parts: 
 * @property string|Expressions $source of this expression, (option)
 * @property string $operator (required)
 * @property string|Expressions $target of this expression (required)
 * Just implement one function __toString.
 */
class Expressions extends Base {
    public function __toString() {
        //if($this->operator == 'FROM')
        //var_dump($this->source.$this->operator. ' '.(AR::$prefix).$this->target);
        return $this->source. ' '. $this->operator. ' '. $this->target;
    }
}