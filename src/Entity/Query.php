<?php namespace App\Entity;

class Query extends InputLine
{
  protected const any_sub = '.|$';
  
  public static $input;
  
  protected $date_from, $date_to;
  public function __construct($args)
  {
    // load args that are the same for every line
    parent::__construct($args);
    // escape dots for constructing preg_ patterns
    $this->cells = array_map('preg_quote', $this->cells);
    
    // load date conditions
    $range = explode('-', $args[3]);
    $this->cells[] = date_parse_from_format('d.m.Y', $range[0]); // date_from
    if (isset($range[1]))  // date_to if it was specified
      $this->cells[] = date_parse_from_format('d.m.Y', $range[1]);
  }
  public function __toString()
  {
    // assign array of all records to the first element of selected array
    $selected[] = array_column(self::$input, 'cells');
    
    for ($col = 0; $col < 3; $col++) // use preg selection for first three cells
    {
      $pattern = '/^'.$this->cells[$col].'(\.|$)/';
      $selected[] = preg_grep($pattern, array_column($selected[0], $col));
    }
    
    // stripe off all rows where any of the criteria haven't been met
    $selected = call_user_func_array('array_intersect_key', $selected);
    // we are interested only in time column with minutes
    $selected = array_column($selected, 4);
    
    if (count($selected))
      // calculate the average of response time
      return array_sum($selected) / count($selected);
    else return '-'; // if no records have been selected
  }
}

?>
