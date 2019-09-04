<?php get_header(); ?>

<div class="container">
	<div class="row">

		<?php FLTheme::sidebar( 'left' ); ?>

		<div class="fl-content <?php FLTheme::content_class(); ?>">
			<?php
			if ( have_posts() ) :
				while ( have_posts() ) :
					the_post();
					get_template_part( 'content', 'single' );
				endwhile;
			endif;
			?>
		</div>

		<!-- <?php FLTheme::sidebar( 'right' ); ?> -->
    <?php
      $parent_sidebar = 'blog';
      $section = 'single-event';
      $position = 'right';
      $size    = FLTheme::get_setting( 'fl-' . $parent_sidebar . '-sidebar-size' );
      $display = FLTheme::get_setting( 'fl-' . $parent_sidebar . '-sidebar-display' );
      $layout  = FLTheme::get_setting( 'fl-' . $parent_sidebar . '-layout' );

      if ( strstr( $layout, $position ) && FLTheme::is_sidebar_enabled( $parent_sidebar ) ) {
        include locate_template( 'sidebar.php' );
      }
    ?>

	</div>
</div>

<?php get_footer(); ?>
