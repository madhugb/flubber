<?php
/*
 *  Database class
 *
 *  @author: Madhu GB <madhu@madspace.me>
 *
 */


class datastore
{

  public    $connection = null;
  protected $database   = null;  
  
  function __construct()
  {    
    
  }

  function set_database( $dbname )
  {
    mysql_select_db($dbname, $this->connection);
  }

  function execquery($query, $returnid = null)
  {    
    $result = mysql_query($query);
    if ($result)
    {
      if (mysql_insert_id())
		    return mysql_insert_id();
	    if (mysql_affected_rows())
  	    return mysql_affected_rows();              
	    return true;      
    }
    else
      return false;
  }  

  function dbfetch($query, $options = array())
  {    
    $data   = array(); 
    $result = mysql_query($query);
    if ($result) 
    {
      while($raw = mysql_fetch_assoc($result))
      {
        $data[] = $raw;
      }

      // if option is set then
      if (count($options) > 0 )
      {
        $found = false;
        if(isset($options['found']))
        {
          $query = sprintf("SELECT FOUND_ROWS() as found");
          $found_res = mysql_query($query);          
          while($raw_found = mysql_fetch_assoc($found_res))
          {
            $found = $raw_found['found'];
          }
        }
        return array("data" => $data, "found" => $found);
      }
      else
      {
        mysql_free_result($result);       
        return $data;
      }
    } 
    return false;       
  }

  function log($argv)
  {
    if (is_array($argv))
      file_put_contents(DOCROOT.'/../log/log.txt', print_r($argv, true) . "\n", FILE_APPEND | LOCK_EX);
    else
      file_put_contents(DOCROOT.'/../log/log.txt', $argv . "\n", FILE_APPEND | LOCK_EX);
  }
  
  // Write memcache related codes here
  function dbinit()
  {
  
  }
  
  // Custom insert function
  function myinsert()
  {
	  
  }
 
}

?>
