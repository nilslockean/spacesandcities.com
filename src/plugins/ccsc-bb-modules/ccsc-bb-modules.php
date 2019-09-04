<?php
/**
 * Plugin Name: CCSC Beaver Builder Modules
 * Description: Custom Beaver Builder modules for the CCSC website.
 * Version: 1.0
 * Author: Nils Lockean
 * Author URI: https://nilslockean.se
 */

define( 'CCSC_MODULES_DIR', plugin_dir_path( __FILE__ ) );
define( 'CCSC_MODULES_URL', plugins_url( '/', __FILE__ ) );

function my_load_module_examples() {
    if ( class_exists( 'FLBuilder' ) ) {
      require_once 'ccsc-publication/ccsc-publication.php';
    }
}
add_action( 'init', 'my_load_module_examples' );

?>