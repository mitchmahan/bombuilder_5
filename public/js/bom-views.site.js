$(function(){
  // Search for cables
  $('#siteBomSearch').devbridgeAutocomplete({
    serviceUrl: '/bom',
    onSelect: function (suggestion) {
      var id = suggestion.data;

      if($('#BomId').val() > 0){
        $('#BomId').val(id);
      }else{
        $('<input>').attr({
          type: 'hidden',
          id: 'BomId',
          value: id,
          name: 'BomId'
        }).appendTo($(this).parent());
      }
    }
  });

});
