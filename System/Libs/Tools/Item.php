<?php
/*************************************************
 * Titan-2 Mini Framework
 * Item Pipe Library
 *
 * Author 	: Turan Karatuğ
 * Web 		: http://www.titanphp.com
 * Docs 	: http://kilavuz.titanphp.com
 * Github	: http://github.com/tkaratug/titan2
 * License	: MIT
 *
 *************************************************/
 namespace System\Libs\Tools;

 class Item
 {
     /**
      * The current value being handled
      *
      * @var mixed
      */
     protected $value;

     /**
      * A unique string that will be replaced with the actual value when calling the pipe method
      * with it.
      *
      * @var string
      */
     protected $identifier = '$$';

     /**
      * Item constructor
      *
      * @param mixed $value
      */
     public function __construct($value)
     {
         $this->value = $value;
     }

     /**
      * @param \Closure|string $callback
      * @param array ...$arguments
      *
      * @return \System\Libs\Tools\Item $this
      */
     public function pipe($callback, ...$arguments)
     {
         if (!in_array($this->identifier, $arguments, true)) {
             array_unshift($arguments, $this->value);
         } else {
             $arguments = array_map(function ($argument) {
                 return $argument === $this->identifier ? $this->value : $argument;
             }, $arguments);
         }

         // Call the piped method
         $this->value = $callback(...$arguments);

         // Allow method chaining
         return $this;
     }

     /**
      * Get the current value
      *
      * @return mixed
      */
     public function get()
     {
         return $this->value;
     }
 }
