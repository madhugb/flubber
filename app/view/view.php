<?php

/**
 *
 *  View
 * 
 * 
 *  @Author  : Madhu G.B <madhu@madspace.me> 
 *  @License : The MIT License (MIT)  
 *  Copyright (c) 2013 Madhu G.B <madhu@madspace.me>
 *  
 */
 
class view
{  
  // View name 
  protected $viewname     = null;

  // Page Identifier for template
  protected $page_id      = null;
  
  // Mobile layout check
  protected $is_mobile    = false;

  // Page contents
  protected $page_content = null;

  // Page meta info
  protected $page_info    = null;

  // Debug mode flag
  protected $is_debug     = false;

  // Other options
  protected $options      = array();

  // Variables for template
  protected $var          = array();

  /*
   *  Construct, Init page information and contents
   */ 
  public function __construct($page_content, $page_info)
  {    
    $this->page_content = $page_content;
    $this->page_info    = $page_info;
    $this->is_mobile    = $page_info['is_mobile'];            
  }

  /*
   * Assign a variable to template variable
   */ 
  private function assign( $variable, $value = null )
  {
		if ( is_array( $variable ) )
			$this->var += $variable;
		else
			$this->var[ $variable ] = $value;
	}

  /*
   *  clear template variables
   */  
  function flush_vars( )
  {
    $this->var = array();
  }

  /*
   * Draw a template
   * replace variables
   * return the contents
   */   
  protected function draw( $tpl_name )
  {
    $contents = "";

    // get the output buffer    
    ob_start();

    // extract values from assigned variables
    extract( $this->var );
  
    // include the template file
    include VIEW_PATH .'templates/'. $tpl_name .".php";
    // get the output buffer content
    $contents = ob_get_contents();

    // end buffer
    ob_end_clean();
        
    $this->flush_vars();
      
    return $contents;    
	}
  
  /*
   * set Headers
   */ 
  private function set_headers()
  {
    $expires = 60*60;
    header("Content-Type: text/html; charset=UTF-8");    
    header("Pragma: public");
    header("Cache-Control: maxage=".$expires);
    header("Expires: " . gmdate('D, d M Y H:i:s', time()+$expires) . " GMT");    
  }

  /*
   *  HTML doctype
   */ 
  private function get_doctype()
  {
    return '<!DOCTYPE html>';
  }

  /*
   *  HTML start tag
   */ 
  private function get_page_start()
  {
    return '<html>';
  }

  /*
   *  HTML end tag
   */   
  private function get_page_end()
  {
    return '</html>';
  }
  
  /*
   *  HTML Meta tags
   */ 
  private function get_meta_tag_includes()
  {
    $meta_tag_str = "";
    $this->assign('page_title',            $this->page_info['page_title']);
    $this->assign('page_meta_description', $this->page_info['page_description']);        
    $meta_tag_str = $this->draw('_meta', true);
    return $meta_tag_str;
    
  }
  
  /*
   *  Include stylesheets
   */ 
  private function get_style_includes()
  {
    $style = "";
    if ($this->is_mobile)
    {          
      $style = $this->draw('_m_styles', true);
    }
    else
    {            
      $style = $this->draw('_styles',true);
    }
    return $style;
  }
  
  /*
   *  Include javascripts
   */ 
  private function get_script_includes()
  {
    $scr = "";
    if ($this->is_mobile)
    {
      $scr = $this->draw('_m_scripts',true);   
    }
    else
    {      
      $scr = $this->draw('_scripts',true);
    }
    return $scr;    
  }
  
  /*
   *  Include Google analytics
   */ 
  private function get_google_analytics_includes()
  {        
    return "";  
  }
  
  /*
   *  Add Page reference 
   */ 
  private function get_page_id_ref()
  {
    /*
     *  Page reference will contain DOMAIN name , Page ID, and PAGEDATA,
     *  This will be accessed by the client application 
     */ 
    return sprintf("<script type='text/javascript'> var DOMAIN= '%s', PAGE = '%s', PAGEDATA=%s;</script>", SITEURL, $this->page_id, json_encode($this->page_content));
  }
  
  /*
   *  Get HTML header block 
   *  Include meta tags, title, styles, javascripts and other basic things 
   *  Google analytics and other advanced things
   */ 
  private function get_header_block()
  {      
    $meta_tags = $this->get_meta_tag_includes();
    $styles    = $this->get_style_includes();
    $scripts   = $this->get_script_includes();
    $ga        = $this->get_google_analytics_includes();
    $page_ref  = $this->get_page_id_ref();
    
    $ga_ex     = "";
    $header    = sprintf("<head>\n%s\n%s\n%s\n%s\n%s\n%s\n</head>\n",
                 $ga_ex, $meta_tags, $styles, $scripts, $ga, $page_ref);    
    return $header;
  }

  /*
   * Footer contents
   */ 
  private function get_footer_block()
  {
    
  }
  
  /*
   *  Draw Core body template
   */
  public function get_core_body($canvas_data)
  {
    // Put core body
    $this->assign('canvas_data', $canvas_data);    
    $core_body = $this->draw("core_body", true);    
    return $core_body;
  }
  
  /*
   * html body contents from template
   */ 
  public function get_body_from_template($data)
  {
    $prefix = "";
    
    // If mobile device
    if ($this->is_mobile)
      $prefix = "m_";
      
    $page_body = "";
    try
    {
      // If data is failure
      if (isset($data['result']) && $data['result'] == 'failure')
      {
        $this->page_id = '_404';
        $this->assign('message', $data['error']);
        $page_data = $this->draw($prefix.$this->page_id, true);
        $page_body = $this->get_core_body($page_data);
      }
      else
      {
        $this->assign($data);        
        $page_data = "";        
        $page_data = $this->draw($this->page_id);        
        $page_body = $this->get_core_body($page_data);
      }    
    }    
    catch(Exception $err)
    {            
      $this->page_id = '_404';
      $this->assign('message', $err->getErrorMessage());
      $page_data = $this->draw($prefix.$this->page_id, true);
      $page_body = $this->get_core_body($page_data);      
    }
    return $page_body;    
  }

  /*
   * Print a page
   */ 
  public function show_page( $page_id , $options = array())
  { 
    $this->page_id = $page_id;
    $this->options = $options;
    
    $page  =  "";
    // get the core body first 
    $body  = $this->get_body_from_template( $this->page_content );

    // According to the body of the page body set other things

    // Get doc type 
    $page .= $this->get_doctype();

    // HTML start tag
    $page .= $this->get_page_start();

    // head block including scripts, styles, meta-tags etc
    $page .= $this->get_header_block();

    // include body
    $page .= $body;
    
    // Footer block
    $page .= $this->get_footer_block();

    // End HTML tag 
    $page .= $this->get_page_end();

    // Set page headers before printing
    $this->set_headers();
    
    // Show the page
    printf("%s", $page);
    return true;    
  }
  
}

?>
