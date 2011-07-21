<?php
/*
Plugin Name: Leaflet
Plugin URI: http://www.nanodesu.ru/wp-leaflet-plugin
Description: Embeds maps in your blog and adds geo data to your posts.  Find samples and a forum on the <a href="http://www.Fotomobil.at/wp-osm-plugin">OSM plugin page</a>.  Simply create the shortcode to add it in your post at [<a href="options-general.php?page=leaflet.php">Settings</a>]
Version: 0.0.1
Author: Hind
Author URI: http://www.nanodesu.ru
Minimum WordPress Version Required: 3.1.0
*/

/*  (c) Copyright 2011  Hind

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
load_plugin_textdomain('leaflet', false, basename(dirname(__FILE__)));

define ("PLUGIN_VER", "v. 0.0.1");

define (ZOOM_LEVEL_MAX, 18); // standard is 17, only mapnik is 18
define (ZOOM_LEVEL_MIN, 1);

if ( ! defined( 'WP_CONTENT_URL' ) )
      define( 'WP_CONTENT_URL', get_option( 'siteurl' ) . '/wp-content' );
if ( ! defined( 'WP_CONTENT_DIR' ) )
      define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
if ( ! defined( 'WP_PLUGIN_URL' ) )
      define( 'WP_PLUGIN_URL', WP_CONTENT_URL. '/plugins' );
if ( ! defined( 'WP_PLUGIN_DIR' ) )
      define( 'WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins' );
if ( ! defined( 'WP_ADMIN_URL' ) )
      define( 'WP_ADMIN_URL', get_admin_url() );
define ("LEAFLET_PLUGIN_URL", WP_PLUGIN_URL."/leaflet");
define ("LEAFLET_PLUGIN_ICONS_URL", LEAFLET_PLUGIN_URL."/icons");
define ("LEAFLET_LOCATION", get_option('leaflet_location'));

global $wp_version;
if (version_compare($wp_version,"2.5.1","<")){
  exit('[Leaflet plugin - ERROR]: At least Wordpress Version 2.5.1 is needed for this plugin!');
}

class Leaflet
{
  function Leaflet() {
    $this->localizionName = 'Leaflet';

    add_action('wp_head', array(&$this, 'wp_head'));
    add_action('admin_menu', array(&$this, 'admin_menu'));
    add_action('wp_print_scripts', array(&$this, 'show_enqueue_script'));
    add_shortcode('leaflet', array(&$this, 'showmap'));
    add_action('category_edit_form_fields', array(&$this, 'category_edit_form_fields'));
    add_action('category_add_form_fields', array(&$this, 'category_edit_form_fields'));
    add_action('edit_category', array(&$this, 'edit_category'));
    add_action('create_category', array(&$this, 'edit_category'));
  }

  function category_edit_form_fields () {
    global $wpdb;
    $catmaps_table_name = $wpdb->prefix.'leaflet_catmaps';
    $text = $wpdb->get_var('SELECT text FROM '.$catmaps_table_name.' WHERE id='.$_GET['tag_ID']);
  ?>
  <tr class="form-field">
          <th valign="top" scope="row">
              <label for="leaflet_mapcode"><?php _e('Map shortcode', 'leaflet'); ?></label>
          </th>
          <td>
              <input type="text" id="leaflet_mapcode" name="leaflet_mapcode" value="<?php echo htmlspecialchars($text); ?>" />
          </td>
      </tr>
  <?php 
  }

  function edit_category($catid) {
    global $wpdb;
    $catmaps_table_name = $wpdb->prefix.'leaflet_catmaps';
    $text = str_replace(array("\r", "\n", "\t"), " ", $_POST['leaflet_mapcode'] );
    if ($wpdb->get_var("SELECT COUNT(*) FROM ".$catmaps_table_name." WHERE id=".$catid) > 0)
      $wpdb->query("UPDATE ".$catmaps_table_name." SET text = '".$text."' WHERE id = ".$catid);
    else
      $wpdb->query("INSERT INTO ".$catmaps_table_name." VALUES (".$catid.", '".$text."')");
  }

  function wp_head() {
    echo '<link rel="stylesheet" href="'.LEAFLET_LOCATION.'/leaflet.css" />';
    echo '<link rel="stylesheet" href="'.LEAFLET_PLUGIN_URL.'/css/post.css" />';
    echo '<script type="text/javascript" src="'.LEAFLET_LOCATION.'/leaflet.js"></script>';
  }

  function leaflet_options_defaults()
  {
    include('leaflet-options.php');	
  }

  function leaflet_options_layers()
  {
    global $wpdb;
    $table_name = $wpdb->prefix.'leaflet_layers';
    $layerlist = $wpdb->get_results('SELECT * FROM '.$table_name.' WHERE id>0', ARRAY_A);
    include('leaflet-options-layers.php');
  }

  function leaflet_options_markers()
  {
    global $wpdb;
    $table_name = $wpdb->prefix.'leaflet';
    $layers_table_name = $wpdb->prefix.'leaflet_layers';

    $mcount = intval($wpdb->get_var('SELECT COUNT(*) FROM '.$table_name));
    $pp = 10;
    $radius = 1;
    $pagenum = isset($_POST['paged']) ? intval($_POST['paged']) : (isset($_GET['paged']) ? intval($_GET['paged']) : 1);
    $start = ($pagenum - 1) * $pp;
    if ($start > $mcount or $start < 0)
        $start = 0;
    $marklist = $wpdb->get_results('SELECT A.id,CONCAT(A.lat,\',\',A.lon) AS coords,A.icon,A.text,A.layer,B.name AS layername FROM '.$table_name.' AS A JOIN '.$layers_table_name.' AS B ON A.layer=B.id LIMIT '.$pp.' OFFSET '.$start, ARRAY_A);
    include('leaflet-options-markers.php');
  }

  function leaflet_layer()
  {
    global $wpdb;
    $table_name = $wpdb->prefix.'leaflet_layers';
    include('leaflet_layer.php');
  }

  function leaflet_marker()
  {
    global $wpdb;
    $table_name = $wpdb->prefix.'leaflet';
    $layers_table_name = $wpdb->prefix.'leaflet_layers';
    include('leaflet_marker.php');
  }

  function showmap($atts) {
    $uid = substr(md5(''.rand()), 0, 8);
    extract(shortcode_atts(array(
    'width' => get_option('leaflet_width', 640), 'height' => get_option('leaflet_height', 480), 
    'lat' => '', 'lon' => '',
    'mlat' => '', 'mlon' => '',
    'mtext' => '',
    'micon' => '',
    'zoom' => get_option('leaflet_zoom_level', 11),     
    'tiles' => get_option('leaflet_tiles', 'Mapnik'),
    'geojson' => '',
    'geojsonurl' => '',
    'layer' => '',
    'marker' => '',
    'panel' => false,
    'pwidth' => 260,
    'mapname' => 'lmap'.$uid
    ), $atts));
    $pname = 'pa'.$uid;

    if ($zoom < ZOOM_LEVEL_MIN || $zoom > ZOOM_LEVEL_MAX) {
      $zoom = 0;
    }
    if ($width < 1 || $height < 1) {
      $width = get_option('leaflet_width', 640); $height = get_option('leaflet_height', 480);
    }
    if (empty($lat) or empty($lon)) {
      $lat = (empty($mlat) or empty($mlon)) ? '58.05' : $mlat;
      $lon = (empty($mlat) or empty($mlon)) ? '38.83' : $mlon;
    }
    if (strcasecmp($tiles, 'Mapnik') == 0)
      $turl = 'http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
    elseif (strcasecmp($tiles, 'Osmarender') == 0)
      $turl = 'http://{s}.tah.openstreetmap.org/Tiles/tile/{z}/{x}/{y}.png';
    elseif (!empty($tiles))
      $turl = $tiles;

    if (OSM_LIBS_LOADED == 0) {
      //$out = '<link rel="stylesheet" href="'.LEAFLET_LOCATION.'/leaflet.css" />';
      //$out .= '<script type="text/javascript" src="'.LEAFLET_LOCATION.'/leaflet.js"></script>';
      define (OSM_LIBS_LOADED, 1);
    }

    if ($panel) {
      $out .= '<table style="position: relative; z-index: 1; background: #fff; width:'.($width + $pwidth).'px; height:'.$height.'px;" cellspacing="0"><tr><td style="vertical-align: top; padding: 0; width: 100%; height: 100%;">';
      $out .= '<div id="'.$mapname.'" style="width:100%; height:100%; overflow:hidden;padding:0;border:0;"></div>';
      $out .= '</td><td style="vertical-align: top; padding: 0; width: '.$pwidth.'px; height: '.$height.'px;"><div class="lpanel" id="'.$pname.'"><div style="margin: 5px;"><input type=\'checkbox\' id=\'checkfs\' onchange=\'togglefs(this);\' /><label for=\'checkfs\'>На весь экран</label></div></div></td></tr></table>';
    }
    else
      $out .= '<div id="'.$mapname.'" style="width:'.$width.'px; height:'.$height.'px; overflow:hidden;padding:0;border:0;"></div>';
    $out .= '<script type="text/javascript">';
    $out .= 'var layers = {};';
    $out .= 'var markers = {};';
    $out .= 'var '.$mapname.';';
    if ($panel) {
      $out .= 'var pname = "'.$pname.'";';
    }
    $out .= '(function($) {';
    $out .= $mapname.' = new L.Map("'.$mapname.'");';
    $out .= 'var layer = new L.TileLayer("'.$turl.'", {maxZoom: 18, attribution: "Map data &copy; 2011 OpenStreetMap contributors"});';
    $out .= $mapname.'.setView(new L.LatLng('.$lat.', '.$lon.'), '.$zoom.');';
    $out .= $mapname.'.addLayer(layer);';
    if (!(empty($mlat) or empty($mlon))) {
      $out .= 'var marker = new L.Marker(new L.LatLng('.$mlat.', '.$mlon.'));';
      if (!empty($mtext)) $out .= 'marker.bindPopup("'.str_replace(array("\n", "\r"), "", $mtext).'");';
      if (!empty($micon)) $out .= 'marker.options.icon = new L.Icon("'.LEAFLET_PLUGIN_ICONS_URL.'/" + e.properties.icon);';
      $out .= $mapname.'.addLayer(marker);';
    }
    if (!empty($geojson) or !empty($geojsonurl) or !empty($layer) or !empty($marker)) {
      $out .= 'var geojson = new L.GeoJSON();';
      $out .= 'geojson.on("featureparse",  function(e) {';
      $out .= 'if (typeof e.properties.text != \'undefined\') e.layer.bindPopup(e.properties.text);';
      $out .= 'if (typeof e.properties.icon != \'undefined\') e.layer.options.icon = new L.Icon("'.LEAFLET_PLUGIN_ICONS_URL.'/" + e.properties.icon);';
      $out .= 'layers[e.properties.layer] = e.properties.layername;';
      $out .= 'if (typeof markers[e.properties.layer] == \'undefined\') markers[e.properties.layer] = [];';
      $out .= 'markers[e.properties.layer].push(e.layer);';
      $out .= '});';
      $out .= 'var geojsonObj;';
      if (!empty($geojson)) {
        $out .= 'geojsonObj = eval("'.$geojson.'");';
        $out .= 'geojson.addGeoJSON(geojsonObj);';
      }
      if (!empty($geojsonurl)) {
        $out .= 'geojsonObj = eval("(" + jQuery.ajax({url: "'.$geojsonurl.'", async: false}).responseText + ")");';
        $out .= 'geojson.addGeoJSON(geojsonObj);';
      }
      if (!empty($layer)) {
        $out .= 'geojsonObj = eval("(" + jQuery.ajax({url: "'.LEAFLET_PLUGIN_URL.'/leaflet-geojson.php?layer='.$layer.'", async: false}).responseText + ")");';
        $out .= 'geojson.addGeoJSON(geojsonObj);';
      }
      if (!empty($marker)) {
        $out .= 'geojsonObj = eval("(" + jQuery.ajax({url: "'.LEAFLET_PLUGIN_URL.'/leaflet-geojson.php?marker='.$marker.'", async: false}).responseText + ")");';
        $out .= 'geojson.addGeoJSON(geojsonObj);';
      }
      $out .= $mapname.'.addLayer(geojson);';
      if ($panel) {
        $out .= 'document.getElementById("'.$pname.'").innerHTML += "<ul></ul>";';
        $out .= 'var ul = document.getElementById("'.$pname.'").getElementsByTagName("ul")[0];';
        $out .= 'for (var l in layers) { ul.innerHTML += "<li><input type=\'checkbox\' value=\'" + l + "\' id=\'check" + l + "\' onchange=\'togglelayer(this);\' /><label for=\'check" + l + "\'>" + layers[l] + "</label></li>";';
        $out .= 'for (var m in markers[l]) { markers[l][m]._shadow.style.display = markers[l][m]._icon.style.display = "none"; } }';
      }
    }
    $out .= '})(jQuery);';
    if ($panel) {
      $out .= 'function togglelayer(checkbox) { var layer = markers[parseInt(checkbox.value)]; for (var mark in layer) { layer[mark]._shadow.style.display = layer[mark]._icon.style.display = (checkbox.checked ? "block" : "none"); } }';
      $out .= 'function togglefs(checkbox) { '.$mapname.'._container.parentNode.parentNode.parentNode.parentNode.className = (checkbox.checked ? "fullscreen" : ""); '.$mapname.'.invalidateSize(); }';
    }
    $out .= '</script>';
    return $out;
  }

  // add Leaflet-config page to Settings
  function admin_menu($not_used) {
    add_menu_page(__('Leaflet settings', 'Leaflet'), 'Leaflet', 8, 'leaflet_defaults', array(&$this, 'leaflet_options_defaults') );
    add_submenu_page('leaflet_defaults', __('Defaults', 'Leaflet'), __('Defaults', 'Leaflet'), 8, 'leaflet_defaults', array(&$this, 'leaflet_options_defaults') );
    add_submenu_page('leaflet_defaults', __('Layers', 'Leaflet'), __('Layers', 'Leaflet'), 8, 'leaflet_layers', array(&$this, 'leaflet_options_layers') );
    add_submenu_page('leaflet_defaults', __('Edit layer', 'Leaflet'), __('New layer', 'Leaflet'), 8, 'leaflet_layer', array(&$this, 'leaflet_layer') );
    add_submenu_page('leaflet_defaults', __('Markers', 'Leaflet'), __('Markers', 'Leaflet'), 8, 'leaflet_markers', array(&$this, 'leaflet_options_markers') );
    add_submenu_page('leaflet_defaults', __('Edit marker', 'Leaflet'), __('New marker', 'Leaflet'), 8, 'leaflet_marker', array(&$this, 'leaflet_marker') );
  }

  // ask WP to handle the loading of scripts
  // if it is not admin area
  function show_enqueue_script() {
    wp_enqueue_script(array ('jquery'));
  }
}  // End class

function activate() {
  global $wpdb;
  file_put_contents(dirname(__FILE__).'/leaflet-wp-path.php', '<?php define(\'WP_PATH\',\''.ABSPATH.'\'); ?>');
  $table_name = $wpdb->prefix.'leaflet';
  $layers_table_name = $wpdb->prefix.'leaflet_layers';
  $catmaps_table_name = $wpdb->prefix.'leaflet_catmaps';
  if($wpdb->get_var("SHOW TABLES LIKE '".$table_name."'") != $table_name) {
    $sql = "CREATE TABLE `".$table_name."` (
      `id` INT(6) UNSIGNED NOT NULL AUTO_INCREMENT,
      `layer` INT(6) UNSIGNED NOT NULL DEFAULT 0,
      `lon` FLOAT NOT NULL DEFAULT '0',
      `lat` FLOAT NOT NULL DEFAULT '0',
      `icon` VARCHAR(255) NULL DEFAULT NULL,
      `text` TEXT NULL,
      PRIMARY KEY (`id`));";
    $wpdb->query($sql);
  }
  if($wpdb->get_var("SHOW TABLES LIKE '".$layers_table_name."'") != $layers_table_name) {
    $sql = "CREATE TABLE `".$layers_table_name."` (
      `id` INT(6) UNSIGNED NOT NULL AUTO_INCREMENT,
      `name` VARCHAR(255) NOT NULL DEFAULT '',
      PRIMARY KEY (`id`));";
    $wpdb->query($sql);
    $sql = "SET SESSION sql_mode='NO_AUTO_VALUE_ON_ZERO'; INSERT INTO `".$layers_table_name."` VALUES (0, 'No layer'); SET SESSION sql_mode='';";
    $wpdb->query($sql);
  }
  if($wpdb->get_var("SHOW TABLES LIKE '".$catmaps_table_name."'") != $catmaps_table_name) {
    $sql = "CREATE TABLE `".$catmaps_table_name."` (
      `id` INT(6) UNSIGNED NOT NULL AUTO_INCREMENT,
      `text` TEXT NULL,
      PRIMARY KEY (`id`));";
    $wpdb->query($sql);
  }
}

register_activation_hook(WP_PLUGIN_DIR.'/leaflet/leaflet.php', 'activate');

$pLeaflet = new Leaflet();
unset($pLeaflet);

?>
