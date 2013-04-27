(function($) {
         
  function App() {
    this.views = [];
    this.body = $('body');
    this.domainurl = 'http://flubbermvc.com'
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
      type :'POST'
    };
    $.extend(options,opt);
    return $.ajax(options);
  };  
  
  App.prototype.route = function(path) {
    if (path)
      document.location = path;
  };
   
  App.prototype.addClass = function(name, rawClass) {
    this.views[name] = rawClass;     
  };
  
  App.prototype.getClass = function(name) {
    return this.views[name];     
  };
  
  App.prototype.getargsFromURL = function() {
    var details = window.location.pathname.toString().split( '/' );
    return (details);
  };
  
  App.prototype.isLoggedIn = function() {
    if ($.cookie('uid')) 
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
  
  App.prototype.init = function() {
    var self = this;   
    self.initCanvas();    
  };   

  (window.application = new App());         

  $(function() {    
    window.application.init();
  });
    
  return (window.application);      
})(jQuery);

/*!
 *  Home Page
 *  ( Home page details)
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

