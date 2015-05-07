<?php 
/**
 * THIS IS DEFINITELY THE WORST CODE IN THIS APPLICATION.
 *
 * Sorry in advance...
 *
 * @TODO: Refactor!
 */

function recurseChildren($part,$fields) {
  /**
   * Recurse through all of the children for a specific part.
   *
   * This allows us to output all children parts for each "main part".
   *
   */
  $table = '';

  foreach($part->children as $parent)
  {
    // Keep track of our part count
    $parent->pivot->count = $parent->pivot->count * $part->pivot->count;
    $table .= "<tr class='child-part-row' data-parent-id='{$part->part_id}'>";
    // Pad our <tr> if the user is logged in (Actions column)
    if(Auth::check()) $table .= "<td></td>";
    foreach($fields as $title => $db_field) {
      if( is_array($db_field) ) {
        $td_data = $parent->$db_field[0]->$db_field[1];
        $table .= "<td class='$db_field[1]'>" . e($td_data) . '</td>';
      }else{            
        if($title == 'Description'){
          $table .= '<td class="hidden-sm hidden-xs">';
        }else{
          $table .= "<td class='$db_field'>";
        }
        $table .= e($parent->$db_field) . '</td>';
      }
    }
    $table .= '</tr>';
    foreach($parent->children as $child)
    {
      // We will need child*parent parts for each child
      $child->pivot->count = $child->pivot->count * $parent->pivot->count;
      /**
       * Add the parent-id to the <tr> HTML5 data attribute.
       *
       * Maybe we can use this value in a future JavaScript function?
       *
       * @ideas: Group parts under their parent part, etc.
       */
      $table .= "<tr class='child-part-row' data-parent-id='{$parent->part_id}'>";
      // Pad our <tr> if the user is logged in (Actions column)
      if(Auth::check()) $table .= "<td></td>";
      foreach($fields as $title => $db_field) {
        if( is_array($db_field) ) {
          $td_data = $child->$db_field[0]->$db_field[1];
          $table .= "<td class='$db_field[1]'>" . e($td_data) . '</td>';
        }else{            
          if($title == 'Description'){
            $table .= '<td class="hidden-sm hidden-xs">';
          }else{
            $table .= "<td class='$db_field'>";
          }
          $table .= e($child->$db_field) . '</td>';
        }
      }
      $table .= '</tr>';
      if( isset($child->children) )
      {
        foreach($child->children as $child)
        {
          $table .= recurseChildren($child,$fields);
        }
      }
    }
  }
  return $table;
}


/**
 * Parts table macro.
 *
 * @input object $parts
 * @output HTML <table>
 */
HTML::macro('parts',
  function(
    $settings,
    $fields = array(),
    $parts
  ){
    $table = "<table id='partsTable' class='table table-condensed table-striped {$settings['class']}'>";
    /* TABLE HEAD */
    $table .= '<thead>';
    /* Logged in users get additional "Actions" column */
    if(Auth::check()) $table.= "<th>Actions</th>";
    /* BUILD TH ROW */
    foreach($fields as $title => $db_field)
    {
      // Hide the Description field on small and extra-small viewports
      if($title == 'Description'){
        $class = 'hidden-sm hidden-xs';
      }else{
        $class = '';
      }
      $table .= "<th class='$class'>" . $title . '</th>';
    }
    $table .= '</thead>';
    /* TABLE BODY */
    $table .= '<tbody>';
    foreach($parts as $part)
    {
      /* Build the parent TR */
      $table .= "<tr class='part-row' data-url='/Part/' data-id='{$part->getKey()}'>";
      /* Add a delete button to the beginning of each row */
      if(Auth::check()){
        $table .= "<td>";
        $table .= "<button data-type='Part' data-id='{$part->getKey()}' class='btn btn-danger delete glyphicon glyphicon-trash'></button>";
        $table .= "</td>";
      }
      foreach($fields as $title => $db_field) {
        /**
         * Check if we are accessing a direct or nested value of our object
         * 
         * For example a part may have a vendor with a 'vendor_name'.
         *
         * Nested object accessors are presented to this macro as an array.
         */
        if( is_array($db_field) ) {
          $td_data = $part->$db_field[0]->$db_field[1];
          $table .= "<td class='$db_field[1]'>" . e($td_data) . '</td>';
        }else{            
          if($title == 'Description'){
            $table .= '<td class="hidden-sm hidden-xs">';
          }else{
            $table .= "<td class='$db_field'>";
          }
          $table .= e($part->$db_field) . '</td>';
        }
      }
      $table .= '</tr>';
      $table .= recurseChildren($part,$fields);
    }
    /**
     * Totals Row
     */
    $table .= '<tr class="total">';
    if(Auth::check()) $table .= '<td></td>';
    foreach($fields as $title => $db_field) {
      if($title == 'Description'){
        $table .= '<td class="hidden-sm hidden-xs"></td>';
      }else{
        $table .= "<td></td>";
      }
    }
    $table .= '</tr>';
    $table .= '</tbody>';
    $table .= '</table>';
    return $table;
  });


/**
 *
 * Default HTML 'table' macro we use throughout the site
 *
 */
HTML::macro('table',
  function(
    $table_id,
    $classes,
    $fields = array(),
    $parts = array(), 
    $resource, 
    $showActions = false
  ){
    $table = "<table id='$table_id' class='$classes'>";

    /* TABLE HEAD */
    $table .= '<thead>';
    if(Auth::check() && $showActions) $table.= "<th></th>";
    foreach($fields as $title => $db_field)
    {
      if($title == 'Description'){
        $class = 'hidden-sm hidden-xs';
      }else{
        $class = '';
      }
      $table .= "<th class='text-center $class'>" . strtoupper(e($title)) . '</th>';
    }
    $table .= '</thead>';

    /* TABLE BODY */
    $table .= '<tbody>';
    foreach($parts as $part)
    {
      if( ($table_id === 'rmuTable') && ( intval($part->rmu) <= 0 ) ){
        continue;
      }
      // Build the parent TR
      $table .= "<tr class='clickable' data-url='/".strtolower($resource)."/' data-id='{$part->getKey()}'>";
      if(Auth::check() && $showActions){
        $table .= "<td>";
        // Delete
        $table .= "<button data-type='$resource' data-id='{$part->getKey()}' class='btn btn-danger delete glyphicon glyphicon-trash'></button> ";
        $table .= "</td>";
      }

      /* Place data in each field */
      foreach($fields as $title => $db_field) {

        /* Access child objects if we see an array in the field */
        if( is_array($db_field) ) {
          $td_data = $part->$db_field[0]->$db_field[1];
          $table .= "<td>" . e($td_data) . '</td>';
        }else{            
          if($title == 'Description'){
            $table .= '<td class="hidden-sm hidden-xs">';
          }else{
            $table .= '<td>';
          }
          $table .= e($part->$db_field) . '</td>';
        }
      }
      $table .= '</tr>';
    }
    $table .= '</tbody>';
    $table .= '</table>';

    return $table;

  });


/** 
 * HTML 'urls' macro
 *
 * Create a list of images and URLs associated to a part or BOM
 */
HTML::macro('urls', function($urls, $delete){
  $links = "";
  $basic_urls = "";

  foreach($urls as $url){
    $href = $url['url'];
    if( Auth::check() && $delete ){
      $delete_button = "<button class='delete btn btn-danger glyphicon glyphicon-trash pull-left'";
      $delete_button .= "data-id={$url['url_id']} data-type='Url' href='/deleteImage'></button>";
    }else{
      $delete_button = '';
    }

    /**
     * Check URLs in BOM 2.0 format first
     *
     * @input url
     * @example 'bom_images/3959/device.png'
     *
     */
    if( File::exists(public_path($href)) ){
      $extension = File::extension($href);
      if( $extension === 'vsd' or $extension === 'vsdx'){
        $image = asset($href . ".png");
      }else{
        $image = asset($href);
      }
      $links .= "<li class='list-group-item'><h4>". basename($image) ."</h4>";
      $links .= $delete_button;
      $links .= "<a href=".asset($href).">";
      $links .= "<img style='max-width: 70%' src='$image' alt=".basename($image).">";
      $links .= "</a>";
      $links .= "</li>";
      continue;
    }

    /**
     *
     * Visio documents in BOM1.0 Format
     *
     * URLs for Visio's and their image from BOM Builder 1.0 do not use
     * extensions. Lets check for Visio's and add them if we find one.
     *
     * @input url
     * @example 'images/someVisio'
     */
    $image = '/bom_images/' . basename($href) . '.jpg';
    $visio = '/bom_images/' . basename($href) . '.vsd';

    if ( File::exists(public_path($image)) && File::exists(public_path($visio)) ){
      $links .= "<li class='list-group-item'>";
      $links .= $delete_button;
      $links .= "<a href='".asset($visio)."'>";
      $links .= "<img style='max-width: 70%' src=".asset($image)." alt=".basename($image).">";
      $links .= "</a>";
      $links .= "<div class='clearfix'></div>";
      $links .= "</li>";
      continue;
    }

    /**
     *
     * Images in BOM1.0 Format
     *
     * Some images are from BOM 1.0 contain their extension
     *
     * @input url
     * @example 'images/image.gif'
     */
    $image = '/bom_images/' . basename($href);
    if( File::exists(public_path($image)) ){
      $links .= "<li class='list-group-item'><h4>". basename($image) ."</h4>";
      $links .= $delete_button;
      $links .= "<a href='".asset($image)."'>";
      $links .= "<img style='max-width: 30%' src=".asset($image)." alt=".basename($image).">";
      $links .= "</a>";
      $links .= "<div class='clearfix'></div>";
      $links .= "</li>";
      continue;
    }

    /**
     *
     * Default action (Just print a list item with the URL)
     *
     */
    if( isset($url['title']) )
    {
      $title = $url['title'];
    }else{
      $title = basename($href);
    }
    $basic_urls .= "<li class='list-group-item'>";
    $basic_urls .= $delete_button;
    $basic_urls .= link_to($href, strtoupper($title), array(
      'target' => 'blank', 
      'data-mode' => ($url['pivot']['mode'] ? 1 : 0)
    ));
    $basic_urls .= "<div class='clearfix'></div>";
    $basic_urls .= "</li>";
  }

  $response = $basic_urls . $links;

  return $response;
});


/**
 * Build images for all parts associated to a BOM 
 *
 * This includes building URLs for children parts recursively.
 */
HTML::macro('partUrls', function($parts)
  {
    $html = '';
    foreach($parts as $parent)
    {
      $html .= HTML::urls($parent->urls, False);
      $html .= HTML::partUrls($parent->children);
    }
    return $html;
  });


/**
 *  Create a Bootstrap3 compatible modal
 *
 *  This is used for all of our "add buttons", etc.
 *  @input array()
 *  @return html modal
 */
HTML::macro('modal', function($name, $url, $inputs){
  $modal = <<<EOT
    <div class="modal fade" id="$name-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
    <div class="modal-content">
    <div class="modal-header">
    <h4 class="modal-title">Add $name</h4>
    </div><!-- /.modal-header -->
EOT;

  // Open Form
  $modal .= Form::open( array(
    'url'     => $url, 
    'method'  => 'post', 
    'class'   => 'form-horizontal', 
    'id'      => 'add'.$name.'Form',
    'files'   => true
  )
);

  $modal .= <<<EOT
    <div class="modal-body">
    <div id="validation-errors"></div>
EOT;

  foreach($inputs as $input){
    $label        = $input['label'];
    $type         = $input['type'];
    $input_name   = $input['name'];
    $input_id     = $input['id'];
    if(isset($input['placeholder'])){
      $placeholder = $input['placeholder'];
    }else{
      $placeholder = Null;
    }

    // Label
    $modal .= '<div class="control-group">';
    $modal .= Form::label( $input_name.'Label',
      $label, 
      array('class' => 'control-label')
    );

    // Input
    $modal .= '<div class="controls">';

    if($type == 'password'){
      $modal .= Form::$type($input_name, 
        array(
          'id' => $input_id,
          'placeholder' => $placeholder,
          'class' => 'form-control'
        ) 
      );
    }
    elseif($type == 'checkbox')
    {
      $modal .= Form::$type($input_name);
    }
    else
    {
      $modal .= Form::$type($input_name, 
        '', 
        array(
          'id' => $input_id,
          'placeholder' => $placeholder,
          'class' => 'form-control'
        ) 
      );
    }

    $modal .= '</div></div>';
  }
  $modal .= <<<EOT
    </div><!-- /.modal-body -->
    <div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">
      Close
    </button>
EOT;
  $modal .= Form::submit('Submit', array('class' => 'btn btn-primary'));
  $modal .= '</div>';
  $modal .= Form::close();
  $modal .= <<<EOT
    </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
EOT;

  return $modal;

});

?>
