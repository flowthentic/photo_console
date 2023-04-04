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
    // stripe off aterix
    
    // load args that are unique to queries
    $range = explode('-', $args[3]);
    $this->date_from = $this->date_to = date_parse_from_format('d.m.Y', $range[0]);
    if (isset($range[1]))  // overwrite date_to if it is specified
      $this->date_to = date_parse_from_format('d.m.Y', $range[1]);
  }
  public function __toString()
  {
    $selected = array();
    $svar_col = array_column(self::$input, 'service_variation');
    //var_dump($svar_col);
    $csub_col = array_column(self::$input, 'category_subcategory');
    $rtyp_col = array_column(self::$input, 'response_type');
    
    
    
    
    for ($col = 0; $col < 3; $col++)
    {
      
    }
    
    $selected[] = $svar_col; // first is an array of all lines' keys
    $selected[] = preg_grep('/^1\.|$/', $svar_col);
    var_dump($selected);
    
    
    /*foreach ($line in self::$input)
    {
      $services_matched = preg_grep('/^\.|\w/',) ($line->$service_variation);
      var_dump($selected)
    }*/
    
    
    $selected = call_user_func_array('array_intersect', $selected);
  }
}

?>
