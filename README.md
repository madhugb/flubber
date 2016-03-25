#Flubber

Flubber 2.0 is built from ground up, while keeping the same principles of 1.0

------
##Create project
------

Download Flubber from `https://github.com/madhugb/flubber/archive/master.zip`

    cd ~/

    wget https://github.com/madhugb/flubber/archive/master.zip

After extracting the zip file, move `Flubber` folder to

    cd ~/flubber

    mv Flubber ~/.Flubber

To start working you need to run this

    php ~/.Flubber/setup.php --path=/var/www/example --url=example.com

It creates a folder structure for you to in `/var/www/example`.

Note : This needs you to have full write access to the path of the project and Flubber codebase

------
##Folder structure
------

```
your/application/path/
|
|----config/
|		|
|		|----locale/
|		|		|
|		|		|----en.ini
|		|		|
|		|		|----<some_lang>.ini
|		|
|		|----config.php
|		|
|		|----functions.php
|		|
|		|----urls.php
|
|----handlers/
|		|
|		|---- <HandlerName>.php
|
|
|----public/
|		|
|		|---- js/, css/, images/
|		|
|		|---- index.php
|
|----views/
		|
		|----templates/
				|
				|---- <base>.html
				|
				|---- <template>.html
```

------

##Configurations
------

To configure Flubber application you need to provide the following

`SITEURL`	- url without protocol (i.e http:// , https:// )

	Example:
		"demo.example.com"
		"example.com"
		"test.domain.example.com"

`HAS_SSL`	-  false | true

`SITEADMIN` - "admin@example.com"

`TIMEZONE` -  "Asia/Kolkata"

`SESSION_NAME` - "__ses"

`SESSION_EXPIRY` -  3600  // in seconds

`SESSION_IDLE` - 3600 // in seconds

`SESSION_AUTO_RENEW` - true | false

`LOGIN_URI` - path to login url if exists

	Example:
		"/login"
		"/app/login"
		"/"

`TOKEN_SECRET` - A long random string for csrf token secret
				make sure to put a long random string.

`TOKEN_EXPIRY` - 300 // in seconds


`DBTYPE` - "mysql" // for now only mysql is supported

`DBHOST` - "localhost"

`DBUSER` - "dbusername"

`DBPASS` - "dbpassword"

`DBNAME` - "databasename"

------

##Urls
------

`/config/urls.php` contains all url patterns for the application

it must contain a variable `$urls` as an array.

each index of url

	$urls = array(

		array(

			"<regex_pattern>", "HandlerName"

		),

		...

	);

For example:

	array(

		"/^login$/", "LoginHandler"

	)

which translates to if the url is `http://example.com/login`.

the request is passed to `LoginHandler`.

	array(

		"/^user\/(?P<user_id>[0-9]+)$", "UserHandler"
	)

which translates to if the url is `http://example.com/user/123`

the request is passed to `UserHandler` with an argument `$user_id = "123"`

------

##Handlers
------

You will be spending more time here; as your application logic is written here.

take a look at a sample `UserHandler` handler

`handlers/UserHandler.php`

	<?php

	use Flubber\BaseHandler as BaseHandler;

	class UserHandler extends BaseHandler {

		function __construct() {

			parent::__construct(array(

					"auth" => array( "get", "post"),

					"csrf_check" => false
				)
			);
		}

		function get() {

			$this->set_status(200);

			// Composre your response here

			$response = array( "message" => "hello");

			// Show `home` template

			$this->show_page("home", $response);
		}

		function post() {

			$data = $this->request->data["post"];

			// send the same data back

			$this->send_json($data);
		}

	}


Let us see how this handler work

------
####Class Structure
------

Every handler must be extended to `BaseHandler`
Apart from extending we must call the `parent constructor` in the constructor of
the handler.

function __construct() {

	$config = array();

	parent::__construct($config);

}

There are some things you can configure per handler those should be passed to
parent constructor in an array.

Configurations are as below

1. `auth` - this sets the authentication check for "get", "post", "put" requests

		$config["auth"] = array("post","put");

    which checks for authentication for **post** and **put** requests.
    If not authenticated it will redirect to login page
    for any other type of requests authentication will not be ckecked.

2. `csrf_check` - this boolean key enables or disables the checks for csrf token

    if the value is true then it checks csrf token in the following places

    a. `X-Csrf-Token` header

    b. `_csrf` in **post** or **put** data

------
####Accessing request data
------

To handle different request methods you need to have function by
the name of request methods. Allowed methods are `GET`, `POST`,
`PUT`, `DELETE`,`OPTIONS`.

To handle `GET` request you need to have `function get(){ }` in the
handler (note that the function names are in lowercase).

In every handler you can access `$this->request` object which contains
all request data.

1. `$this->request->get("key")`  - value of `GET` argument _key_.

2. `$this->request->post("key")` - value of `POST` data _key_.


Or you can access `$this->request->data`  directly which has


	array(

		"get" => array(

			"get_data_key_1" => "get_data_value_1"

			...
		),

		"post" => array(

			"post_data_key_1" => "post_data_value_1"

			...
		),

		"put" => array(),

		"files" => array()
	)


There is a special **_params_** key in requests.

This can be accessed from `$this->request->params` will get

all the values from url path based on **_named regex_** match.

For example "/^user\/(?P<user_id>[0-9]+)$" will match _http://example.com/user/123_

and `$this->request->params["user_id"]` contains value _123_

also you can access this from function argument like below

	function get($user_id=false) {
		// here $user_id will contain 123

	}

------
####Response Handling
------

You can respond by showing a template file or sending a json response.

to show a template page

```php
$this->show_page("template_name", $data);
```

where _$data_ is array of data to be used in template

to send a json response

```php
$this->send_json($data);
```

where _$data_ is array which can be json encoded.

find more detail about Templates  below


------
####Headers
------

To access request headers
```php
$this->request->headers;
```

To respond with headers the below functions will help
```php
$this->set_status(200); // sets status to "200 OK"

$this->headers["X-My-Custom-Header"] = "Some Value"; // will sets the header
```

------
####Session Handling
------

Flubber Session is identified based on Cookie. Cookie name can be
changed from config `SESSION_NAME`

To start a session
```php
$this->session->start();
```

One must set a session identifier `uid` after starting session
```php
$this->session->set("uid", "<unique id for the user>");
```
To destroy a session
```php
$this->session->destroy();
```

------
##Templates
------

Flubber uses `Twig` templating library for rendring html templates

although this is bigger (~500Kb) than actual framework size (~200Kb). It made

lot of sense to just include this instead of writing one more. However someday

we will have a lighter solution.

Flubber uses a wrapper method to Twig the APIs are listed below

As we have shown before to show a template page you can call

```php
$this->show_page("<template file name>", $response);
```
Read Twig documentation for more details

------
##Localization
------

language files are located in `config/locale/` directory of your app

each language should have a dedicated `ini` file, which should contain all the strings

for example :

```
;en.ini
hello="Hello World!"
flubber="Flubber"
flubber_url="http://Flubber.co"
```

To set a locale for your handler in constructor

```php
function __construct() {

	...

	// Get locale from session / cookie /  db or hard code
	//  $mylocale = "en";
	$this->locale = $mylocale;
}

```