<?php
/*
    Maps page for Leaflet wordpress plugin
    Hind * created: June 2011
*/
?>

<link rel="stylesheet" href="<?php echo LEAFLET_PLUGIN_URL; ?>/css/admin.css" />
<p><img src="<?php echo LEAFLET_PLUGIN_URL ?>/WP_LEAFLET_Plugin_Logo.png" alt="Leaflet Logo"></p>
<h2>Leaflet Plugin <?php echo PLUGIN_VER ?> </h2>
<table cellspacing="0" class="wp-list-table widefat fixed bookmarks">
  <thead>
  <tr>
    <th style="" class="manage-column column-cb check-column" id="cb" scope="col"><input type="checkbox"></th><th style="" class="manage-column column-id sortable desc" id="id" scope="col"><a href="<?php echo WP_ADMIN_URL; ?>admin.php?page=leaflet_layers&orderby=id&order=asc"><span>Id</span><span class="sorting-indicator"></span></a></th><th style="" class="manage-column column-name sortable desc" id="name" scope="col"><a href="<?php echo WP_ADMIN_URL; ?>admin.php?page=leaflet_layers&orderby=name&order=asc"><span><?php _e('Name', 'Leaflet') ?></span><span class="sorting-indicator"></span></a></th><th class="manage-column column-code" id="code" scope="col"><?php _e('Shortcode', 'Leaflet') ?></th></tr>
  </thead>
  <tfoot>
  <tr>
    <th style="" class="manage-column column-cb check-column" scope="col"><input type="checkbox"></th><th style="" class="manage-column column-id sortable desc" scope="col"><a href="<?php echo WP_ADMIN_URL; ?>admin.php?page=leaflet_layers&orderby=id&order=desc"><span>Id</span><span class="sorting-indicator"></span></a></th><th style="" class="manage-column column-name sortable desc" scope="col"><a href="<?php echo WP_ADMIN_URL; ?>admin.php?page=leaflet_layers&orderby=name&order=desc"><span><?php _e('Name', 'Leaflet') ?></span><span class="sorting-indicator"></span></a></th><th class="manage-column column-code" scope="col"><?php _e('Shortcode', 'Leaflet') ?></th></tr>
  </tfoot>
  <tbody id="the-list">
<?php
  if (count($layerlist) < 1)
    echo '<tr><td colspan="3">'.__('Layers not found', 'Leaflet').'</td></tr>';
  else
    foreach ($layerlist as $row)
      echo '<tr valign="middle" class="alternate" id="link-'.$row['id'].'"><th class="check-column" scope="row"><input type="checkbox" value="'.$row['id'].'" name="layercheck[]"></th><td class="column-id">'.$row['id'].'</td><td class="column-name"><strong><a title="'.__('Edit', 'Leaflet').' &laquo;'.$row['name'].'&raquo;" href="'.WP_ADMIN_URL.'admin.php?page=leaflet_layer&id='.$row['id'].'" class="row-title">'.$row['name'].'</a></strong><br><div class="row-actions"><span class="edit"><a href="'.WP_ADMIN_URL.'admin.php?page=leaflet_layer&id='.$row['id'].'">'.__('Edit', 'Leaflet').'</a> | </span><span class="delete"><a onclick="if ( confirm( \''.__('You attempt to delete layer', 'Leaflet').' «'.$row['name'].'».\' ) ) { return true;}return false;" href="'.WP_ADMIN_URL.'admin.php?page=leaflet_layer&action=delete&id='.$row['id'].'" class="submitdelete">'.__('Delete', 'Leaflet').'</a></span></div></td><td>[leaflet layer="'.$row['id'].'"]</td></tr>';
?>
  </tbody>
</table>
