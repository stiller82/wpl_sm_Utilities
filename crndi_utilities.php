<?php
 /*
 Plugin Name: creandi utilities
 Description: Diverse Tools zum deaktivieren von Menüpunkten
 Version: 1.0
 Author: Stefan Stiller
 Author URI: https://www.creandi.de/
*/

if ( !defined( 'WPINC' ) ) { die; }
error_reporting(0);

/**
 * @param int $post_id POST ID
 */

function init_crndi_utilities()
{
   global $wpdb;
   $table = $wpdb->prefix . "crndi_utilities";
 
   $sql = "CREATE TABLE IF NOT EXISTS " .$table."(
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`name` varchar(255) NOT NULL,
	`beschreibung` varchar(255) NOT NULL,
	`befehl` varchar(255) NOT NULL,
	`bereich` varchar(255) NOT NULL,
	`status` boolean NOT NULL, PRIMARY KEY (id)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	dbDelta($sql);

	$wpdb->get_results("SELECT *  FROM ".$table);
	
	if(!$wpdb->num_rows) {
		$data = array(array("name" => "Gutenberg", "beschreibung" => "Gutenberg Editor deaktivieren", "status" => 1, "befehl" => "", "bereich" => "Allgemein"),
				array("name" => "UpdateMail", "beschreibung" => "Update Mails deaktivieren", "status" => 1, "befehl" => "", "bereich" => "Allgemein"),
				array("name" => "Beiträge", "beschreibung" => "Beiträge anzeigen", "status" => 1, "befehl" => "edit.php", "bereich" => "Menü"),
				array("name" => "Upload", "beschreibung" => "Medien anzeigen", "status" => 1, "befehl" => "upload.php", "bereich" => "Menü"),
				array("name" => "Edit", "beschreibung" => "Seiten anzeigen", "status" => 1, "befehl" => "edit.php?post_type=page", "bereich" => "Menü"),
				array("name" => "Themes", "beschreibung" => "Themes anzeigen", "status" => 1, "befehl" => "themes.php", "bereich" => "Menü"),
				array("name" => "Plugins", "beschreibung" => "Plugins anzeigen", "status" => 1, "befehl" => "plugins.php", "bereich" => "Menü"),
				array("name" => "User", "beschreibung" => "Benutzer anzeigen", "status" => 1, "befehl" => "users.php", "bereich" => "Menü"),
				array("name" => "Tools", "beschreibung" => "Werkzeuge anzeigen", "status" => 1, "befehl" => "tools.php", "bereich" => "Menü"),
				array("name" => "Kommentare", "beschreibung" => "Kommentare anzeigen", "status" => 1, "befehl" => "edit-comments.php", "bereich" => "Menü"),
				array("name" => "Options", "beschreibung" => "Einstellungen anzeigen", "status" => 1, "befehl" => "options-general.php", "bereich" => "Menü"),
				array("name" => "Logo", "beschreibung" => "Wordpress Logo anzeigen", "status" => 1, "befehl" => "wp-logo", "bereich" => "Top"),
				array("name" => "TopNeu", "beschreibung" => "Neu erstellen im Top Menü anzeigen", "status" => 1, "befehl" => "new-content", "bereich" => "Top"),
				array("name" => "TopKommentare", "beschreibung" => "Kommentare im Top Menü anzeigen", "status" => 1, "befehl" => "comments", "bereich" => "Top"),
				array("name" => "TopName", "beschreibung" => "Seiten Namen im Tom Menü anzeigen", "status" => 1, "befehl" => "site-name", "bereich" => "Top"));
				
		foreach($data as $row){
			$wpdb->insert($table, $row);
		}
	}
}
register_activation_hook(__FILE__, 'init_crndi_utilities');

function crndi_utilities_Bknd() {
	add_menu_page(
		'Utilities',
		'Utilities',
		'manage_options',
		'crndi_utilities_plugin',
		'crndi_utilities_interface',
		'dashicons-admin-tools',
		'2'
	);
}
add_action( 'admin_menu', 'crndi_utilities_Bknd' );

function crndi_utilities_interface(){
	include 'php/bknd_settings.php';
}

function crndi_utilities_allgemein() {
	global $wpdb;
	$table = $wpdb->prefix . "crndi_utilities";
	$functions = $wpdb->get_results("SELECT *  FROM ".$table." WHERE bereich = 'Allgemein'");

	foreach($functions as $funcion) {
		if($funcion->name == "UpdateMail" && $funcion->status == 0 ) {
			add_filter( 'auto_plugin_update_send_email', '__return_false' );
			add_filter( 'auto_theme_update_send_email', '__return_false' );
		}

		if($funcion->name == "Gutenberg" && $funcion->status == 0 ) {
			add_filter('use_block_editor_for_post', '__return_false');
			add_filter('use_block_editor_for_post_type', '__return_false');
		}
	}
}
add_action('init', 'crndi_utilities_allgemein');

function crndi_utilities_menu() {
	global $wpdb;
	$table = $wpdb->prefix . "crndi_utilities";
	$functions = $wpdb->get_results("SELECT *  FROM ".$table." WHERE bereich = 'Menü'");

	foreach($functions as $funcion) {
		if($funcion->status == 0) { 
			remove_menu_page($funcion->befehl); 
		}
	}
}
add_action('admin_init', 'crndi_utilities_menu');

function crndi_utilities_top()
{
    global $wp_admin_bar;
	global $wpdb;
	$table = $wpdb->prefix . "crndi_utilities";
	$functions = $wpdb->get_results("SELECT *  FROM ".$table." WHERE bereich = 'Top'");

	foreach($functions as $funcion) {
		if($funcion->status == 0) { 
			$wp_admin_bar->remove_node( $funcion->befehl );
		}
	}
}
add_action( 'admin_bar_menu', 'crndi_utilities_top', 999 );
?>