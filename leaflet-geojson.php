<?php
/*
    GeoJSON generator for Leaflet wordpress plugin
    Hind * created: June 2011
*/
if (isset($_GET['layer'])) {
  include_once(dirname($_SERVER['SCRIPT_FILENAME']).'/leaflet-wp-path.php');
  include_once(WP_PATH.'wp-config.php');
  include_once(WP_PATH.'wp-includes/wp-db.php');
  global $wpdb;
  $mq = get_magic_quotes_gpc();
  $table_name = $wpdb->prefix.'leaflet';
  $layers_table_name = $wpdb->prefix.'leaflet_layers';
  $layer = $_GET['layer'];
  
  $q = 'LIMIT 0';
  if ($layer == '*' or $layer == 'all')
    $q = 'LIMIT 500';
  else {
    $layers = explode(',', $layer);
    $checkedlayers = array();
    foreach ($layers as $clayer) {
      if (intval($clayer) > 0)
        $checkedlayers[] = intval($clayer);
    }
    if (count($checkedlayers) > 0)
      $q = 'WHERE layer IN ('.implode(',', $checkedlayers).')';
  }
  $sql = 'SELECT CONCAT(lon,\',\',lat) AS coords,A.layer,B.name AS layername,A.icon,A.text FROM '.$table_name.' AS A JOIN '.$layers_table_name.' AS B ON A.layer=B.id '.$q;
  $markers = $wpdb->get_results($sql, ARRAY_A);
  $first = true;
  echo '{"type":"FeatureCollection","features":[';
  foreach ($markers as $marker) {
    if ($first) $first = false;
    else echo ',';
    echo '{"type":"Feature","geometry":{"type":"Point","coordinates":['.$marker['coords'].']},"properties":{"layer":'.$marker['layer'].',"layername":"'.$marker['layername'].'","icon":"'.$marker['icon'].'","text":"'.str_replace('"', '\"', ($mq ? stripslashes($marker['text']) : $marker['text'])).'"}}';
  }
  echo ']}';
}
elseif (isset($_GET['marker'])) {
  include_once(dirname($_SERVER['SCRIPT_FILENAME']).'/leaflet-wp-path.php');
  include_once(WP_PATH.'wp-config.php');
  include_once(WP_PATH.'wp-includes/wp-db.php');
  global $wpdb;
  $mq = get_magic_quotes_gpc();
  $table_name = $wpdb->prefix.'leaflet';
  $layers_table_name = $wpdb->prefix.'leaflet_layers';
  $markerid = $_GET['marker'];

  $markers = explode(',', $markerid);
  $checkedmarkers = array();
  foreach ($markers as $cmarker) {
    if (intval($cmarker) > 0)
      $checkedmarkers[] = intval($cmarker);
  }
  if (count($checkedmarkers) > 0)
    $q = 'WHERE A.id IN ('.implode(',', $checkedmarkers).')';
  else
    die();

  $sql = 'SELECT CONCAT(lon,\',\',lat) AS coords,A.layer,B.name AS layername,A.icon,A.text FROM '.$table_name.' AS A JOIN '.$layers_table_name.' AS B ON A.layer=B.id '.$q;
  $markers = $wpdb->get_results($sql, ARRAY_A);
  $first = true;
  echo '{"type":"FeatureCollection","features":[';
  foreach ($markers as $marker) {
    if ($first) $first = false;
    else echo ',';
    echo '{"type":"Feature","geometry":{"type":"Point","coordinates":['.$marker['coords'].']},"properties":{"layer":'.$marker['layer'].',"layername":"'.$marker['layername'].'","icon":"'.$marker['icon'].'","text":"'.str_replace('"', '\"', ($mq ? stripslashes($marker['text']) : $marker['text'])).'"}}';
  }
  echo ']}';
}

?>
