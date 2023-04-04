<?php namespace App\Entity;

abstract class InputLine
{
  protected const date_format = 'd.m.Y';
  protected const any = array(null, null);
  
  public $service_variation, $category_subcategory;
  protected $response_type;
  
  protected function __construct($args)
  {
    // load args that are the same for every line
    //var_dump($args);
    $this->service_variation = $args[0];
    $this->category_subcategory = $args[1];
    $this->response_type = $args[2];
  }
}

?>
