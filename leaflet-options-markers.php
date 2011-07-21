<?php
/*
    Markers page for Leaflet wordpress plugin
    Hind * created: June 2011
*/

// Pages
$pager = '';
if ($mcount > $pp) {
  $maxpage = intval(ceil($mcount / $pp)); // 2
  if ($maxpage > 1) {
    $pager .= '<form method="POST" action="'.WP_ADMIN_URL.'admin.php?page=leaflet_markers"><div class="tablenav top"><div class="tablenav-pages">';
    if ($pagenum > (2 + $radius * 2)) {
      foreach (range(1, 1 + $radius) as $num)
        $pager .= '<a href="'.WP_ADMIN_URL.'admin.php?page=leaflet_markers&paged='.$num.'" class="first-page">'.$num.'</a>';
      $pager .= ' … ';
      foreach (range($pagenum - $radius, $pagenum - 1) as $num)
        $pager .= '<a href="'.WP_ADMIN_URL.'admin.php?page=leaflet_markers&paged='.$num.'" class="first-page">'.$num.'</a>';
    }
    else
      if ($pagenum > 1)
        foreach (range(1, $pagenum - 1) as $num)
          $pager .= '<a href="'.WP_ADMIN_URL.'admin.php?page=leaflet_markers&paged='.$num.'" class="first-page">'.$num.'</a>';
    $pager .= '<span class="paging-input"><input type="text" size="2" value="'.$pagenum.'" name="paged" class="current-page"> из <span class="total-pages">'.$maxpage.' </span></span>';
    if (($maxpage - $pagenum) >= (2 + $radius * 2)) {
      foreach (range($pagenum + 1, $pagenum + $radius) as $num)
        $pager .= '<a href="'.WP_ADMIN_URL.'admin.php?page=leaflet_markers&paged='.$num.'" class="first-page">'.$num.'</a>';
      $pager .= ' … ';
      foreach (range($maxpage - $radius, $maxpage) as $num)
        $pager .= '<a href="'.WP_ADMIN_URL.'admin.php?page=leaflet_markers&paged='.$num.'" class="first-page">'.$num.'</a>';
    }
    else
      if ($pagenum < $maxpage)
        foreach (range($pagenum + 1, $maxpage) as $num)
          $pager .= '<a href="'.WP_ADMIN_URL.'admin.php?page=leaflet_markers&paged='.$num.'" class="first-page">'.$num.'</a>';
    $pager .= '</div></div></form>';
  }
}

?>

<link rel="stylesheet" href="<?php echo LEAFLET_PLUGIN_URL; ?>/css/admin.css" />
<p><img src="<?php echo LEAFLET_PLUGIN_URL ?>/WP_LEAFLET_Plugin_Logo.png" alt="Leaflet Logo"></p>
<h2>Leaflet Plugin <?php echo PLUGIN_VER ?> </h2>
<?php echo $pager; ?>
<form method="POST">
<table cellspacing="0" class="wp-list-table widefat fixed bookmarks">
  <thead>
  <tr>
    <th class="manage-column column-cb check-column" scope="col"><input type="checkbox"></th>
    <th class="manage-column column-id sortable desc" scope="col"><a href="<?php echo WP_ADMIN_URL; ?>admin.php?page=leaflet_markers&orderby=id&order=desc"><span>Id</span><span class="sorting-indicator"></span></a></th>
    <th class="manage-column column-layername sortable desc" scope="col"><a href="<?php echo WP_ADMIN_URL; ?>admin.php?page=leaflet_markers&orderby=layername&order=desc"><span><?php _e('Layer', 'Leaflet') ?></span><span class="sorting-indicator"></span></a></th>
    <th class="manage-column column-coords" scope="col"><?php _e('Coordinates', 'Leaflet') ?></th>
    <th class="manage-column column-icon sortable desc" scope="col"><a href="<?php echo WP_ADMIN_URL; ?>admin.php?page=leaflet_markers&orderby=icon&order=desc"><span><?php _e('Icon', 'Leaflet') ?></span><span class="sorting-indicator"></span></a></th>
    <th class="manage-column column-content sortable desc" scope="col"><a href="<?php echo WP_ADMIN_URL; ?>admin.php?page=leaflet_markers&orderby=content&order=desc"><span><?php _e('Content', 'Leaflet') ?></span><span class="sorting-indicator"></span></a></th>
    <th class="manage-column column-code" id="code" scope="col"><?php _e('Shortcode', 'Leaflet') ?></th>
  </tr>
  </thead>
  <tfoot>
  <tr>
    <th class="manage-column column-cb check-column" scope="col"><input type="checkbox"></th>
    <th class="manage-column sortable desc" scope="col"><a href="<?php echo WP_ADMIN_URL; ?>admin.php?page=leaflet_markers&orderby=id&order=desc"><span>Id</span><span class="sorting-indicator"></span></a></th>
    <th class="manage-column sortable desc" scope="col"><a href="<?php echo WP_ADMIN_URL; ?>admin.php?page=leaflet_markers&orderby=layername&order=desc"><span><?php _e('Layer', 'Leaflet') ?></span><span class="sorting-indicator"></span></a></th>
    <th class="manage-column" scope="col"><?php _e('Coordinates', 'Leaflet') ?></th>
    <th class="manage-column sortable desc" scope="col"><a href="<?php echo WP_ADMIN_URL; ?>admin.php?page=leaflet_markers&orderby=icon&order=desc"><span><?php _e('Icon', 'Leaflet') ?></span><span class="sorting-indicator"></span></a></th>
    <th class="manage-column sortable desc" scope="col"><a href="<?php echo WP_ADMIN_URL; ?>admin.php?page=leaflet_markers&orderby=content&order=desc"><span><?php _e('Content', 'Leaflet') ?></span><span class="sorting-indicator"></span></a></th>
    <th class="manage-column column-code" scope="col"><?php _e('Shortcode', 'Leaflet') ?></th>
  </tr>
  </tfoot>
  <tbody id="the-list">
<?php
  if (count($marklist) < 1)
    echo '<tr><td colspan="6">'.__('Markers not found', 'Leaflet').'</td></tr>';
  else
    foreach ($marklist as $row)
      echo '<tr valign="middle" class="alternate" id="link-'.$row['id'].'">
      <th class="check-column" scope="row"><input type="checkbox" value="'.$row['id'].'" name="markercheck[]"></th>
      <td class="column-id">'.$row['id'].'</td>
      <td class="column-layer">'.$row['layername'].'</td>
      <td class="column-coords">'.$row['coords'].'</td>
      <td class="column-icon">'.$row['icon'].'&nbsp<img style="height: 24px;" src="'.LEAFLET_PLUGIN_ICONS_URL.'/'.$row['icon'].'"></td>
      <td class="column-content"><strong><a title="'.__('Edit marker', 'Leaflet').' '.$row['id'].'" href="'.WP_ADMIN_URL.'admin.php?page=leaflet_marker&id='.$row['id'].'" class="row-title">'.mb_substr(strip_tags($row['text']), 0, 64).'...</a></strong><br><div class="row-actions"><span class="edit"><a href="'.WP_ADMIN_URL.'admin.php?page=leaflet_marker&id='.$row['id'].'">'.__('Edit', 'Leaflet').'</a> | </span><span class="delete"><a onclick="if ( confirm( \''.__('You attempt to delete marker', 'Leaflet').' '.$row['id'].'.\' ) ) { return true;}return false;" href="'.WP_ADMIN_URL.'admin.php?page=leaflet_marker&action=delete&id='.$row['id'].'" class="submitdelete">'.__('Delete', 'Leaflet').'</a></span></div></td>
      <td class="column-code">[leaflet marker="'.$row['id'].'"]</td>';
?>
  </tbody>
</table>
</form>
<?php echo $pager; ?>
