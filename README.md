Flubber
===========

Introduction
-------------------

Flubber is a light weight flexible framework based on MVC design pattern. It is designed from ground-up. 


Its designed to be 
 
 * **Compact**: Basic skeliton is ~300kb 
 
 * **Scalable**: Database scheme, CDN support
 
 * **Easy to deploy**: No installation required, just copy-paste
 
 * **Multi Purpose**: Design wide range of apps starting from Todo, CMS to a sophisticated web product
 
 * **Multiple platforms**: Multi platform support built-in
 
 * **Localized**: Localization is built-in 
 
 * **Customizable**: Highly customizable because of less dependency
 
 * **Extendable**: Extend the behaviour of the app with easy Extensions or build your own extensions.


 visit [http://flubbermvc.com](http://flubbermvc.com) for more info and demo
 
 
-----------------------------------


**Note**: It may not be simple to understand entire framework, its still under constant iterations. So It may look like its not developer friendly. There are many aspects of Framework which are missing, like exception handling, refined template design.  


-----------------------------------

Architecture
------------

There are different sides to Flubber. You can customize it to behave in different ways according to your requirements.

 * **Server heavy**: Simple Template based html rendering. Ideal if you want to keep all decisions on server side.
 
 * **Client heavy**: jQuery based single page application. In client heavy applications, you create dynamic pages which are rendered on the fly by jQuery.
                     It allows you to create highly interactive applications which run extremely fast.
 
 * **Mixed**: Use Template based rendering with jQuery for UX enhancement. 

Its very important to understand the individual components of the framework to customize it according to your needs.


But before that let us look at the directory structure

     <your-app-folder>
       |
       |----- app/
       |       |
       |       |----- config/
       |       |        |
       |       |        |----- locale/
       |       |        |        |
       |       |        |        |----- en.php     
       |       |        |        :
       |       |        |        :
       |       |        |        |----- some-language.php       
       |       |        |
       |       |        |----- config.php
       |       |        |
       |       |        |----- locale.php
       |       |        |
       |       |        |----- settings.php
       |       |        |
       |       |        |----- urls.php
       |       |        
       |       |----- controller/
       |       |        |
       |       |        |----- controller.php       
       |       | 
       |       |----- model/
       |       |        |
       |       |        |----- model-a.php
       |       |        :
       |       |        :
       |       |        |----- model-n.php
       |       |
       |       |----- view/
       |       |        |
       |       |        |----- templates/
       |       |        |         |
       |       |        |         |----- template-a.php
       |       |        |         :
       |       |        |         :
       |       |        |         |----- template-n.php
       |       |        |       
       |       |        |----- view.php      
       |       |
       |       |----- app.php
       |
       |----- db/
       |
       |----- html/
       |       |    
       |       |----- css/
       |       |       |    
       |       |       |----- basic.css
       |       |       |
       |       |       |----- styles.css
       |       |       :
       |       |       :
       |       |       |----- custom-css.css         
       |       |
       |       |----- images/
       |       |    
       |       |----- js/
       |       |       |    
       |       |       |----- app.js
       |       |       | 
       |       |       |----- jquery.js
       |       |    
       |       |----- index.php    
       |       |
       |       |----- .htaccess    
       |       
       |----- lib/
       |       |    
       |       |----- datastore/
       |       |       |     
       |       |       |----- datastore.php
       |       |    
       |       |----- functions.php
       |       |    
       |       |----- lib.php
       
    
  

       



A Todo App
-------------------------------

 It is recommended to create a simple Todo app using Flubber, to understand the basic understanding of it.


 
 
 **Database**
 
Create a `todo` database and create todo table as shown below
 
     CREATE TABLE todo 
     (
       `id`          bigint(11) primary key auto_increment,
       `created`     datetime,
       `modified`    datetime,
       `title`       varchar(512),
       `description` text,
       `status`      enum('PENDING','COMPLETED')
     );
 
 
 
 **Config**
 
Open `app/config/settings.php` file and change it like below

     <?php  
     
       define('DBHOST', 'localhost');   /* your db host name */
       define('DBUSER', 'MY_DB_USER');  /* your db user name */
       define('DBPASS', 'MY_PASS');     /* your db password */
       define('DBNAME', 'todo');        /* your database name */      
       
     ?>
    
    
 **URLs**
 
There should be a URL for our Todo app, so lets create required urls. 
 
Open `app/config/urls.php` file and add the following urls

     <?php
      
        $urls = array
        (
          ...
          array("#^todo$#",array("_action"=>"list_todo"))
        );
     ?>
    
    

**Model**

For our Todo app to work you need a model. Here is how you do it.
 Create a file named `todo.php` in `app/model/` which contains the following code.

     <?php
     
      // Include library      
      include_once LIB_PATH. 'lib.php';
      
      // Todo is the name of our model
      class todo extends lib
      {
        function __construct()
        {
        
        }
      }
      
     ?>
    
    
Create a function `get_tasks`

    public function get_tasks( $request )
    {
      /* 
        Query to get from todo table that we created above         
      */
      $tasks_query = sprintf("select * from todo order by status ASC, id DESC");
      
      /* Fetch from database */
      $tasks_list  = $this->dbfetch( $tasks_query );
      
      /*
        return the result to controller
        remember its mandatory to send `result` as success or failure
        `data` contains actual response.
      */
      
      $result      = array( 'result' => 'success', 
                            'data'   => $tasks_list );
      return $result;
    }

We can add multiple functions for our Todo list like below

*create a new task*:

    public function create_task($request)
    {
        $data = $request['post'];
        $created = date("Y-m-d H:i:s");
        $task_query = sprintf("insert into todo(`title`,`status`,`created`) values('%s','PENDING','%s')", 
            $data['task_title'],$created);     
        $res = $this->execquery( $task_query );
        if ($res)
        {
          array( 'result' => 'success', 'data' => $res );
        }
        else
          array( 'result' => 'failure', 'message' => _s('cannot_create_task') );
    }

*update an existing task*

    public function update_task($request)
    {
        $data = $request['post'];
        $field = '';
        if (isset($data['status']))
            $field = sprintf("`status`='%s'", $data['status']);     
        $task_query = sprintf("update todo set %s where id=%s",
            $field, $data['task_id']);     
        $res = $this->execquery( $task_query );
        if ($res)
        {
           array( 'result' => 'success');
        }
        else
        {
           array( 'result' => 'failure', 
                  'message' => _s('cannot_create_task') );
        }
    }

**View**

Now lets create a `view` to display our `tasks`
To create a `view` we need to create a `template` file 
  
Create a file `todo_list.php` in `app/view/templates/` and add the following contents..

    <div class="row-fluid">
        <div class="span12">  
            <!-- _s function is a PHP function to render strings in multiple languages -->
            <h1><? echo _s('todo_list_header'); ?></h1>

            <div class="row-fluid">

              <!-- New task block -->
              <div class="row-fluid">
                  <div class="span8">
                      <input class="new-task-text" type="textbox" id="new-task-text" autocomplete="off" />
                  </div>        
                  <div class="span4">
                      <input type="button" id="create-task" value="Create">
                  </div>        
              </div>
              <!-- New task block ends -->

              <!-- Empty task message block -->
              <? 
              if (count($data) == 0)
              {
              ?>
              <h3><? echo _s('empty_tasks_list_message');?></h3>                  
              <!-- Empty task message block ends -->

              <!-- tasks list block -->
              <?
              }
              else        
              {    
              ?>
              <!--  All done message -->
              <?
                  $counter = 0;          
                  foreach($data as $task)
                  {
                      $counter += ($task['status'] == 'COMPLETED' ? 1: 0);
                  }
              ?>
              <?
                  if($counter == count($data))
                  {
              ?>
              <h3> <? echo _s('all_tasks_done_message');?> </h3>
              <!--  All done message ends-->
              <?}?>                 
              <!--  list of tasks -->
              <form name="todo-list<?echo mktime();?>">
              <?
                foreach($data as $task)
                {
              ?>
                  <div class="row-fluid todo-row" id="row-<? echo $task['id']; ?>">
                      <div class="span12">
                          <input id="task-<? echo $task['id']; ?>" class="complete-task" type="checkbox" <? echo ($task['status'] == 'COMPLETED' ? 'checked="checked"' : ''); ?> />
                          <span class="task-name <?echo ($task['status'] == 'COMPLETED' ? 'task-done': ''); ?>">
                              <? echo $task['title']; ?>
                          </span>
                      </div>
                  </div>              
              <?}
              }
              ?>
              </form>
              <!-- tasks list block ends-->
            </div>    
        </div>  
    </div>



In the template above you can see the PHP code inside HTML template.

  Response contains `result` and `data` here we will be using `data` which is nothing but the list of todo tasks from our model.
  
  In the *controller* section you will see how to pass this value to view.
  
 **Localization** 
  
  We can render our Todo app in multiple languages, see the `_s()` function call inside the template, passed string will be replaced by the appropriate string of the current language ( by default English). However this is not mandatory, we can use plain strings anywhere in the HTML code.   

  Lets add some strings that we mentioned in above template.
  
  open `app/config/locale/en.php` and add the following string.

    <?php
        $strings = array
        (    
            ...
            
            "todo_list_header" => "My Todo List",
            
            "empty_tasks_list_message" => "It seems that you just stated organizing yourself.. Creat tasks now!!",
            
            "all_tasks_done_message" => " Everything is done, Good job!!",
            
            "cannot_create_task" => "Oops, Failed to add your Task, Try again.."
            ...
        );
    ?>

We can create our own file for language support in the same format. Set the language just by calling `set_locale()` anywhere in the application. We can also change it in `config.php`.


**Controller**

Like in any *MVC design pattern*, without `Controller` application cannot do anything. To render any view or to request any model, we need to add a controller condition. In **Flubber**, controller checks the `_action` key in the request and based on which it takes decission.

Lets add multiple conditions inside `take_action` function to do operations like *list*, *create*,*update* 

Open `app/controller/controller.php` and add the below conditions inside the switch condition.

    function take_action()
    {
        switch( $this->_action )
        {                
            ...            
            
            /*
              Get the list of tasks in todo
            */
            case 'todo_list':
                /* Get model Object */
                $model = $this->getmodel('todo');
                /* Call appropriate function */
                $data  = $model->get_tasks($this->_request); 
                
                /* Pass data to view */
                $view =  $this->getview($data);
                /* Render page to client */
                return $view->show_page('todo_list');
            break;
            
            /*
              Add a new task to todo
            */
            case 'new_task':
                $model = $this->getmodel('todo');
                $data  = $model->create_task($this->_request);
                /*
                  Donot print the page, return the array
                  this will be returned as JSON response
                */
                return $data;        
            break;
           
            /*
              Update a task in todo
            */
            case 'update_task':
                $model = $this->getmodel('todo');
                $data  = $model->update_task($this->_request);
                return $data;        
            break;
            
            ...
        }
    }


Client-side
----------------

So far we have done the following things:

* Changing configuration 
* Connecting your database to Flubber
* Creating URLs
* Creating Todo Model 
* Adding new Template for view
* Localization 
* Linking all to add a condition in Controller

  
But its not done yet, In this we cannot add any new todo list or mark a task as done. To add these functionality lets jump into Client side. 

Flubber comes with a Client side library, which in itself is a micro framework written using jQuery. It is so powerful that we can easily create a single page application without server side  templates, Its so simple that we can create a UX enhancement for every page of our application. 

This is inspired by [Ben Nadel](http://www.bennadel.com)'s plugin design for Single page application development using jQuery. 

Here is a simple example of a Home page plugin 

    /*
     *  Home Page plugin     
     */     
    window.application.addClass('home',(function($,app) {  
        
        function Home() {    
           // global class variables
        };      
        
        // This function will be invoked first.
        Home.prototype.init = function() {
            var self = this;
            alert('Hello Welcome!!');
        };
        // return an instance of the Home class.
        return (new Home());    
        
    })(jQuery, window.application));



and here is how we tell jQuery app to load home plugin, by using  `PAGE` variable ( comes from server side by default) inside `initCanvas()`. This function statement accept `PAGE` as the argument and invokes plugin if it exists.

    // This is initCanvas function
    App.prototype.initCanvas = function() {
        var self = this, page = PAGE, view = null;    
        switch(page) {
            // If the PAGE is home then this plugin will be invoked.
            case 'home':            
                view = self.getClass('home');
                view.init();
            break;  
        }
        return;
    };


Now for our simple Todo app lets create a `todo_list` plugin. Remember that we have already created conditions in our controller of server side to accept update and create actions. 

    /*
     *  Todo list
     *  ( Todo page details)
     */
    window.application.addClass('todo_list',(function($,app) {
      
        function Todo() {
        
        };    
        
        // This function will send a task create request to server
        Todo.prototype.create_task = function( callback ) {
            var task_title = $('#new-task-text').val();
            if (task_title == '') {
                alert('task cannot be empty!!');
                return false;
            }    
            var option = {
                'data': {
                    '_action'   : 'new_task',
                    'task_title': task_title
                },
                'success': callback
            };
            app.Ajax(option);
        };
        
        // This request will update an existing task 
        Todo.prototype.update_task = function( data, callback ) {        
            var option = {
                'data': {
                    '_action'   : 'update_task'        
                },
                'success': callback
            };
            $.extend(option.data, data);
            app.Ajax(option);
        };
        
        // This is the main function that will invoke from application
        Todo.prototype.init = function() {
            var self = this;
            
            // Always focus on task create textbox, keep it clean
            $('#new-task-text').focus().empty();
            
            // If we press enter then create task
            $('#new-task-text').keydown(function(e){
                // On pressing enter
                if (e.which == 13) {
                    self.create_task(function(){
                        location.reload();
                    });
                }      
            });
            
            // If we press create button then call create task
            $('#create-task').click(function(e){      
                self.create_task(function(){
                    location.reload();
                });          
            });
            
            // Mark task as done / undone by checking checkbox
            $('.complete-task').change(function(){
                var id = $(this).attr('id').split('-')[1];
                var data = {'task_id':id,'status' : 'COMPLETED'};
                if (!$(this).is(':checked')) {
                    data.status = 'PENDING';
                }                
                self.update_task(data, function( res ) {              
                    location.reload();
                });              
            });
        };
        
        // return todo page plugin to application
        return (new Todo());    
        
    })(jQuery,window.application));
    
    
    
After all this Lets add some style to our app so that it looks OK.


    .todo-row{
       padding:5px 0px;
       border-bottom:1px solid #ccc;
    }
    #new-task-block
    {
        border-bottom: 1px solid #ccc;
        background:#efefef;
        padding:10px 0px;
    }

    #create-task{
        padding:6px 5px;
    }
    .new-task-text{
        width:100%;
        margin-left:2px;
        padding:10px 2px;
        border-radius:0px;
        border:1px solid #ccc;  
    }

    .task-name{
        font-size:1.5em;
        font-family:inherit;
        color:#555;
    }

    .task-done{
        text-decoration:line-through;
        color:#808080;
    }

Ok, We have just created a simple Todo application. Oh One more thing, It works in mobile also, try to resize the window and see the magic :-)   
