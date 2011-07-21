<?php
/*
    Edit layer page for Leaflet wordpress plugin
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
if (!empty($action)) {
  if ($action == 'add') {
    if (isset($_POST['name']) and !empty($_POST['name'])) {
      $result = $wpdb->insert($table_name, array('name' => $wpdb->escape($_POST['name'])));
      if ($result !== FALSE)
        echo __('Layer added, shortcode: ', 'Leaflet').'<br>'.'[leaflet layer="'.$wpdb->insert_id.'"]';
      else
        echo __('Error on data adding to table ', 'Leaflet').$table_name.'.';
    }
  }
  elseif ($action == 'edit') {
    if (isset($_POST['name']) and !empty($_POST['name']) and !empty($oid)) {
      $result = $wpdb->update($table_name, array('name' => $wpdb->escape($_POST['name'])), array('id' => $oid));
      if ($result !== FALSE)
        echo __('Layer updated, shortcode: ', 'Leaflet').'<br>'.'[leaflet layer="'.$oid.'"]';
      else
        echo __('Error on data updating at table ', 'Leaflet').$table_name.'.';
    }
  }
  elseif ($action == 'delete') {
    if (!empty($oid)) {
      $result = $wpdb->update($table_name.'_layers', array('layer' => 0), array('layer' => $oid));
      $result = $wpdb->query('DELETE FROM '.$table_name.' WHERE id='.$oid);
      if ($result !== FALSE)
        echo __('Layer deleted', 'Leaflet');
      else
        echo __('Error on data deleting at table ', 'Leaflet').$table_name.'.';
    }
  }
}
else {
  $id = '';
  $name = '';
  $isedit = isset($_GET['id']);
  if ($isedit) {
    $id = $_GET['id'];
    $name = $wpdb->get_var('SELECT name FROM '.$table_name.' WHERE id='.$id);
  }
?>
<form method="post">
<input type="hidden" name="id" value="<?php echo $id ?>" />
<input type="hidden" name="action" value="<?php echo ($isedit ? 'edit' : 'add') ?>" />
<table class="widefat fixed">
  <tr><th class="column-parameter"><?php _e('Parameter', 'Leaflet') ?></th><th class="column-value"><?php _e('Value', 'Leaflet') ?></th></tr>
  <tr><h3><?php _e(($isedit ? 'Edit layer' : 'New layer'), 'Leaflet'); ?></h3></tr>
  <tr>
    <td><label for="name"><?php _e('Layer name', 'Leaflet') ?>:</label></td>
    <td><input style="width: 100%;" type="text" name="name" value="<?php echo $name ?>" /></td>
  </tr>
</table>
<div class="submit"><input type="submit" name="layer" value="<?php _e(($isedit ? 'Update' : 'Add'), 'Leaflet') ?> &raquo;" /></div>
</form>
<?php
  if ($isedit) {
?>
<form method="post">
<input type="hidden" name="id" value="<?php echo $id ?>" />
<input type="hidden" name="action" value="delete" />
<div class="submit"><input type="submit" name="layer" value="<?php _e('Delete', 'Leaflet') ?> &raquo;" /></div>
</form>
<?php
  }
?>
</div>
<?php
}
?>
