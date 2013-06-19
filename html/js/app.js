(function($) {
         
  function App() {
    /*!
     *  Collection of ready view Classes
     */ 
    this.views = [];

    /*!
     *  reference to page body
     */ 
    this.body = $('body');

    /*!
     *  Domain URL 
     */ 
    this.domainurl = '';

    /*!
     *  Page reference 
     */ 
    this.pageID = '';
  };

  App.prototype.log = function(message, object) {
    if (typeof console.log !== 'undefined') {
      if (typeof object !== 'undefined')
        console.log(message, object);
      else
        console.log(message);
    }
  };
  
  App.prototype.Ajax = function(opt) {
    var options = {
      url:'/',
      type :'POST',
      dataType:'json'
    };
    $.extend(options,opt);
    return $.ajax(options);
  };  
  
  App.prototype.route = function(path) {
    if (path)
      document.location = path;
  };

  App.prototype.getPageData = function() {
    /*!
     *  This variable is set in server side VIEW class
     */ 
	  return window.PAGEDATA;
  };
   
  App.prototype.addClass = function(name, rawClass) {
    this.views[name] = rawClass;     
  };
  
  App.prototype.getClass = function(name) {
    /*!
     *  After getting the raw class
     *  check for the pageData and pass it to plugin
     */ 
    if (typeof this.views[name] === 'undefined')
      return false;
    var rawClass = this.views[name];    
    rawClass.appVars = this.getPageData();    
    return rawClass;       
  };
  
  App.prototype.getargsFromURL = function() {
    var details = window.location.pathname.toString().split( '/' );
    return (details);
  };
  
  App.prototype.isLoggedIn = function() {
    if ($.cookie('id')) 
      return true;    
    return false;
  };
  
  App.prototype.normalizeString = function(str) {
    if (typeof str === 'string' && str.length > 0 ) {
      try {
        return str.split(' ').join('_');
      }
      catch(err) {
        return str;
      }
    }    
    return false;
  };


  App.prototype.Controller = function() {};
  
  App.prototype.Controller.prototype = {
    route: function(location) {
      document.location = location;
    }
  };
  
  App.prototype.initCanvas = function() {
    var self = this, page = PAGE, view = null;    
    switch(page) {
      
      case 'home':
      default:
        view = self.getClass('home');
        view.init();
      break;      
    }
    return;
  };
  
  App.prototype.init = function(page, domain) {
    var self = this;
    /*!
     * set the global variables
     */ 
    self.pageID = page;
    self.domainurl = domain;
    
    self.initCanvas();    
  };   

  (window.application = new App());         

  $(function() {
    /*!
     *  When document is ready pass page and domain reference to plugin
     */ 
    window.application.init(PAGE,DOMAIN);
  });
    
  return (window.application);
  
})(jQuery);

/*!
 *  New Class definition
 *  this plugin structure will be refered as Flubber's View Class
 *  
 *  when `window.application` is ready, it loads all the class with a name
 *  and a raw object of the View Class as shown below
 *  and feom the application inin function we can trigger different plugins according to pageID
 * 
 */
window.application.addClass('home',(function($,app) {
  
  function Home() {
    
  };    
  
  Home.prototype.init = function() {
    var self = this;
    alert('Hello Welcome!!');
  };
  
  return (new Home());
  
})(jQuery,window.application));

