<?php
/*********************
Scripts and enqueue
*********************/
function scripts_and_styles() {

	wp_register_script(
		'category_get_feed', 
		get_stylesheet_directory_uri() . '/js/category_reload.js',
		array( 'jquery' ),
		'1.0',
		true
	);
	
	wp_enqueue_script('category_get_feed');

	wp_localize_script( 'category_get_feed', 'category_news_vars', array(
		'category_news_nonce' => wp_create_nonce( 'category_news_nonce' ),
		'category_news_ajax_url' => admin_url( 'admin-ajax.php' ),
		)
	);
}

add_action( 'wp_enqueue_scripts', 'scripts_and_styles', 999 );

/*********************
custom handler for AJAX request to grab category posts
**********************/
function category_news_filter( $cat ) {
	/** verify the nonce first */
	if( !wp_verify_nonce( $_POST['hp_news_nonce'], 'hp_news_nonce' ) ) die('This action is not allowed');

	$cat = $_POST['cat'];

	/** change this to fit your relevant query */
	$args = array(
	'cat'	=> $cat,
	'post_type'	=> 'post', 
	'posts_per_page'	=> 4,
	);

	$query = new WP_Query( $args );

	if ( $query->have_posts() ) : while ( $query->have_posts() ) : $query->the_post(); ?>

		<div class="post-entry">
			<h3><a href="<?php the_permalink(); ?>"><?php print get_the_title();?></a></h3>
			<?php echo wp_trim_words( get_the_content(), 40, '...' ); ?>

			<div class="post-meta">
			  
				<?php $categories = get_the_category();
					if ( ! empty( $categories ) ) :
				?>
					<span class="category-name">
						<?php print esc_html( $categories[0]->name ); ?>
					 </span> 
				<?php endif ;?>
				
				<?php print get_the_date('F j, Y');?>
			
			</div><!-- end post-meta-->
		</div><!-- end post-entry-->
	<?php endwhile; ?>

	<!-- if no posts found -->
	<?php else: ?>
		<h2>No posts found</h2>
	<?php endif;
	die(); 
 
}

add_action('wp_ajax_hp_news_filter', 'category_news_filter');
/** for nonauthenticated user */
add_action('wp_ajax_nopriv_hp_news_filter', 'category_news_filter');

/*********************
Function to make a dropdown menu of WP categories 
to be used in page template
**********************/
function category_filter(){
?>

  <form method="post">
		<select name="select" id="category-filter" class="cat-filter">
			<!-- default to 0 for all categories-->
			<option value='0'>All</option>
			<?php $cat_args=array(
					'type' => 'post',
					'orderby' => 'name',
					'order' => 'ASC'
				);
				$categories=get_categories($cat_args);
				foreach($categories as $category): 
					$cat_id = $category->term_id;
					$cat_name = $category->name;
			?>
				<option value='<?php print $cat_id;?>'><?php print $cat_name;?></option>
			<?php endforeach;
			wp_reset_postdata(); ?>
		</select>
	</form>
<?php }
