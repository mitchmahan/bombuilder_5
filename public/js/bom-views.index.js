/*
 * bom-views.index.js
 *
 * JavaScript for the homepage. Mostly gridster settings and functions
 * for the dyanmic "Site" boxes found on the homepage.
 *
 * @author Mitch Mahan
 */
$(function() {
  /**
   *
   * Initiate Gridster
   *
   */
  $(".gridster ul").gridster({
    widget_margins: [20, 10],
    widget_base_dimensions: [400, 80],
    columns: 2,
    max_cols: 2,
    min_cols: 2,
    row: 2,
    serialize_params: function(w, wgd) {
      return {
        id: wgd.el[0].id,
        col: wgd.col, 
        row: wgd.row,
        size_x: wgd.size_x,
        size_y: wgd.size_y,
      };
    },
    draggable: {
      stop: function(event, ui) {
        var positions = JSON.stringify(gridster.serialize());
        $.cookie("bom.grid-data", positions);

        /**
         *
         *  Save panels to the database.
         *
         *  This would allow a logged in user to ALWAYS display the same panels
         *  no matter what the cookie says.
         *
         *  Needs implementation:
         *  Backend controller fuction for updating user.panels in the database
         *  JavaScript to retrieve the user.panels and ignore the cookie
         *  Additional calls to the below (on widget close, etc.)
         *
         *
         if(user_is_logged_in){
           $.post("/panel/user",positions,
           function(data){
             var dataArray = $.makeArray(data);
             $.map(dataArray[0], createSiteWidget);

             // Save widgets to our cookie
             var positions = JSON.stringify(gridster.serialize());
             $.cookie("bom.grid-data", positions);
           }
           );
         }
         *
         */
      }
    }
  });
  var gridster = $(".gridster ul").gridster().data('gridster');


  /** 
   * SITES MENU
   *
   * Add panels/sites/widgets for a network to the homepage.
   *
   */

  /**
   * "Add All" buttons
   *
   */
  $('.network-select').click(function(e){
    var network = $(this).data('network');

    $.get("/panels?network=" + network, function(data) {
      // Add the widgets to the grid
      var dataArray = $.makeArray(data);
      $.map(dataArray[0], createSiteWidget);

      // Save widgets to our cookie
      var positions = JSON.stringify(gridster.serialize());
      $.cookie("bom.grid-data", positions);
    });
  });

  /**
   * Delete all gridster widgets
   *
   * The "Reset" button
   *
   */
  $('a#delete-widgets').click(function(e){
    gridster.remove_all_widgets();
    e.preventDefault();
    $.removeCookie("bom.grid-data");
    location.reload();
  });

  /**
   * Add a specific panel
   *
   */
  $('.panel-add').click(function(e){
    var panelId = $(this).data('id');

    $.get("/panel/" + panelId, function(data) {
      // Build the widget
      createSiteWidget(data);

      // Save widgets to our cookie
      var positions = JSON.stringify(gridster.serialize());
      $.cookie("bom.grid-data", positions);
    });
    //saved_panels.push({id: data.id, col: 1, row: 1, size_x: 1, size_y: 1});
  });


  /**
   * Would you like milk with your cookie?
   * How about some Panels? Lets load some up!
   *
   * Show the "networkSelection" modal if no cookie exists
   *
   */
  if($.cookie("bom.grid-data") == undefined) {
    $('#networkSelection').modal('show');
    /**
     *  Select a network and load the panels
     */
    $('.network-select').click(function(e){
      // Hide the modal
      $('#networkSelection').modal('hide');
    });
  } else {
    /**
     * Load only the panels found in the cookie
     */
    var saved_panels = JSON.parse($.cookie("bom.grid-data"));
    $.each(saved_panels, function(key, panel){
      $.get("/panel/" + panel.id, function(data){
        // Keep the location and size of the panel
        $.extend(data,panel);
        // Build the widget
        createSiteWidget(data);
      });
    }); 
  }


  /**
   * Function to create a new gridster widget based on the data we get
   * from our backend.
   *
   */
  function createSiteWidget(data){
    var widget = $('<ul>', {
      'id': data.id,
      'class': 'new list-group'
    });

    var close = $('<span>', {
      'class': 'btn-sm btn-danger glyphicon glyphicon-remove pull-right',
      'style': 'margin-top: -5px;'
    });

    /**
     * Removing a panel (clicking the red X) should save the new grid
     * to the users cookie
     */
    close.click(function(){
      // Remove the widget from the grid
      gridster.remove_widget($(this).parent().parent()) 
      // Save the new grid to our cookie
      var positions = JSON.stringify(gridster.serialize());
      $.cookie("bom.grid-data", positions);
    });

    var title = $('<li>', {
      'class': 'new list-group-item active',
      'text': data.name
    });
    $(title).append(close);
    $(widget).append(title);

    var select = $('<select>', {
      'class': 'form-control',
    });
    // Make the first option blank
    $('<option>').appendTo(select);
    
    /**
     * Changing the select menu to a BOM should change our URL to that BOM
     *
     */
    select.change(function() { window.location = $(this).find(':selected').data('href'); });

    /**
     * Add a select option in our widget for each
     * BOM that is a part of this panel
     *
     */
    $.each(data.panels, function( key, obj ){
      var option = $('<option>', {
        'text': obj.value,
        'data-href': '/bom/' + obj.id
      });

      $(select).append(option);
    });
    $(widget).append(select);

    gridster.add_widget(widget, 1, 1, data.col, data.row);
  }

});

/*!
 * jQuery Cookie Plugin v1.4.1
 * https://github.com/carhartl/jquery-cookie
 *
 * Copyright 2006, 2014 Klaus Hartl
 * Released under the MIT license
 */
(function (factory) {
 if (typeof define === 'function' && define.amd) {
 // AMD
 define(['jquery'], factory);
 } else if (typeof exports === 'object') {
 // CommonJS
 factory(require('jquery'));
 } else {
 // Browser globals
 factory(jQuery);
 }
 }(function ($) {

   var pluses = /\+/g;

   function encode(s) {
   return config.raw ? s : encodeURIComponent(s);
   }

   function decode(s) {
   return config.raw ? s : decodeURIComponent(s);
   }

   function stringifyCookieValue(value) {
   return encode(config.json ? JSON.stringify(value) : String(value));
   }

   function parseCookieValue(s) {
   if (s.indexOf('"') === 0) {
   // This is a quoted cookie as according to RFC2068, unescape...
   s = s.slice(1, -1).replace(/\\"/g, '"').replace(/\\\\/g, '\\');
   }

   try {
     // Replace server-side written pluses with spaces.
     // If we can't decode the cookie, ignore it, it's unusable.
     // If we can't parse the cookie, ignore it, it's unusable.
     s = decodeURIComponent(s.replace(pluses, ' '));
     return config.json ? JSON.parse(s) : s;
   } catch(e) {}
   }

   function read(s, converter) {
     var value = config.raw ? s : parseCookieValue(s);
     return $.isFunction(converter) ? converter(value) : value;
   }

   var config = $.cookie = function (key, value, options) {

     // Write

     if (arguments.length > 1 && !$.isFunction(value)) {
       options = $.extend({}, config.defaults, options);

       if (typeof options.expires === 'number') {
         var days = options.expires, t = options.expires = new Date();
         t.setTime(+t + days * 864e+5);
       }

       return (document.cookie = [
           encode(key), '=', stringifyCookieValue(value),
           options.expires ? '; expires=' + options.expires.toUTCString() : '', // use expires attribute, max-age is not supported by IE
           options.path    ? '; path=' + options.path : '',
           options.domain  ? '; domain=' + options.domain : '',
           options.secure  ? '; secure' : ''
           ].join(''));
     }

     // Read

     var result = key ? undefined : {};

     // To prevent the for loop in the first place assign an empty array
     // in case there are no cookies at all. Also prevents odd result when
     // calling $.cookie().
     var cookies = document.cookie ? document.cookie.split('; ') : [];

     for (var i = 0, l = cookies.length; i < l; i++) {
       var parts = cookies[i].split('=');
       var name = decode(parts.shift());
       var cookie = parts.join('=');

       if (key && key === name) {
         // If second argument (value) is a function it's a converter...
         result = read(cookie, value);
         break;
       }

       // Prevent storing a cookie that we couldn't decode.
       if (!key && (cookie = read(cookie)) !== undefined) {
         result[name] = cookie;
       }
     }

     return result;
   };

   config.defaults = {};

   $.removeCookie = function (key, options) {
     if ($.cookie(key) === undefined) {
       return false;
     }

     // Must not alter options, thus extending a fresh object...
     $.cookie(key, '', $.extend({}, options, { expires: -1 }));
     return !$.cookie(key);
   };

 }));
