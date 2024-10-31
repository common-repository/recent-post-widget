<?php
/*
Plugin Name: Recent Post Widget
Description:This is plugin for creating a widget for recent post. In it user can select number of posts to be displayed in front end.It shows post's featured image along with post title.
Author: Infoseek Team
Version: 1.0
Author URI: http://infoseeksoftwaresystems.com/
License: GPL2

*/

add_action( 'wp_enqueue_scripts', 'my5tech_stylesheet' );
add_action( 'admin_enqueue_scripts', 'my5tech_stylesheet' );
/**
 * Enqueue plugin style-file
 */
function my5tech_stylesheet() {
    wp_register_style( 'prefix-style', plugins_url('css/style.css', __FILE__) );
    wp_enqueue_style( 'prefix-style' );
}


// Register and load the widget
function my5tech_load_widget() {
    register_widget( 'wpb_widget' );
}
add_action( 'widgets_init', 'my5tech_load_widget' );
 
// Creating the widget 
class wpb_widget extends WP_Widget {
 
function __construct() {
parent::__construct(
 
// Base ID of your widget
'wpb_widget', 
 
// Widget name will appear in UI
__('Recent-Post Widget', 'wpb_widget_domain'), 
 
// Widget description
array( 'description' => __( 'Recent-Post Display', 'wpb_widget_domain' ), ) 
);
}
 
// Creating widget front-end
 
public function widget( $args, $instance ) {
	$title  = apply_filters( 'widget_title', $instance['title'] );
	$num    = apply_filters( 'widget_post', $instance['abbr-no'] );
	$ptdt   = apply_filters( 'widget_post_date', $instance['p-date'] );
	$ptathr = apply_filters( 'widget_post_author', $instance['authr'] );
	$ptcat  = apply_filters( 'widget_post_author',  $instance['p-cate'] );
	$pttype = "'".implode("','", $instance['post_type'])."'";
	$porder = $instance['order'];
	$custmcss = $instance['css'];
	
	// before and after widget arguments are defined by themes
	echo $args['before_widget'];
	if ( ! empty( $title ) )

	echo $args['before_title'] . $title . $args['after_title'];

	// This is where you run the code and display the output

	// the query
	$args1 = array(
        'post_type'=> array($pttype),
		'post_status'=>'publish',
        'posts_per_page'=> $num,
		'order' => $porder
		);
    $the_query = new WP_Query( $args1 );

	 
	 if ( $the_query->have_posts() ) : 
?>
	 
	<ul class="my_pst">
	 
		<!-- the loop -->
		<?php while ( $the_query->have_posts() ) : $the_query->the_post(); ?>
			<li class="ft-img">
				<a href="<?php the_permalink(); ?>">
					<?php if(has_post_thumbnail()){ the_post_thumbnail();}
					else{?>
					<img src="<?php echo plugins_url('img/default-thumbnail.jpg', __FILE__);?>" alt="image">	
					<?php }?>
					<span class="p-title"><?php the_title(); ?></span>
					
				</a>
				<?php if($ptdt==1){?>
				<span class="dt">
					<span class="dth">Date : </span><?php echo get_the_date( 'd-m-Y' );
					?>
				</span> 
				<?php } ?>
				
				<?php if($ptathr==1){?>
				<span class="dt">
					<span class="ath">Author : </span><?php echo get_the_author();
					?>
				</span> 
				<?php } ?>
				
				<?php if($ptcat ==1){?>
				<span class="dt-cat">
					<span class="ath">Category: </span><?php echo the_category();?>
					<?php 
					if($pttype == "'product'"){
						$terms = get_the_terms( $post->ID, 'product_cat' );
						foreach ( $terms as $term ){
							$category_name = $term->name;?>
							<ul>
							<li>
							<a href="<?php the_permalink();?>"><?php echo $category_name; ?></a>
							</li>
							
					<?php }?>
					</ul>
					<?php }
					?>
				</span> 
				<?php } ?>
			</li>
		<?php endwhile; ?>
		<!-- end of the loop -->
	 
	</ul>

	 
	<?php wp_reset_postdata(); ?>
	 <style>
	 <?php echo $custmcss; ?>
	 </style>
	<?php else : ?>
		<p><?php _e( 'Sorry, no posts matched your criteria.' ); ?></p>
	<?php endif; 

	echo $args['after_widget'];
	}	 
	// Widget Backend 
	public function form( $instance ) {
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
			$num   = $instance[ 'abbr-no' ];
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
		<p>
			<label for="<?php echo $this->get_field_id( 'abbr-no' ); ?>"><?php _e( 'Number of Posts to Show:' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'abbr-no' ); ?>"  name="<?php echo $this->get_field_name( 'abbr-no' ); ?>" type="number" min ="1" value="<?php echo esc_attr( $num ); ?>"/>
		</p>
		<h4>Advance Setting:</h4>
		<p>
			<label for="<?php echo $this->get_field_id( 'p-date' ); ?>"><?php _e( 'Show Date:' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'p-date' ); ?>" name="<?php echo $this->get_field_name( 'p-date' ); ?>" type="checkbox"  <?php checked( $instance['p-date'], 1 ); ?>/>
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id( 'authr' ); ?>"><?php _e( 'Show Author:' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'authr' ); ?>" name="<?php echo $this->get_field_name( 'authr' ); ?>" type="checkbox"  <?php checked( $instance['authr'], 1 ); ?>/>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'p-cate' ); ?>"><?php _e( 'Show Category:' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'p-cate' ); ?>" name="<?php echo $this->get_field_name( 'p-cate' ); ?>" type="checkbox"  <?php checked( $instance['p-cate'], 1 ); ?>/>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'order' ); ?>">
				<?php _e( 'Order', 'my5-recent-post' ); ?>
			</label>
			<select class="widefat" id="<?php echo $this->get_field_id( 'order' ); ?>" name="<?php echo $this->get_field_name( 'order' ); ?>" style="width:100%;">
				<option value="DESC" <?php selected( $instance['order'], 'DESC' ); ?>><?php _e( 'Descending', 'rpwe' ) ?></option>
				<option value="ASC" <?php selected( $instance['order'], 'ASC' ); ?>><?php _e( 'Ascending', 'rpwe' ) ?></option>
			</select>
		</p>
		
		<div class="check-form">
			<label>
				<?php _e( 'Post Types :', 'my5-recent-post' ); ?>
			</label>
			<hr>
			<ul class="allposts">
				<?php foreach ( get_post_types( array( 'public' => true ), 'objects' ) as $type ) : ?>
					<li>
						<input type="radio" value="<?php echo esc_attr( $type->name ); ?>" id="<?php echo $this->get_field_id( 'post_type' ) . '-' . $type->name; ?>" name="<?php echo $this->get_field_name( 'post_type' ); ?>[]" <?php checked( is_array( $instance['post_type'] ) && in_array( $type->name, $instance['post_type'] ) ); ?> />
						<label for="<?php echo $this->get_field_id( 'post_type' ) . '-' . $type->name; ?>">
							<?php echo esc_html( $type->labels->name ); ?>
						</label>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
		<p>
			<label for="<?php echo $this->get_field_id( 'css' ); ?>">
				<?php _e( 'Custom CSS:', 'my5-recent-post' ); ?>
			</label>
			<textarea class="widefat" id="<?php echo $this->get_field_id( 'css' ); ?>" name="<?php echo $this->get_field_name( 'css' ); ?>" style="height:150px;"><?php echo $instance['css']; ?></textarea>
		</p>
		<?php 
		
		
	}

		 
	// Updating widget replacing old instances with new
	public function update( $new_instance, $old_instance ) {
	
	
		$name  = get_post_types( array( 'public' => true ), 'names' );
		$types = array();
		foreach( $new_instance['post_type'] as $type ) {
			if ( in_array( $type, $name ) ) {
				$types[] = $type;
			}
		}
		if ( empty( $types ) ) {
			$types[] = 'post';
		}
		
		
		$instance = array();
		$instance['title']   = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['abbr-no'] = ( ! empty( $new_instance['abbr-no'] ) ) ? strip_tags( $new_instance['abbr-no'] ) : '';
		$instance['p-date']  = isset( $new_instance['p-date'] ) ? (bool) $new_instance['p-date'] : 0;
		$instance['authr']   = isset( $new_instance['authr'] ) ? (bool) $new_instance['authr'] : 0;
		$instance['p-cate']  = isset( $new_instance['p-cate'] ) ? (bool) $new_instance['p-cate'] : 0;
		$instance['post_type'] = $types;
		$instance['order']   = stripslashes( $new_instance['order'] );
		$instance['css']     = $new_instance['css'];
		return $instance;
	}
} 
?>