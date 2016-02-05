<?php
namespace Flubber;
/**
 *
 *  Response Handler
 *
 *
 *  @Author  : Madhu Geejagaru Balakrishna <me@madhugb.com>
 *  @License : The MIT License (MIT)
 *  @Copyright : (c) 2013-2016 Madhu Geejagaru Balakrishna <me@madhugb.com>
 *  @Source : http://flubber.co
 *
 */
use Twig_Environment, Twig_Loader_Filesystem,Twig_SimpleFilter;

global $twig_loader, $twig;
class Flubber_Twig_Environment extends Twig_Environment {
    /*
    * This exists so template cache files use the same
    * group between apache and cli
    */
    protected function writeCacheFile($file, $content) {
        if (!is_dir(dirname($file))) {
            $old = umask(0002);
            mkdir(dirname($file), 0777, true);
            umask($old);
        }
        parent::writeCacheFile($file, $content);
        chmod($file, 0777);
    }
}

$twig_loader = new Twig_Loader_Filesystem(VIEW_PATH.'templates');
$twig = new Flubber_Twig_Environment($twig_loader);

// Disble cache for now
// array( 'cache' => VIEW_PATH.'templates_cache')

$filter = new Twig_SimpleFilter('_s', '_s');
$twig->addFilter($filter);

class Response {

    public $headers = array();

    public $csrf_check = TRUE;

    public $exception = null;

    public $view = null;

    public $template =  'error';

    public $data = array();

    function __construct($template, $data, $_meta=array()) {
        global $twig, $FlubberLocale;
        $this->view = $twig;
        $this->template = $template;
        $this->data = $data;
        $locale = "en";

        if (isset($_meta["locale"])) {
            $locale = $_meta["locale"];
        }

        if (isset($_meta["headers"])) {
            $this->set_headers($_meta["headers"]);
        }

        if (isset($_meta["headers"])) {
            $this->set_headers($_meta["headers"]);
        }

        $FlubberLocale->set_locale($locale);
    }

    function prepare_content() {
        return $this->view->loadTemplate($this->template.'.html');
    }

    function set_status($status) {
        http_response_code($status);
    }

    function set_header($name, $value='') {
        if (!in_array($name, $this->headers)) array_push($this->headers, $name);
        $this->headers[$name] = $value;
    }

    function set_headers($headers=array()) {
        foreach($headers as $key => $value) {
            $this->set_header($key, $value);
        }
    }

    function render_headers() {
        foreach($this->headers as $header => $value) {
            header($header. ": ". $value);
        }
    }

    function respond() {
        $this->render_headers();
        if ($this->template == 'JSON') {
            echo json_encode($this->data);
        } else if ($this->template == 'TEXT') {
            echo $this->data;
        } else {
            $content = $this->prepare_content();
            echo $content->render($this->data);
        }
        return true;
    }
}

?>