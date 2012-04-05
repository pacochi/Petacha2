/*
 * Tag Plugin for jQuery 1.1.2
 *
 * Copyright(C) 2007 LEARNING RESOURCE LAB.
 * http://postal-search-apis-and-solutions.blogspot.com/
 *
 * Dual licensed under the MIT (MIT-LICENSE.txt)
 * and GPL (GPL-LICENSE.txt) licenses.
 */
(function($) {
  
  // $.tag
  $.tag = function(name) {
    return jQuery('<' + name + ' />');
  };
  
  // tag
  $.fn.tag = function(name) {
    var self = this;
    return self.pushStack($.tag(name));
  };
  
  // gat
  $.fn.gat = function() {
    var self = this;
    return self.end().append(self);
  };
  
})(jQuery); // function($)
