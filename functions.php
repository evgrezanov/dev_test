<?php

function the_shop_info($post_id) {
	if ($post_id) {
		$adress = get_post_meta($post_id,'shop_adress',true);
		$phone = get_post_meta($post_id,'shop_phone',true);
		$website = get_post_meta($post_id,'shop_website',true);
		$email = get_post_meta($post_id,'shop_email',true);
		
		echo '<strong>Adress: </strong>'.$adress.'<br>';
		echo '<strong>Phone: </strong>'.$phone.'<br>';
		echo '<strong>Website: </strong>'.$website.'<br>';
		echo '<strong>Email: </strong>'.$email.'<br>';
	}
}

add_shortcode( 'list-posts-basic', 'rmcc_post_listing_shortcode1' );
function rmcc_post_listing_shortcode1( $atts ) {
    ob_start();
        // get all category
		$cats = get_categories();
        foreach ($cats as $cat) {
            if ( $cat->parent == '0') {
            	// show parent category name
				echo "<h1>".$cat->cat_name."</h1>";
	            // get child category
				$cats_child = get_categories('parent='.$cat->cat_ID);
				// check cild_category
				if ($cats_child) {
					foreach ($cats_child as $cat_child) {
						echo "<h3>".$cat_child->cat_name."</h3>";
						echo "<ul class='uldotted'>";
						$posts_t = get_posts('posts_per_page=-1&cat='.$cat_child->cat_ID);
						foreach ($posts_t as $post_t) {
							setup_postdata($post_t);
							the_post();
							// check post category
							$category = get_the_category($post_t->ID);
							if ($category[0]->cat_ID == $cat_child->cat_ID) {
								echo '<li><a href="'.get_permalink($post_t->ID).'">'.get_the_title($post_t->ID).'</a></li>';
								the_shop_info($post_t->ID);
							}
						}
						wp_reset_postdata();
						echo "</ul>";
					}
				}
				else {
					$posts_s = get_posts('posts_per_page=-1&cat='.$cat->cat_ID);
					echo "<ul class='uldotted'>";
					foreach ($posts_s as $post_s) {
						setup_postdata($post_s);
						the_post();
						echo '<li><a href="'.get_permalink($post_s->ID).'">'.get_the_title($post_s->ID).'</a></li>';
						the_shop_info($post_s->ID);
					}
					wp_reset_postdata();
					echo "</ul>";
				}

            }
        }
    $myvariable = ob_get_clean();
    return $myvariable;
}


// AJAX Filter functions

add_action('wp_print_scripts','include_scripts'); 
function include_scripts(){
  // include js file
  wp_register_script('afp_script', get_stylesheet_directory_uri() . '/js/filter-posts.js', false, null, false);
  wp_enqueue_script('afp_script', get_stylesheet_directory_uri() . '/js/filter-posts.js', array('jquery') );


        wp_localize_script( 'jquery', 'ajaxdata', 
			array(
   				'url' => admin_url('admin-ajax.php'), 
   				'nonce' => wp_create_nonce('add_object')
			)
		);
}


add_shortcode ('city_select', 'city_select_box_shortcode');
function city_select_box_shortcode() {
	$cities_args = array(
					'hide_empty' 	=> true,
					'hierarchical'	=> 0,
					'order'	=> 'ASC',
					'taxonomy'	=> 'category',
	);

	$cities = get_terms('category', $cities_args);
?>
	<select id="selecttaxterm" name="selecttaxterm" >
		<option value='0'><?php _e('Choose your country'); ?></option>
<?php
	if ($cities) {
		foreach ($cities as $city) {
			if ( $city->parent == '0') {
				echo '<option class="'.$city->taxonomy.'" value="'.$city->term_id.'">'.$city->name.'</option>';
			}
		}
	}
	?>
	</select>
<?php

}


add_action( 'wp_ajax_nopriv_myajax-submit', 'myajax_submit');
add_action( 'wp_ajax_myajax-submit', 'myajax_submit');
function myajax_submit() {
	$id = $_POST['id'];
	$nonce = $_POST['nonce'];

	// check nonce code
	if ( !wp_verify_nonce( $nonce, 'add_object' ) ){ die ( 'Stop!');}

    ob_start();
        // get category
    	$cat = get_category($id);
        if ( $cat->parent == '0') {
        	// show category name
			echo "<h1>".$cat->cat_name."</h1>";
            // get child category
			$cats_child = get_categories('parent='.$cat->cat_ID);
			// check cild_category
			if ($cats_child) {
				foreach ($cats_child as $cat_child) {
					echo "<h3>".$cat_child->cat_name."</h3>";
					echo "<ul class='uldotted'>";
					$posts_t = get_posts('posts_per_page=-1&cat='.$cat_child->cat_ID);
					foreach ($posts_t as $post_t) {
						setup_postdata($post_t);
						the_post();
						// check post category
						$category = get_the_category($post_t->ID);
						if ($category[0]->cat_ID == $cat_child->cat_ID) {
							echo '<li><a href="'.get_permalink($post_t->ID).'">'.get_the_title($post_t->ID).'</a></li>';
							the_shop_info($post_t->ID);
						}
					}
					wp_reset_postdata();
					echo "</ul>";
				}
			}
			else {
				$posts_s = get_posts('posts_per_page=-1&cat='.$cat->cat_ID);
				echo "<ul class='uldotted'>";
				foreach ($posts_s as $post_s) {
					setup_postdata($post_s);
					the_post();
					echo '<li><a href="'.get_permalink($post_s->ID).'">'.get_the_title($post_s->ID).'</a></li>';
					the_shop_info($post_s->ID);
				}
				wp_reset_postdata();
				echo "</ul>";
			}
        }

    $myvariable = ob_get_clean();
    // give back data
    echo $myvariable;
	// exit
	exit;
}