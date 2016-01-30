<?php

namespace Flubber;
/**
 *
 *  Default error handler
 *
 *
 *  @Author  : Madhu Geejagaru Balakrishna <me@madhugb.com>
 *  @License : The MIT License (MIT)
 *  Copyright (c) 2013-2016 Madhu Geejagaru Balakrishna <me@madhugb.com>
 *
 */

use Flubber\BaseHandler as BaseHandler;

class ErrorHandler extends BaseHandler {

	function __construct() {
		// No CSRF, Because you dont want this to fail.
		parent::__construct(array('csrf_check' => false));
	}

	function get() {
		$this->set_status(404);
		$this->send_response('404 Not Found');
	}

	function notify() {
		$exc = $this->request->exception;
		$this->set_status($exc->status_code);
		$this->send_response($exc->message);
	}

}

?>