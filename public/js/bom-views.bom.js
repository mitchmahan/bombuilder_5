$(function(){
  /**
   *
   * BOM Page Tab Functions
   *
   */
  var hash = window.location.hash;
  hash && $('ul.nav a[href="' + hash + '"]').tab('show');

  /* Update the URL when a tab is clicked */
  $('.nav-tabs a').click(function (e) {
    $(this).tab('show');
    var scrollmem = $('body').scrollTop();
    window.location.hash = this.hash;
    $('html,body').scrollTop(scrollmem);
  });
  $('table#partsTable tr:last td:nth-child(1)').text('TOTAL');
  $('table#partsTable tr:last td:last-child').text(function(){
    var t = 0;
    $('table#partsTable tr:not(:first,:last)').each(function(){
      part = $(this);
      count = part.find('.count').text();
      price = part.find('.price').text()
      t += count * price;
    });
    console.log(t);
    return '$' + t.toFixed(2);
  });

  /**
   * Search inputs in the given Modals
   *
   */
  $('#partSearch').devbridgeAutocomplete({
    serviceUrl: '/part',
    onSelect: function (suggestion) {
      var id = suggestion.data;

      if($('#PartId').val() > 0){
        $('#PartId').val(id);
      }else{
        $('<input>').attr({
          type: 'hidden',
          id: 'PartId',
          value: id,
          name: 'PartId'
        }).appendTo($(this).parent());
      }
    }
  });

  $('#cablerunSearch').devbridgeAutocomplete({
    serviceUrl: '/cablerun',
    onSelect: function (suggestion) {
      var id = suggestion.data;

      if($('#CablerunId').val() > 0){
        $('#CablerunId').val(id);
      }else{
        $('<input>').attr({
          type: 'hidden',
          id: 'CablerunId',
          value: id,
          name: 'CablerunId'
        }).appendTo($(this).parent());
      }
    }
  });

  /**
   * Hide all "transuntrust" URLs by default.
   *
   * Enable the transuntrust checkbox to change which URLs are shown
   *
   * Bom -> URLs Tab -> transuntrust checkbox
   */
  $('[data-mode="1"]').parent().hide();
  $('#transuntrust').click(function(){
    if( $(this).is(":checked") )
      {
        $('[data-mode="0"]').parent().hide();
        $('[data-mode="1"]').parent().show();
      }else{
        $('[data-mode="0"]').parent().show();
        $('[data-mode="1"]').parent().hide();
      }
  });

});
