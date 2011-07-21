<?php
/*
    Edit marker page for Leaflet wordpress plugin
    Hind * created: June 2011
*/
?>
<link rel="stylesheet" href="<?php echo LEAFLET_PLUGIN_URL; ?>/css/admin.css" />
<div class="wrap">
<p><img src="<?php echo LEAFLET_PLUGIN_URL ?>/WP_LEAFLET_Plugin_Logo.png" alt="Leaflet Logo"></p>
<h2>Leaflet Plugin <?php echo PLUGIN_VER ?></h2>
<?php
$action = isset($_POST['action']) ? $_POST['action'] : (isset($_GET['action']) ? $_GET['action'] : '');
$oid = isset($_POST['id']) ? $_POST['id'] : (isset($_GET['id']) ? $_GET['id'] : '');
$mq = get_magic_quotes_gpc();
if (!empty($action)) {
  if (isset($_POST['coords'])) {
    if (preg_match('/(-?\d+(?:\.\d+)?)[^\d.]+?(-?\d+(?:\.\d+)?)/', $_POST['coords'], $res) == 1) {
      $lat = floatval($res[1]);
      $lon = floatval($res[2]);
    }
  }
  $layer = isset($_POST['layer']) ? intval($_POST['layer']) : 0;
  if ($action == 'add') {
    if (isset($lat) and isset($lon)) {
      $result = $wpdb->insert($table_name, array( 'layer' => $layer, 'lon' => $lon, 'lat' => $lat, 'icon' => ($mq ? $_POST['icon'] : $wpdb->escape($_POST['icon'])), 'text' => str_replace(array("\r", "\n"), "", ($mq ? $_POST['text'] : $wpdb->escape($_POST['text']))) ));
      if ($result !== FALSE)
        echo __('Marker added', 'Leaflet');
      else
        echo __('Error on data adding to table ', 'Leaflet').$table_name.'.';
    }
    else {
      echo __('Incorrect coordinates.', 'Leaflet');
    }
  }
  elseif ($action == 'edit') {
    if (isset($lat) and isset($lon)) {
      $result = $wpdb->update($table_name, array( 'layer' => $layer, 'lon' => $lon, 'lat' => $lat, 'icon' => ($mq ? $_POST['icon'] : $wpdb->escape($_POST['icon'])), 'text' => str_replace(array("\r", "\n"), "", ($mq ? $_POST['text'] : $wpdb->escape($_POST['text']))) ), array('id' => $oid));
      if ($result !== FALSE)
        echo __('Marker updated', 'Leaflet');
      else
        echo __('Error on data updating at table ', 'Leaflet').$table_name.'.';
    }
    else {
      echo __('Incorrect coordinates', 'Leaflet');
    }
  }
  elseif ($action == 'delete') {
    if (!empty($oid)) {
      $result = $wpdb->query('DELETE FROM '.$table_name.' WHERE id='.$oid);
      if ($result !== FALSE)
        echo __('Marker deleted', 'Leaflet');
      else
        echo __('Error on data deleting at table ', 'Leaflet').$table_name.'.';
    }
  }
}
else {
  // Get icons list
  $iconlist = array();
  $dir = WP_PLUGIN_DIR.'/leaflet/icons/';
  $ndir = opendir($dir);
  while ($file = readdir($ndir)) {
    if ($file === false)
      break;
    if ($file != "." and $file != "..")
      if (!is_dir($dir.$file) and substr($file, count($file)-5, 4) == '.png')
        $iconlist[] = $file;
  }
  closedir($ndir);

  // Get layers list
  $layerlist = $wpdb->get_results('SELECT * FROM '.$layers_table_name.' WHERE id>0', ARRAY_A);
  $id = '';
  $lon = '0.0';
  $lat = '0.0';
  $coords = '0.0, 0.0';
  $icon = '';
  $text = '';
  $layer = '';
  $isedit = isset($_GET['id']);
  if ($isedit) {
    $id = $_GET['id'];
    $row = $wpdb->get_row('SELECT lat,lon,icon,text,layer FROM '.$table_name.' WHERE id='.$id, ARRAY_A);
    $lon = $row['lon'];
    $lat = $row['lat'];
    $coords = $lat.', '.$lon;
    $icon = $row['icon'];
    $text = $row['text'];
    $layer = $row['layer'];
  }
?>
<link rel="stylesheet" href="<?php echo LEAFLET_LOCATION ?>/leaflet.css" />
<script type="text/javascript" src="<?php echo LEAFLET_LOCATION ?>/leaflet.js"></script>
<form method="post">
<input type="hidden" name="id" value="<?php echo $id ?>" />
<input type="hidden" name="action" value="<?php echo ($isedit ? 'edit' : 'add') ?>" />
<table class="widefat fixed">
  <tr><th class="column-parameter"><?php _e('Parameter', 'Leaflet') ?></th><th class="column-value"><?php _e('Value', 'Leaflet') ?></th></tr>
  <tr><h3><?php _e(($isedit ? 'Edit marker' : 'New marker'), 'Leaflet'); ?></h3></tr>
  <tr>
    <td></td>
    <td><div id="selectlayer"></div></td>
  </tr>
  <tr>
    <td><label for="coords"><?php _e('Coordinates', 'Leaflet') ?>:</label></td>
    <td><input style="width: 100%;" type="text" id="coords" name="coords" value="<?php echo $coords ?>" /><br><small><?php _e('lat, lon (latitude, longitude) or just click on map', 'Leaflet') ?></small></td>
  </tr>
  <tr>
    <td><label for="layer"><?php _e('In layer', 'Leaflet') ?>:</label></td>
    <td>
      <select style="width: 100%;" id="layer" name="layer">
        <option value="0">Без карты</option>
<?php
foreach ($layerlist as $row)
  echo '<option value="'.$row['id'].'"'.($row['id'] == $layer ? ' selected="selected"' : '').'>'.$row['name'].'</option>';
?>
      </select>
    </td>
  </tr>
  <tr>
    <td><label for="icon"><?php _e('Icon', 'Leaflet') ?>:</label></td>
    <td>
      <select style="width: 100%;" id="icon" name="icon" onkeyup="updateicon(this.value);" onchange="updateicon(this.value);">
        <option value=""><?php _e('Default', 'Leaflet') ?></option>
<?php
foreach ($iconlist as $row)
  echo '<option value="'.$row.'"'.($row == $icon ? ' selected="selected"' : '').'>'.$row.'</option>';
?>
      </select>
      <br><div id="previewer"><div><img id="preview" src="<?php echo LEAFLET_PLUGIN_ICONS_URL.'/'.$icon ?>"></div></div>
    </td>
  </tr>
  <tr>
    <td><label for="text"><?php _e('Text', 'Leaflet') ?>:</label></td>
    <td><?php ?><textarea style="width: 100%; height: 400px;" id="text" name="text"><?php echo ($mq ? stripslashes($text) : $text) ?></textarea></td>
  </tr>
</table>
<div class="submit"><input type="submit" name="marker" value="<?php _e(($isedit ? 'Update' : 'Add'), 'Leaflet') ?> &raquo;" /></div>
</form>
<?php
  if ($isedit) {
?>
<form method="post">
<input type="hidden" name="id" value="<?php echo $id ?>" />
<input type="hidden" name="action" value="delete" />
<div class="submit"><input type="submit" name="marker" value="<?php _e('Delete', 'Leaflet') ?> &raquo;" /></div>
</form>
<?php
  }
?>
</div>
<script type="text/javascript">
var marker;
(function($) {
  var selectlayer = new L.Map("selectlayer");
  var layer = new L.TileLayer("http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {maxZoom: 18, attribution: "Map data &copy; 2011 OpenStreetMap contributors"});
  selectlayer.setView(new L.LatLng(<?php echo $coords ?>), <?php echo ($isedit ? 14 : 1) ?>);
  selectlayer.addLayer(layer);
  marker = new L.Marker(new L.LatLng(<?php echo $coords ?>));
  selectlayer.addLayer(marker);
  selectlayer.on('click', function(e) {
      document.getElementById('coords').value = e.latlng.lat.toFixed(4) + ', ' + e.latlng.lng.toFixed(4);
      marker.setLatLng(e.latlng);
  });
})(jQuery)
function updateicon(newicon) {
  document.getElementById('preview').src = "<?php echo LEAFLET_PLUGIN_ICONS_URL.'/' ?>" + newicon;
}
</script>
<?php
}
?>
