<?php
	/*
	Plugin Name: SM Utilities
	Description: Plugin zum deaktivieren diverser Menüpunkte in Wordpress zur übersichtlicheren Gestaltung des Admin Bereiches
	Version: 2.0
	Author: Stefan Stiller | stiller media
	Author URI: https://www.stillermedia.de/
	*/

	if ( !defined( 'WPINC' ) ) { die; }
	error_reporting(0);
	
/******************************************************************************************************* */

	function sm_utilities_admFiles(){
		$version = filemtime( plugin_dir_url(__FILE__) . 'styles/style_bknd.css');
		wp_enqueue_style('my-style',  plugin_dir_url(__FILE__) . 'styles/style_bknd.css', array(), $version);
	}
	add_action( 'admin_enqueue_scripts', 'sm_utilities_admFiles' );

/******************************************************************************************************* */	

	function sm_utilities_install() {
		global $wpdb;
		$sm_utilities_table = $wpdb->prefix . "sm_utilities";

		$initDB_file = file_get_contents(plugin_dir_path(__FILE__) . 'data/init_db.json');
		$initDB = json_decode($initDB_file, true);

		$sql = "CREATE TABLE IF NOT EXISTS $sm_utilities_table (
			`id` int(11) NOT NULL AUTO_INCREMENT,
			`name` varchar(255) NOT NULL,
			`beschreibung` varchar(255) NOT NULL,
			`befehl` varchar(255) NOT NULL,
			`bereich` varchar(255) NOT NULL,
			`status` boolean NOT NULL, PRIMARY KEY (id)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);

		$wpdb->get_results("SELECT * FROM ".$wpdb->prefix."sm_utilities");
		
		if(!$wpdb->num_rows) {
			foreach($initDB as $row){ 
				$wpdb->insert($sm_utilities_table, $row); 
			}
		}
	}
	register_activation_hook(__FILE__, 'sm_utilities_install');

/******************************************************************************************************* */

	function sm_utilities_admMainMenu() {
		add_menu_page(
			'SM Utilities',
			'SM Utilities',
			'manage_options',
			'sm_utilities_plugin',
				function() { include 'php/bknd_settings.php'; },
			'dashicons-admin-tools',
			'99'
		);
	}
	add_action( 'admin_menu', 'sm_utilities_admMainMenu' );

/******************************************************************************************************* */

	function sm_utilities_initFunctions() {
		global $wpdb;

		$functions = $wpdb->get_results("SELECT *  FROM ".$wpdb->prefix."sm_utilities WHERE bereich = 'Allgemein'");

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
	add_action('init', 'sm_utilities_initFunctions');

/******************************************************************************************************* */

	function sm_utilities_viewMenuSide() {
		global $wpdb;

		$functions = $wpdb->get_results("SELECT *  FROM ".$wpdb->prefix."sm_utilities WHERE bereich = 'Menü'");

		foreach($functions as $funcion) {
			if($funcion->status == 0) { 
				remove_menu_page($funcion->befehl); 
			}
		}
	}
	add_action('admin_init', 'sm_utilities_viewMenuSide');

/******************************************************************************************************* */

	function sm_utilities_viewMenuBar() {
		global $wp_admin_bar;
		global $wpdb;

		$functions = $wpdb->get_results("SELECT *  FROM ".$wpdb->prefix."sm_utilities WHERE bereich = 'Top'");
		foreach($functions as $funcion) {
			if($funcion->status == 0) { 
				$wp_admin_bar->remove_node( $funcion->befehl );
			}
		}
	}
	add_action( 'admin_bar_menu', 'sm_utilities_viewMenuBar', 999 );
?>