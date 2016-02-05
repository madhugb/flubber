<?php
namespace Flubber;
/**
 *
 *  Basic Exception Class
 *
 *
 *  @Author  : Madhu Geejagaru Balakrishna <me@madhugb.com>
 *  @License : The MIT License (MIT)
 *  @Copyright : (c) 2013-2016 Madhu Geejagaru Balakrishna <me@madhugb.com>
 *  @Source : http://flubber.co
 *
 */
class FLException extends \Exception {

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