<?php

// Defines
define( 'FL_CHILD_THEME_DIR', get_stylesheet_directory() );
define( 'FL_CHILD_THEME_URL', get_stylesheet_directory_uri() );

// Classes
require_once 'classes/class-fl-child-theme.php';

// Actions
add_action( 'wp_enqueue_scripts', 'FLChildTheme::enqueue_scripts', 1000 );

// CCSC Sidebars
if ( function_exists('register_sidebar') ) {

  register_sidebar(array(
    'name' => 'Single Event',
    'id' => 'single-event-sidebar',
    'before_widget' => '<div id="%1$s" class="widget %2$s">',
    'after_widget' => '</div>',
    'before_title' => '<h2>',
    'after_title' => '</h2>'
   ));

   register_sidebar(array(
    'name' => 'Single Urban Lab',
    'id' => 'single-urban-lab-sidebar',
    'before_widget' => '<div id="%1$s" class="widget %2$s">',
    'after_widget' => '</div>',
    'before_title' => '<h2>',
    'after_title' => '</h2>'
   ));
}

// Urban Labs Post Type
function ccsc_urban_labs_post_type() {
	// Register the post type
	register_post_type( 'urban-lab',
		array(
			'labels' => array(
				'name' => 'Urban Labs',
        'singular_name' => 'Urban Lab',
        'all_items' => 'All Urban Labs',
        'add_new_item' => 'Add New Urban Lab'
			),
			'public' => true,
			'supports' => array ( 'title', 'editor', 'custom-fields', 'excerpt', 'thumbnail' ),
			'menu_icon' => 'dashicons-image-filter',
			'rewrite' => array( 'slug' => __( 'urban-labs' ))
		)
  );
  
  // Add custom taxonomies
  register_taxonomy(
		'leader',
		'urban-lab',
		array(
      'label' => 'Leaders',
      'labels' => array(
        'name' => 'Leaders',
        'singular_name' => 'Leader',
        'all_items' => 'All Leaders',
        'add_new_item' => 'Add New Leader',
        'not_found' => 'No leaders found.'
      ),
      'hierarchical' => true,
      'public' => false,
      'show_ui' => true
		)
	);
}
add_action( 'init', 'ccsc_urban_labs_post_type' );