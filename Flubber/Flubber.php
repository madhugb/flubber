<?php
namespace Flubber;

define('FLIB', dirname(__FILE__).'/');
define('FLVERSION', 2.0);

// Load the core functions
require_once 'Functions.php';

use Flubber\FLException as FLException,
    Flubber\Datastore as Datastore,
    Flubber\Request as Request,
    Flubber\Locale as Locale,
    Flubber\Session as Session,
    Flubber\Handlers as Handlers;

class Flubber {

    protected $request = null;

    function __construct($config) {

        try {

            if (!array_key_exists('app', $config) || !is_dir($config['app'])) {

                throw new FLException("App path is not sent.
                    Please send `app` as abslute path while invoking Flubber.
                    Example:
                    `Flubber\Flubber(array( 'app' => '/var/www/my_app/' );`",
                                        array( 'status' => 500));

            } else {

                define('APPROOT', $config['app']);

            }

            $this->config();

            // Load Locale strings
            Locale::autoload();

            // Load Datastore
            Datastore::init();

            // Load Session
            Session::init();

            // Load request
            Request::init();

        } catch (FLException $e) {

            echo $e->getmessage();

        } catch (\Exception $e) {

            echo $e->getmessage();

        }
    }

    /*
     * Add more configuration paths for internal use
     */
    private function config() {

        if (APPROOT) {

            define('CONFIG_PATH',   APPROOT.'config/');

            define('HANDLER_PATH',  APPROOT.'handlers/');

            define('VIEW_PATH',     APPROOT.'views/');

            define('LOCALE_PATH',   APPROOT.'config/locale/');

        } else {

            $mess = "`APPROOT` is not defined. Please specify the absolute path
                    for the directory where your application is located";
            throw new FLException($mess);

        }

        // include configuration
        if (!file_exists(CONFIG_PATH. 'config.php'))
            throw new FLException("Configuration file not found.
                    Make sure that you have config/config.php file.");
        require CONFIG_PATH. 'config.php';

        // Load handlers file
        if (!file_exists(CONFIG_PATH. 'urls.php'))
            throw new FLException("URL handlers not found.
                    Make sure that you have config/urls.php file.");
        require CONFIG_PATH. 'urls.php';

        // Register all urls for handler
        Handlers::register($urls);

        // Set timezone for the application
        if (defined('TIMEZONE')) {

            if (function_exists('date_default_timezone_set')) {

                date_default_timezone_set(TIMEZONE);

            }

        }

    }

    /*
     * Initialize Main application
     */
    public function start() {

        global $FLRequest;

        // Run module passing the request to get appropriate response
        try {
            if ($FLRequest->handler == null) {
                throw new FLException('Not Found', array('status' => 404));
            }
            $module = gethandler($FLRequest->handler);
            call_user_func_array(
                    array($module, $FLRequest->method), $FLRequest->params);

        } catch (FLException $e) {

            $FLRequest->exception = $e;
            $FLRequest->handler = 'Error';
            $error = null;

            if (file_exists(HANDLER_PATH.'/Error.php')) {
                $error = gethandler('Error');
            } else {
                $error = ( new ErrorHandler() );
            }

            $error->notify();

        } catch (\Exception $e) {
            echo $e->getmessage();
        } finally {
            return true;
        }
    }
}

?>