<?php
namespace Flubber;

define('FLIB', dirname(__FILE__).'/');

// Load the core functions
require_once 'functions.php';

use Flubber\Datastore as Datastore,
	Flubber\Request as Request,
	Flubber\FLException as FLException;

class Flubber {

	 private $request = array( 'post' => array(), 'get' => array(),
								'put' => array(), 'delete' => array());

	function __construct() {
		Datastore::init();
		$this->request = new Request();
	}

	/*
	 * Initialize Main application
	 */
	public function start() {
		$response = false;

		// Run module passing the request to get appropriate response
		try {
			$module = gethandler($this->request);
			call_user_func_array(
				array($module, $this->request->method),
				$this->request->params);
		} catch (FLException $e) {
			$this->request->exception = $e;
			$this->request->handler = 'Error';
			$error = gethandler($this->request);
			$error->notify();
		} catch (\Exception $e) {
			print_r($e);
		} finally {
			return true;
		}
	}
}

?>