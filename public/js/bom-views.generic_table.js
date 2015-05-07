/**
 *
 * Common JS for each generic-table view
 *
 * Generally the basic "Manage" pages
 *
 */

$(document).ready(function(){


  /**
  *
  * Data tables should be ordered by the first column
  *
  * Default of 10 rows shown.
  *
  */
  $('.data-table').DataTable({
    'iDisplayLength': 10,
    'order': [[ 1, "asc" ]]
  });


  // Search for cables
  $('#cableSearch').devbridgeAutocomplete({
    serviceUrl: '/cable',
    onSelect: function (suggestion) {
      var id = suggestion.data;

      if($('#CableId').val() > 0){
        $('#CableId').val(id);
      }else{
        $('<input>').attr({
          type: 'hidden',
          id: 'CableId',
          value: id,
          name: 'CableId'
        }).appendTo(this);
      }
    }
  });
});
