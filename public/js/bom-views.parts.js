/**
 * 
 * Combined JS functions for parts and part views.
 *
 */
$(document).ready(function(){


  /**
   *
   * Sort the parts datatable by the 3rd column
   * Set number of records returned to 100
   *
   */
  $('.data-table').DataTable({
    'iDisplayLength': 100,
    'order': [[ 3, "desc" ]]
  });


  /**
   *
   * Search for parts for adding "children"
   *
   */
  $('#partSearch').devbridgeAutocomplete({
    serviceUrl: '/part',
    onSelect: function (suggestion) {
      var id = suggestion.data;

      if($('#ChildPartId').val() > 0){
        $('#ChildPartId').val(id)
      }else{
        $('<input>').attr({
          type: 'hidden',
          id: 'ChildPartId',
          value: id,
          name: 'ChildPartId'
        }).appendTo($(this).parent());
      }
    }
  });

  /**
   *
   * Search for vendors for adding a new part
   *
   */
  $('#vendorSearch').devbridgeAutocomplete({
    serviceUrl: '/vendor',
    onSelect: function (suggestion) {
      var id = suggestion.data;

      if($('#VendorId').val() > 0){
        $('#VendorId').val(id)
      }else{
        $('<input>').attr({
          type: 'hidden',
          id: 'VendorId',
          value: id,
          name: 'VendorId'
        }).appendTo($(this).parent());
      }
    }
  });

});
