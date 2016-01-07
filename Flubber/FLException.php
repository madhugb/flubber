<?php

class FLException extends Exception {

    public $message = "";

    public $status_code = 500;

    function __construct($message, $data) {
        parent::__construct($message);
        $this->message = $message;
        if (array_key_exists('status', $data)) {
          $this->status_code = $data['status'];
        }

    }

}

?>