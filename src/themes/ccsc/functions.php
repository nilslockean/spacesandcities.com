<?php

// Defines
define( 'FL_CHILD_THEME_DIR', get_stylesheet_directory() );
define( 'FL_CHILD_THEME_URL', get_stylesheet_directory_uri() );

// Classes
require_once 'classes/class-fl-child-theme.php';

// Actions
add_action( 'wp_enqueue_scripts', 'FLChildTheme::enqueue_scripts', 1000 );

function my_acf_google_map_api( $api ){
	$api['key'] = 'AIzaSyB0Lem3uy2KXWtcEwhlXPSUO9fsPn8PfCc';
	
	return $api;
}
add_filter('acf/fields/google_map/api', 'my_acf_google_map_api');

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

// Excerpt length
add_filter('excerpt_length', function($length) {
  return 20;
});

// CCSC Widgets
function ccsc_load_widgets() {
  register_widget( 'linked_urban_lab_widget' );
  register_widget( 'coordinator_widget' );
  register_widget( 'urban_lab_events_widget' );
}
add_action( 'widgets_init', 'ccsc_load_widgets' );

// Linked Urban Lab Widget
class linked_urban_lab_widget extends WP_Widget {
  function __construct() {
    parent::__construct(
      // Base ID
      'linked_urban_lab_widget', 

      // Widget name will appear in UI
      'Linked Urban Lab', 

      // Widget description
      array( 'description' => 'Linked Urban Lab details.' ) 
    );
  }

  // Creating widget front-end
  public function widget( $args, $instance ) {

    $this_lab_id = get_the_id(); 
    
    // before and after widget arguments are defined by themes
    echo $args['before_widget'];


    $linked_urban_lab = get_field('linked_urban_lab');
    if ( $linked_urban_lab ): ?>
    <h2><?php echo get_the_title($linked_urban_lab); ?></h2>
    <!-- <p><?php echo get_the_excerpt($linked_urban_lab); ?></p> -->
    <a href="<?php echo get_permalink($linked_urban_lab); ?>">More about this Urban Lab &rarr;</a>
    <?php endif;

    echo $args['after_widget'];
  }
       
  // Widget Backend 
  public function form( $instance ) { ?>
    <p>
      This widget will display the event's linked Urban Lab details (title, excerpt and link).
    </p>
    <p>
    You can easily link an event to an Urban Lab when editing the event.
    </p>
  <?php }
} // Class linked_urban_lab_widget ends here

// Urban Lab Coordinator Widget
class coordinator_widget extends WP_Widget {
  function __construct() {
    parent::__construct(
      // Base ID
      'coordinator_widget', 

      // Widget name will appear in UI
      'Coodinator', 

      // Widget description
      array( 'description' => 'Coodinator\'s name, email address and phone number.' ) 
    );
  }

  // Creating widget front-end
  public function widget( $args, $instance ) {

    $this_lab_id = get_the_id(); 
    
    // before and after widget arguments are defined by themes
    echo $args['before_widget'];

    $coordinator = get_field('coordinator');
    if ( $coordinator ) {
      echo '<h2>Coordinator</h2>';
      foreach( $coordinator as $field => $value ) {
        if ( !$value ) continue;
        echo '<div class="coordinator-details ' . $field . '">' . $value . '</div>';
      }
    }

    echo $args['after_widget'];
  }
       
  // Widget Backend 
  public function form( $instance ) { ?>
    <p>
      This widget will display the currently viewed Urban Lab or Event coodinator's name, email address and phone number.
    </p>
  <?php }
} // Class coordinator_widget ends here

// Urban Labs Event Widget
class urban_lab_events_widget extends WP_Widget {
  function __construct() {
    parent::__construct(
      // Base ID
      'urban_lab_events_widget', 

      // Widget name will appear in UI
      'Urban Lab Events', 

      // Widget description
      array( 'description' => 'Workshops and Traineeships linked to the currently viewed Urban Lab.' ) 
    );
  }

  // Creating widget front-end
  public function widget( $args, $instance ) {

    // before and after widget arguments are defined by themes
    echo $args['before_widget'];

    $this_lab_id = get_the_id(); 

    function get_vsel_query_args($event_cat_slug, $urban_lab_id) {
      // Only get upcoming events
      $today = strtotime('today'); 
      $vsel_meta_query = array( 
        'relation' => 'AND',
        array( 
          'key' => 'event-date', 
          'value' => $today, 
          'compare' => '>=' 
        ),
        array(
          'key' => 'linked_urban_lab',
          'value' => $urban_lab_id,
          'compare' => '='
        ),
      );

      $vsel_tax_query = array(
        array(
          'taxonomy' => 'event_cat',
          'field'    => 'slug',
          'terms'    => $event_cat_slug,
        )
      );

      // linked_urban_lab
      // args
      $query_args = array(
        'post_type' => 'event', 
        'post_status' => 'publish', 
        'ignore_sticky_posts' => true, 
        'meta_key' => 'event-date', 
        'orderby' => 'meta_value_num', 
        'order' => 'asc',
        'paged' => false, 
        'meta_query' => $vsel_meta_query,
        'tax_query' => $vsel_tax_query
      );

      return $query_args;
    }
    
    $traineeships_query_args = get_vsel_query_args('traineeships', $this_lab_id);
    $traineeships_query = new WP_Query( $traineeships_query_args );
    if( $traineeships_query->have_posts() ): ?>
    <h2>Traineeships</h2>
    <div id="vsel" class="vsel-container">
    <?php while( $traineeships_query->have_posts() ) : $traineeships_query->the_post(); ?>
      <?php
        $event_date = get_post_meta( get_the_ID(), 'event-date', true ); 
      ?>
      <div class="vsel-content traineeships vsel-upcoming">
        <div class="vsel-meta">
          <h3 class="vsel-meta-title">
            <a href="<?php the_permalink(); ?>">
              <?php the_title(); ?>
            </a>
          </h3>

          <p class="vsel-meta-date vsel-meta-combined-date"><?php
            $sep = ' - ';
            $date_format_custom = get_option('vsel-setting-38');
              
            // set date format
            if ( !empty($date_format_custom) ) {
              $date_format = $date_format_custom;
            } else {
              $date_format = get_option('date_format');
            }
            $widget_start_date = get_post_meta( get_the_ID(), 'event-start-date', true );
            $widget_end_date = get_post_meta( get_the_ID(), 'event-date', true );
            $widget_start_label = get_option('vsel-setting-23');
            $widget_end_label = get_option('vsel-setting-24');


            $output = sprintf(esc_attr($widget_start_label), '<span>'.date_i18n( esc_attr($date_format), esc_attr($widget_start_date) ).'</span>' );
            $output .= $sep;
            $output .= sprintf(esc_attr($widget_end_label), '<span>'.date_i18n( esc_attr($date_format), esc_attr($widget_end_date) ).'</span>' );
            echo $output;
          ?></p>
        </div>
      </div>
    <?php endwhile; ?>
    </div>
    <?php endif;
    wp_reset_query();

    $workshops_query_args = get_vsel_query_args('workshops', $this_lab_id);
    $workshops_query = new WP_Query( $workshops_query_args );
    if( $workshops_query->have_posts() ): ?>
    <h2>Workshops</h2>
    <div id="vsel" class="vsel-container">
    <?php while( $workshops_query->have_posts() ) : $workshops_query->the_post(); ?>
      <?php
        $event_date = get_post_meta( get_the_ID(), 'event-date', true ); 
      ?>
      <div class="vsel-content workshops vsel-upcoming">
        <div class="vsel-meta">
          <h3 class="vsel-meta-title">
            <a href="<?php the_permalink(); ?>">
              <?php the_title(); ?>
            </a>
          </h3>

          <p class="vsel-meta-date vsel-meta-combined-date"><?php
            $sep = ' - ';
            $date_format_custom = get_option('vsel-setting-38');
              
            // set date format
            if ( !empty($date_format_custom) ) {
              $date_format = $date_format_custom;
            } else {
              $date_format = get_option('date_format');
            }
            $widget_start_date = get_post_meta( get_the_ID(), 'event-start-date', true );
            $widget_end_date = get_post_meta( get_the_ID(), 'event-date', true );
            $widget_start_label = get_option('vsel-setting-23');
            $widget_end_label = get_option('vsel-setting-24');


            $output = sprintf(esc_attr($widget_start_label), '<span>'.date_i18n( esc_attr($date_format), esc_attr($widget_start_date) ).'</span>' );
            $output .= $sep;
            $output .= sprintf(esc_attr($widget_end_label), '<span>'.date_i18n( esc_attr($date_format), esc_attr($widget_end_date) ).'</span>' );
            echo $output;
          ?></p>
        </div>
      </div>
    <?php endwhile; ?>
    </div>
    <?php endif;
    wp_reset_query();

    echo $args['after_widget'];
  }
       
  // Widget Backend 
  public function form( $instance ) { ?>
    <p>
      This widget will display Workshops and Traineeships linked to the currently viewed Urban Lab.
    </p>
    <p>
      You can easily link an event to an Urban Lab when editing the event.
    </p>
  <?php }
} // Class urban_lab_events_widget ends here

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