<?php
/*
Plugin Name: Ultimate Sticky Posts Widget
Description: Adds a widget that shows your posts the way you want.
Author: Pieter Ferreira
Version: 1.2.2
License: GPLv2
*/

add_action( 'wp_enqueue_scripts', 'sticky_posts_widget_styles' );
function sticky_posts_widget_styles() {
	wp_register_style( 'sticky-posts', plugins_url( 'sticky-posts/sticky.css' ) );
	wp_enqueue_style( 'sticky-posts' );
}

class bsp_widget extends WP_Widget {

function __construct() {
parent::__construct(
// Base ID of the widget
'bsp_widget', 

// Widget name that appears in UI
__('Ulitmate Sticky Posts Widget', 'bsp_widget_domain'), 

// Widget description
array( 'description' => __( 'Display your posts the way you want to!, sticky or not.', 'bsp_widget_domain' ), ) 
);
}

// Creating widget front-end
public function widget( $args, $instance ) {


echo $args['before_widget'];

if ( ! empty( $title ) )
echo $args['before_title'] . $title . $args['after_title'];
 	$title = apply_filters('widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);
	$cssid = $instance['cssid'];
	$cssclass = $instance['cssclass'];
    $sticky = $instance['sticky'];
    $order = $instance['order'];
    $orderby = $instance['orderby'];
    echo $before_widget;

      // Ultimate Sticky posts Query
      
      if ($sticky == 'only') {
        $sticky_query = $args = array( 
        'posts_per_page' => $instance['num'], 
        'post__in' => get_option( 'sticky_posts' ),
        'orderby' => $instance['orderby'],
		'order' => $instance['order'],
        'ignore_sticky_posts' => 1  
         ); 
      } elseif ($sticky == 'hide') {
      $sticky_query = $args = array( 
        'posts_per_page' => $instance['num'], 
        'post__not_in' => get_option( 'sticky_posts' ),
        'orderby' => $instance['orderby'],
		'order' => $instance['order']
         );
      } 
      else { 
        $sticky_query = $args = array( 
        'posts_per_page' => $instance['num'],
        'order' => $order,
        'orderby' => $orderby,
        'ignore_sticky_posts' => 1
        
      );
      } 
      
// This is where you run the code and display the output
   			
			$query = new WP_Query( $args );
?>
			<div id="<?php echo $instance["cssid"] ?>" class="<?php echo $instance["cssclass"]  ?>">
			
			<?php
			if ( $title ) {
       		echo $before_title;
        		echo "<h3>" . $title . "</h3>";
        	echo $after_title;
      		}
   				$featured = new WP_Query($args); 
				if ($featured->have_posts()): while($featured->have_posts()): $featured->the_post(); ?>

				<div class="bsp_container">
				<?php if (current_theme_supports('post-thumbnails') && $instance['show_thumbnail'] && has_post_thumbnail()) : ?>
					<div class="bsp_image">
						<a href="<?php the_permalink(); ?>"><?php the_post_thumbnail($instance['thumb_size']); ?>
						<div class="bsp_overlay"></div>
						</a>
					</div>
				<?php endif; ?>
				<?php if ( isset( $instance['show_title'] ) ) : ?>
					<div class="bsp_title"><h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3></div>
				<?php endif; ?>
				
				<?php if ( isset( $instance['show_excerpt'] ) ) : ?>
					<div class="bsp_excerpt"><?php the_excerpt(); ?></div>
				<?php endif; ?>
				
				<?php if ( isset( $instance['category'] ) ) : ?>
					<div class="bsp_category"><?php the_category(', '); ?></div>
				<?php endif; ?>
				
				</div>
				<?php
				endwhile; else:
			endif;
			?>
			</div>
			<?php
echo $args['after_widget'];
}
		
// Widget Backend 
public function form( $instance ) {

$instance = wp_parse_args( (array) $instance, array(
        'title' => __('Sticky Posts', 'bsp'),
        'cssid' => 'your-ID-class',
        'cssclass' => 'your-CLASS',
        'num' => '5',
        'order' => 'DESC',
        'orderby' => 'date',
        'show_title' => true
        
      ) );
$title = $instance[ 'title' ];
$show_title = $instance[ 'title' ];
$show_excerpt = $instance[ 'show_excerpt' ];

$category = $instance[ 'category' ];
$num = $instance[ 'num' ];
$cssid = $instance[ 'cssid' ];
$cssclass = $instance[ 'cssclass' ];
$sticky = $instance['sticky'];
$order = $instance['order'];
$orderby = $instance['orderby'];
$thumb_size = $instance['thumb_size'];
$show_thumbnail = $instance['show_thumbnail'];



// Widget admin form
?>
<p>
<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
</p>

<p>
<label for="<?php echo $this->get_field_id( 'category' ); ?>"><?php _e( 'Show Titles:' ); ?></label> 
<input type="checkbox" class="show_title" id="<?php echo $this->get_field_id("show_title"); ?>" name="<?php echo $this->get_field_name("show_title"); ?>"<?php checked( (bool) $instance["show_title"], true ); ?> />
</p>

<p>
<label for="<?php echo $this->get_field_id( 'show_excerpt' ); ?>"><?php _e( 'Show Excerpt:' ); ?></label> 
<input type="checkbox" class="show_excerpt" id="<?php echo $this->get_field_id("show_excerpt"); ?>" name="<?php echo $this->get_field_name("show_excerpt"); ?>"<?php checked( (bool) $instance["show_excerpt"], true ); ?> />
</p>

<p>
<label for="<?php echo $this->get_field_id( 'category' ); ?>"><?php _e( 'Show Category:' ); ?></label> 
<input type="checkbox" class="category" id="<?php echo $this->get_field_id("category"); ?>" name="<?php echo $this->get_field_name("category"); ?>"<?php checked( (bool) $instance["category"], true ); ?> />
</p>

<p>
<label for="<?php echo $this->get_field_id( 'num' ); ?>"><?php _e( 'Number of posts to show:' ); ?></label> 
<input class="num" id="<?php echo $this->get_field_id( 'num' ); ?>" name="<?php echo $this->get_field_name( 'num' ); ?>" type="text" value="<?php echo esc_attr( $num ); ?>" />
</p>

<p>
<label for="<?php echo $this->get_field_id( 'cssid' ); ?>"><?php _e( 'CSS ID:' ); ?></label> 
<input class="classid" id="<?php echo $this->get_field_id( 'cssid' ); ?>" name="<?php echo $this->get_field_name( 'cssid' ); ?>" type="text" value="<?php echo esc_attr( $cssid ); ?>" />
</p>

<p>
<label for="<?php echo $this->get_field_id( 'cssclass' ); ?>"><?php _e( 'CSS Class:' ); ?></label> 
<input class="classhere" id="<?php echo $this->get_field_id( 'cssclass' ); ?>" name="<?php echo $this->get_field_name( 'cssclass' ); ?>" type="text" value="<?php echo esc_attr( $cssclass ); ?>" />
</p>
<p>
          <label for="<?php echo $this->get_field_id('sticky'); ?>"><?php _e( 'Sticky posts', 'bsp' ); ?>:</label>
          <select name="<?php echo $this->get_field_name('sticky'); ?>" id="<?php echo $this->get_field_id('sticky'); ?>" class="widefat">
            <option value="show"<?php if( $sticky === 'show') echo ' selected'; ?>><?php _e('Show All Posts', 'bsp'); ?></option>
            <option value="hide"<?php if( $sticky == 'hide') echo ' selected'; ?>><?php _e('Hide Sticky Posts', 'bsp'); ?></option>
            <option value="only"<?php if( $sticky == 'only') echo ' selected'; ?>><?php _e('Show Only Sticky Posts', 'bsp'); ?></option>
          </select>
        </p>
        <div class="bsp-tab bsp-hide bsp-tab-order">

        <p>
          <label for="<?php echo $this->get_field_id('orderby'); ?>"><?php _e('Order by', 'bsp'); ?>:</label>
          <select name="<?php echo $this->get_field_name('orderby'); ?>" id="<?php echo $this->get_field_id('orderby'); ?>" class="widefat">
            <option value="date"<?php if( $orderby == 'date') echo ' selected'; ?>><?php _e('Published Date', 'bsp'); ?></option>
            <option value="title"<?php if( $orderby == 'title') echo ' selected'; ?>><?php _e('Title', 'bsp'); ?></option>
            <option value="comment_count"<?php if( $orderby == 'comment_count') echo ' selected'; ?>><?php _e('Comment Count', 'bsp'); ?></option>
            <option value="rand"<?php if( $orderby == 'rand') echo ' selected'; ?>><?php _e('Random'); ?></option>
          </select>
        </p>

        <p<?php if ($orderby !== 'meta_value') echo ' style="display:none;"'; ?>>
          <label for="<?php echo $this->get_field_id( 'meta_key' ); ?>"><?php _e('Custom field', 'bsp'); ?>:</label>
          <input class="widefat" id="<?php echo $this->get_field_id('meta_key'); ?>" name="<?php echo $this->get_field_name('meta_key'); ?>" type="text" value="<?php echo $meta_key; ?>" />
        </p>

        <p>
          <label for="<?php echo $this->get_field_id('order'); ?>"><?php _e('Order', 'bsp'); ?>:</label>
          <select name="<?php echo $this->get_field_name('order'); ?>" id="<?php echo $this->get_field_id('order'); ?>" class="widefat">
            <option value="DESC"<?php if( $order == 'DESC') echo ' selected'; ?>><?php _e('Descending', 'bsp'); ?></option>
            <option value="ASC"<?php if( $order == 'ASC') echo ' selected'; ?>><?php _e('Ascending', 'bsp'); ?></option>
          </select>
        </p>
        <?php if ( function_exists('the_post_thumbnail') && current_theme_supports( 'post-thumbnails' ) ) : ?>

          <?php $sizes = get_intermediate_image_sizes(); ?>

          <p>
            <input class="checkbox" id="<?php echo $this->get_field_id( 'show_thumbnail' ); ?>" name="<?php echo $this->get_field_name( 'show_thumbnail' ); ?>" type="checkbox" <?php checked( (bool) $show_thumbnail, true ); ?> />

            <label for="<?php echo $this->get_field_id( 'show_thumbnail' ); ?>"><?php _e( 'Show thumbnail', 'bsp' ); ?></label>
          </p>

          <p<?php if (!$show_thumbnail) echo ' style="display:none;"'; ?>>
            <select id="<?php echo $this->get_field_id('thumb_size'); ?>" name="<?php echo $this->get_field_name('thumb_size'); ?>" class="widefat">
              <?php foreach ($sizes as $size) : ?>
                <option value="<?php echo $size; ?>"<?php if ($thumb_size == $size) echo ' selected'; ?>><?php echo $size; ?></option>
              <?php endforeach; ?>
              <option value="full"<?php if ($thumb_size == $size) echo ' selected'; ?>><?php _e('full'); ?></option>
            </select>
          </p>

        <?php endif; ?>

      </div>
      <script>

          jQuery(document).ready(function($){

            var show_thumbnail = $("#<?php echo $this->get_field_id( 'show_thumbnail' ); ?>");
            var thumb_size_wrap = $("#<?php echo $this->get_field_id( 'thumb_size' ); ?>").parents('p');


            // Toggle excerpt length on click
            show_thumbnail.click(function(){
              thumb_size_wrap.toggle('fast');
            });

          });

        </script>
<?php 
}
	
// Updating widget replacing old instances with new
public function update( $new_instance, $old_instance ) {
$instance = array();
$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
$instance['show_title'] = $new_instance['show_title'];
$instance['show_excerpt'] = $new_instance['show_excerpt'];
$instance['category'] = $new_instance['category'];
$instance['num'] = ( ! empty( $new_instance['num'] ) ) ? strip_tags( $new_instance['num'] ) : '';
$instance['cssid'] = strip_tags( $new_instance['cssid']);
$instance['cssclass'] = strip_tags( $new_instance['cssclass']);
$instance['sticky'] = $new_instance['sticky'];
$instance['order'] = $new_instance['order'];
$instance['orderby'] = $new_instance['orderby'];
$instance['show_thumbnail'] = isset( $new_instance['show_thumbnail'] );
$instance['thumb_size'] = strip_tags( $new_instance['thumb_size'] );


return $instance;
}
} // Class bsp_widget ends here

// Register and load the widget
function bsp_load_widget() {
	register_widget( 'bsp_widget' );
}
add_action( 'widgets_init', 'bsp_load_widget' );

