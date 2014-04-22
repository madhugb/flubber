<?php
/*
 *  Database class
 *
 *  Its a wrapper for sql 
 *   
 *  @Author  : Madhu G.B <madhu@madspace.me> 
 *  @License : The MIT License (MIT)  
 *  Copyright (c) 2013 Madhu G.B <madhu@madspace.me>
 *  
 */


class datastore
{

  public    $connection = null;
  protected $database   = null;  
  
  function __construct()
  {    
    if (SQLDRIVER == 'MYSQL')
    {
      try
      {
        $this->connection = mysql_connect(DBHOST, DBUSER, DBPASS);        
        mysql_select_db(DBNAME, $this->connection);
      }
      catch(Exception $e)
      {
        logger('Connection failed:', $e , true);
        echo "<h1>Error in connecting </h1><br><h2>Please try again later.</h2>";      
        exit;
      }
    }
    
    if (SQLDRIVER == 'PDO')
    {
      try
      {
        $cn = sprintf('mysql:host=%s;dbname=%s;',DBHOST, DBNAME);
        $this->connection = new PDO($cn, DBUSER, DBPASS, array( PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING ));
      }
      catch(PDOException $e)
      {
        logger('Connection failed:', $e , true);
        echo "<h1>Error in connecting </h1><br><h2>Please try again later.</h2>";      
        exit;
      }
    }    
  }


  function execquery($query, $returnid = null)
  {
    if (SQLDRIVER == 'MYSQL')
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

    if (SQLDRIVER == 'PDO')
    {
      $statement = $this->connection->prepare($query);
      $result    = $statement->execute();       
      if ( $result )
      {    
        if ($this->connection->lastInsertId())
        {       
          return $this->connection->lastInsertId();
        }

        if ($statement->rowCount())
        {        
          return $statement->rowCount();
        }
          
        return true;      
      }
      else
        return false;
    }
  }  

  function dbfetch($query, $options = array())
  {    
    $data   = array();
    if (SQLDRIVER == 'MYSQL')
    {
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
    
    if (SQLDRIVER == 'PDO')
    {
      $stmt = $this->connection->prepare( $query );
      $stmt->execute();
      $result = $stmt->fetchAll();    
      $stmt->closeCursor();
      
      // if option is set then
      if (count($options) > 0 )
      {
        $found = false;
        // if number of rows found key
        if (isset($options['found']))
        {
          $rows_query = sprintf("SELECT FOUND_ROWS() as found");
          $stmtr = $this->connection->prepare( $rows_query );
          $stmtr->execute();
          $rows_res = $stmtr->fetchAll();          
          $stmtr->closeCursor();
          if (isset($rows_res[0]) && $rows_res[0]['found'] > 0)
            $found = $rows_res[0]['found'];
        }
        return array("data" => $result, "found" => $found);
      }
      else
      {    
        return $result;
      }    
      return false;       
    }  
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
