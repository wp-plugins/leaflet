<?php
/*
    Option page for Leaflet wordpress plugin
    Hind * created: June 2011
*/
if(isset($_POST['options'])) {   
  if ($_POST['leaflet_zoom_level'] >= ZOOM_LEVEL_MIN && $_POST['leaflet_zoom_level'] <= ZOOM_LEVEL_MAX){
    update_option('leaflet_zoom_level', $_POST['leaflet_zoom_level']);
  }
  update_option('leaflet_width', $_POST['leaflet_width']);
  update_option('leaflet_height', $_POST['leaflet_height']);
  update_option('leaflet_tiles', $_POST['leaflet_tiles']);
  update_option('leaflet_location', $_POST['leaflet_location']);
}
else {
  add_option('leaflet_zoom_level', 0);
  add_option('leaflet_width', 640);
  add_option('leaflet_height', 480);
  add_option('leaflet_tiles', 'Mapnik');
  add_option('leaflet_location', 'http://leaflet.cloudmade.com/dist');
}
$leaflet_zoom_level = get_option('leaflet_zoom_level');
$leaflet_width = get_option('leaflet_width');
$leaflet_height = get_option('leaflet_height');
$leaflet_tiles = get_option('leaflet_tiles');
$leaflet_location = get_option('leaflet_location');
?>

<link rel="stylesheet" href="<?php echo LEAFLET_PLUGIN_URL; ?>/css/admin.css" />
<div class="wrap">
<p><img src="<?php echo LEAFLET_PLUGIN_URL ?>/WP_LEAFLET_Plugin_Logo.png" alt="Leaflet Logo"></p>
<h2>Leaflet Plugin <?php echo PLUGIN_VER ?> </h2>
<form method="post">
<table class="widefat fixed">
  <tr><th class="column-parameter"><?php _e('Parameter', 'Leaflet') ?></th><th class="column-value"><?php _e('Value', 'Leaflet') ?></th></tr>
  <tr> <h3><?php _e('Default properties', 'Leaflet'); ?></h3> </tr>
  <tr>
    <td><label for="leaflet_width"><?php _e('Map width', 'Leaflet') ?>:</label></td>
    <td><input style="width: 100%;" type="text" id="leaflet_width" name="leaflet_width" value="<?php echo $leaflet_width ?>" /></td>
  </tr>
  <tr>
    <td><label for="leaflet_height"><?php _e('Map height', 'Leaflet') ?>:</label></td>
    <td><input style="width: 100%;" type="text" id="leaflet_height" name="leaflet_height" value="<?php echo $leaflet_height ?>" /></td>
  </tr>
  <tr>
    <td><label for="leaflet_zoom_level"><?php _e('Map zoomlevel', 'Leaflet') ?>:</label></td>
    <td><input style="width: 100%;" type="text" id="leaflet_zoom_level" name="leaflet_zoom_level" value="<?php echo $leaflet_zoom_level ?>" /></td>
  </tr>
  <tr>
    <td><label for="leaflet_tiles"><?php _e('Tiles URL or Mapnik/Osmarender', 'Leaflet') ?>:</label></td>
    <td><input style="width: 100%;" type="text" id="leaflet_tiles" name="leaflet_tiles" value="<?php echo $leaflet_tiles ?>" /><br><small><?php echo __('For example, ', 'Leaflet').'http://{s}.tile.cloudmade.com/BC9A493B41014CAABB98F0471D759707/997/256/{z}/{x}/{y}.png'; ?></small></td>
  </tr>
  <tr>
    <td><label for="leaflet_location"><?php _e('Leaflet library location', 'Leaflet') ?>:</label></td>
    <td><input style="width: 100%;" type="text" id="leaflet_location" name="leaflet_location" value="<?php echo $leaflet_location ?>" /><br><small><?php _e('Place where leaflet.js and leaflet.css are', 'Leaflet'); ?></small></td>
  </tr>
</table>
<div class="submit"><input type="submit" name="options" value="<?php _e('Save Changes', 'Leaflet') ?> &raquo;" /></div>
</div>
</form>
