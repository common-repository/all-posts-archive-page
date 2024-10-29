<?php
/**
 * Template Name: Archive - Binge Read Page
 * Author: Narrow Bridge Media
 * Author URL: http://narrowbridgemedia.com/
 * Customized with code from http://www.joemaraparecio.com/customizing-genesis-archive-template-display-posts-month/
 */

//* Remove standard post content output
remove_action( 	'genesis_loop', 'genesis_do_loop'  );

// Add custom archive output
add_action( 'genesis_loop', 'nbm_page_archive_content'  );
function nbm_page_archive_content() {	
	
	$post_items = new WP_Query( array('post_type'=> 'post', 'posts_per_page' => '-1', 'order' => 'DESC', 'orderby' => 'date' ));
	$set_month = '';

	while( $post_items->have_posts()) : $post_items->the_post();
		if( $set_month == '' ){
			$set_month = get_the_date(F);
			echo '<div class="nbm_archive_page"><h2>'.get_the_date(F).' '.get_the_date(Y).'</h2><ul>';
		}else{
			if( $set_month != get_the_date(F) ){
				echo '</ul></div>';
				echo '<div class="nbm_archive_page"><h2>'.get_the_date(F).' '.get_the_date(Y).'</h2><ul>';
				$set_month = get_the_date(F);				
			}
		}			
		echo '<li><a href="'.get_the_permalink().'"><span class="archive_post_date">'.get_the_date('F j, Y').' - </span>'.get_the_title().'</a></li>';
		
	endwhile;	
}
genesis();