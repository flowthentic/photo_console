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
    $this->cells[] = date_parse_from_format(parent::date_format, $args[3]);
    $this->cells[] = (int)$args[4];
  }
}

?>
