<?php namespace App\Entity;

// Object for handling individual files
class Photo
{
    public readonly Array $info;
    public readonly \DateTime $outputDate;
    public static \DateInterval $delta;
    public static String $prefix, $outputFormat;
    public static int $suffix;
    private static $finfo;
  
    public function __construct(private String $path)
    {
        if (!isset(self::$finfo))  // initialize finfo class
            self::$finfo = new \finfo();
    
        $fprops = array();
        //get the key-value pairs
        preg_match_all('/\W*([\w -]+)=?([\w: -]+)?[$\],]/',  
        //  using output string the finfo object
                   self::$finfo->file($this->path),
        // store matches and submatches in the array
                   $fprops);
        // store property names and their values as key value pairs
        $this->info = array_combine($fprops[1], $fprops[2]);
    
        if (isset($this->info['JPEG image data']))
        {
            $this->outputDate = \DateTime::createFromFormat('Y:m:d H:i:s', $this->info['datetime']);
        }
        else
        {
            $this->outputDate = new \DateTime();
            $this->outputDate->setTimestamp(filemtime($path));
        }
        $this->outputDate->add(self::$delta);
    }
    public function __toString()
    {
        $append = pathinfo($this->path, self::$suffix);
        return self::$prefix.$this->outputDate->format(self::$outputFormat).$append;
    }
}
