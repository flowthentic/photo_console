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
    $range = explode('-', trim($args[3]));
    $this->cells[] = \DateTime::createFromFormat(parent::dateformat, $range[0])->setTime(0,0);
    if (isset($range[1]))  // date_to if it was specified
      $this->cells[] = \DateTime::createFromFormat(parent::dateformat, $range[1])->setTime(0,0);
  }
  public function __toString()
  {
    // assign array of all records to the first element of selected array
    $selected[] = array_column(self::$input, 'cells');
    
    for ($col = 0; $col < 3; $col++) // use preg selection for first three cells
    {
      if ($this->cells[$col] == '\*' && $col < 2) // first two columns can match *
        continue; // skip to next column
      $pattern = '/^'.$this->cells[$col].'(\.|$)/';
      $selected[] = preg_grep($pattern, array_column($selected[0], $col));
    }
    // check the date conditions
    $selected[] = array_filter($selected[0], function($line)
    {
      $match = $line[3] >= $this->cells[3]; // if greater than from_date
      if (isset($this->cells[4])) // if to_date is set
        $match = $match && $line[3] <= $this->cells[4]; // value has to be lower
      return $match;
    });
    
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
