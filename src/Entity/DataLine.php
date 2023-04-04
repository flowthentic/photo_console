<?php namespace App\Entity;
use App\Entity\InputLine;

class DataLine extends InputLine
{
  protected $date, $time;
  public function __construct($args)
  {
    // load args that are the same for every line
    parent::__construct($args);
    // load args that are unique to data lines
    $this->cells[] = \DateTime::createFromFormat(parent::dateformat, $args[3])->setTime(0,0);
    $this->cells[] = (int)$args[4];
  }
}

?>
