/*
 * bom.js - global javascript for the bombuilder app
 *
 * @author Mitch Mahan
 */
$(function() {
  /**
   *
   *  All AJAX requests should use the sites CSRF token
   *
   */
  $.ajaxSetup({
    headers: {
      'X-CSRF-Token': $('meta[name="_token"]').attr('content')
    }
  });


  /**
   *
   * Search for BOMs
   *
   */
  $('#search_input').devbridgeAutocomplete({
    serviceUrl: '/bom',
    onSelect: function (suggestion) {
      var id = suggestion.data;
      window.location = "/bom/" + id;
    }
  });


  /**
   *
   * Logout
   *
   */
  $( "#logout-btn" ).on( "click", function(e) {
    e.preventDefault();
    $.get("/user/logout", function(){
      location.reload();
    });
  });

  /**
   *
   * Forms should submit data to the data-url of the form
   *
   * All forms should be serialized and submitted via AJAX.
   *
   * IE8,9 do not support FormData and will submit the forms directly.
   *
   */
  $('form').submit(function() {
    var form = $(this);
    var validation = $(this).find("#validation-errors");
    /**
     *
     * IE8 and 9 do not support "FormData" method
     *
     */
    if(typeof FormData == "undefined"){
      // return true and just submit the form normally
      return true;
    }else{
      var data = new FormData(this)
      $.ajax({
        url: form.attr('action'),
        type: 'post',
        cache: false,
        dataType: 'json',
        data: data,
        processData: false,
        contentType: false,
        beforeSend: function() { 
          validation.hide().empty(); 
        },
        success: function(data) {
          if(data.success){ 

            // If the return data is a panel add it to our cookie
            if(data.type == 'panel'){
              if($.cookie("bom.grid-data") != undefined) {
                var saved_panels = JSON.parse($.cookie("bom.grid-data"));
                saved_panels.push({id: data.id, col: 1, row: 1, size_x: 1, size_y: 1});

                var positions = JSON.stringify(saved_panels);
                $.cookie("bom.grid-data", positions);
              }
            }

            location.reload();
          }
        },
        error: function(xhr, textStatus, thrownError) {
          /* Show validation errors in our modal */
          if(xhr.status == 400){
            $.each(xhr.responseJSON.errors, function(index, value) {
              var alert = '<div class="alert alert-warning alert-block">';
              alert += value;
              alert += '<button type="button" class="close" data-dismiss="alert">';
              alert += '</button></div>';
              validation.append(alert);
            });
            validation.show();
          }
        },
      });
    }

    return false;
  });

  /**
   *
   * All modal-btn's should open the respective
   * modal found in the data-modal attribute
   *
   */
  $(".btn-modal").click(function(e) {
    e.preventDefault();
    e.canelBubble = true;

    var modal_name = $(this).data('modal');
    $("#" + modal_name + "-modal").modal('show');

    return false;
  });


  /**
   *
   * Delete buttons on forms should post the data-id to the current location
   *
   * This will direct to the controller "delete()" method
   *
   */
  $('.delete').click(function(e){
    e.preventDefault();
    e.canelBubble = true;
    e.stopPropagation();

    $.post(window.location.pathname + '/delete', {
      'id': $(this).data('id'),
      'type': $(this).data('type')
    } 
          )
          .done(function(data) {
            location.reload();
          })
          .fail(function(data) {
            alert(JSON.stringify(data.responseJSON.errors));
          });
  });


  /**
   *
   *  Default editable elements should be "Inline" 
   *
   */
  $.fn.editable.defaults.mode = 'inline';


  /**
   *
   * Most table rows should be "clickable"
   *
   */
  $(".clickable").click(function() {
    var id = $(this).data('id');
    var url = $(this).data('url');
    window.location = url + id;
  });

});

/**
 *
 * Make certain elements editable via the .editable jquery plugin
 *
 */
function makeEditable() {
  $(".edit").editable({
    type: 'text',
  });

  $('#desc').editable({
    type: 'textarea',
    title: 'Site Desc',
    rows: 3
  });

  $('#email').editable({
    type: 'email',
    title: 'First Name',
  });

  $('#password').editable({
    type: 'password',
    title: 'Password',
  });

  $('#vendor').editable({
    type: 'select',
    title: 'Vendor',
    source: function() {
      var result;
      $.ajax({
        url: '/vendor',
        type: 'GET',
        global: false,
        async: false,
        dataType: 'json',
        success: function(data) {
          var select = $.map(data, function(model){
            return {value: model.vendor_id, text: model.vendor_name};
          });
          result = select;
        }
      });
      return result;
    }
  });

  $('#cable').editable({
    type: 'select',
    title: 'Cable',
    source: function() {
      var result;
      $.ajax({
        url: '/cable',
        type: 'GET',
        global: false,
        async: false,
        dataType: 'json',
        success: function(data) {
          var select = $.map(data, function(model){
            return {value: model.cable_id, text: model.type};
          });
          result = select;
        }
      });
      return result;
    }
  });

}
