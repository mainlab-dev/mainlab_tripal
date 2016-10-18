<?php
$featuremap = $variables['node']->featuremap;
$feature_positions = $featuremap->positions;

// get the total number of records
$total_features = count($feature_positions);


if(count($feature_positions) > 0){ ?>
  <div class="tripal_featuremap-data-block-desc tripal-data-block-desc">This map contains <?php print number_format($total_features) ?> features:</div> <?php 
  
  // the $headers array is an array of fields to use as the colum headers.
  // additional documentation can be found here
  // https://api.drupal.org/api/drupal/includes%21theme.inc/function/theme_table/7
  $headers = array('Linkage Group', 'Locus Name', 'Type', 'Position');
  
  // the $rows array contains an array of rows where each row is an array
  // of values for each column of the table in that row.  Additional documentation
  // can be found here:
  // https://api.drupal.org/api/drupal/includes%21theme.inc/function/theme_table/7
  $rows = array();
  
  foreach ($feature_positions as $position){
    $nid = chado_get_nid_from_id ('feature', $position->feature_id);
    if (!$nid) {
      $nid = chado_get_nid_from_id ('feature', $position->marker_feature_id);
    }
    $rows[] = array(
      $position->lg,
      $nid ? "<a href='/node/$nid'>" . $position->marker . '</a>': $position->marker,
      $position->type,
      round($position->position, 1) . ' ' . $featuremap->unittype_id->name
    );
  } 
  // the $table array contains the headers and rows array as well as other
  // options for controlling the display of the table.  Additional
  // documentation can be found here:
  // https://api.drupal.org/api/drupal/includes%21theme.inc/function/theme_table/7
  $table = array(
    'header' => $headers,
    'rows' => $rows,
    'attributes' => array(
      'id' => 'tripal_featuremap-table-featurepos',
      'class' => 'tripal-data-table'
    ),
    'sticky' => FALSE,
    'caption' => '',
    'colgroups' => array(),
    'empty' => '',
  );
  
  // once we have our table array structure defined, we call Drupal's theme_table()
  // function to generate the table.
  print theme_table($table);

}

