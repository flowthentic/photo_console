<?php namespace App\Entity;

abstract class InputLine
{
  protected const dateformat = 'd.m.Y';
  protected $cells;
  
  protected function __construct($args)
  {
    // load args that are the same for every line type
    $this->cells = array_slice($args, 0, 3);
  }
}

?>
