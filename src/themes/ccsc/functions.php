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

// Register and load the widget
function wpb_load_widget() {
  register_widget( 'wpb_widget' );
}
add_action( 'widgets_init', 'wpb_load_widget' );

// Creating the widget 
class wpb_widget extends WP_Widget {

function __construct() {
parent::__construct(

// Base ID of your widget
'wpb_widget', 

// Widget name will appear in UI
__('WPBeginner Widget', 'wpb_widget_domain'), 

// Widget description
array( 'description' => __( 'Sample widget based on WPBeginner Tutorial', 'wpb_widget_domain' ), ) 
);
}

// Creating widget front-end

public function widget( $args, $instance ) {
$title = apply_filters( 'widget_title', $instance['title'] );

// before and after widget arguments are defined by themes
echo $args['before_widget'];
if ( ! empty( $title ) )
echo $args['before_title'] . $title . $args['after_title'];

// This is where you run the code and display the output
echo __( 'Hello, World!', 'wpb_widget_domain' );
echo $args['after_widget'];
}
       
// Widget Backend 
public function form( $instance ) {
if ( isset( $instance[ 'title' ] ) ) {
$title = $instance[ 'title' ];
}
else {
$title = __( 'New title', 'wpb_widget_domain' );
}
// Widget admin form
?>
<p>
<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
</p>
<?php 
}
   
// Updating widget replacing old instances with new
public function update( $new_instance, $old_instance ) {
$instance = array();
$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
return $instance;
}
} // Class wpb_widget ends here

function ccsc_sidebar($section) {
  $parent_sidebar = 'blog';
  $position = 'right';
  $size    = FLTheme::get_setting( 'fl-' . $parent_sidebar . '-sidebar-size' );
  $display = FLTheme::get_setting( 'fl-' . $parent_sidebar . '-sidebar-display' );
  $layout  = FLTheme::get_setting( 'fl-' . $parent_sidebar . '-layout' );

  if ( strstr( $layout, $position ) && FLTheme::is_sidebar_enabled( $parent_sidebar ) ) {
    include locate_template( 'sidebar.php' );
  }
}