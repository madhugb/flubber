<?php
namespace Flubber;

use Twig_Environment, Twig_Loader_Filesystem,Twig_SimpleFilter;

// Load templating egine
global $twig_loader, $twig;

class Flubber_Twig_Environment extends Twig_Environment {

  /*
   * This exists so template cache files use the same
   * group between apache and cli
   */
  protected function writeCacheFile($file, $content) {
      if (!is_dir(dirname($file))) {
          $old = umask(0002);
          mkdir(dirname($file),0777,true);
          umask($old);
      }
      parent::writeCacheFile($file, $content);
      chmod($file,0777);
  }
}

$twig_loader = new Twig_Loader_Filesystem(VIEW_PATH.'templates');
$twig = new Flubber_Twig_Environment($twig_loader);

// Disble cache for now
// array( 'cache' => VIEW_PATH.'templates_cache')

$filter = new Twig_SimpleFilter('_s', '_s');
$twig->addFilter($filter);

class Response {

	public $status = 200;

	public $headers = array();

	public $csrf_check = TRUE;

	public $exception = null;

	public $view = null;

	public $template =  'error';

	public $data = array();

	function __construct($template, $data) {
		global $twig;
		$this->view = $twig;
		$this->template = $template;
		$this->data = $data;
		set_locale("en");
	}

	function prepare_content() {
		return $this->view->loadTemplate($this->template.'.html');
	}

	function set_status() {
		http_response_code($this->status);
	}

	function set_header($name, $value='') {
		$this->headers[$key] = $value;
	}

	function set_headers($headers=array()) {
		foreach($headers as $key => $value) {
			$this->set_header($key, $value);
		}
	}

	function render_headers($headers=array()) {
		foreach($this->headers as $key => $value) {
			header($name.": ".$value);
		}
	}

	function respond() {
		$this->set_status();
		$this->render_headers();
		$content = $this->prepare_content();
		echo $content->render($this->data);
		return true;
	}
}

?>