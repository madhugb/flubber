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
    Flubber\Session as Session;

// Load All Locale strings.
Locale::autoload();

// Load datastore
Datastore::init();

// Load Session
Session::init();

// Initialize request
Request::init();

class Flubber {

    protected $request = null;

    function __construct() { }

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