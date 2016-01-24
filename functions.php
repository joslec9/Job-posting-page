<?php
/**
 * Theme functions file
 *
 * DO NOT MODIFY THIS FILE. Make a child theme instead: http://codex.wordpress.org/Child_Themes
 *
 * @package ClassiClean
 *
 */


// processes the entire ad thumbnail logic for featured ads
if ( !function_exists('tb_ad_featured_thumbnail') ) :
	function tb_ad_featured_thumbnail() {
		global $post;

		// go see if any images are associated with the ad
    $image_id = cp_get_featured_image_id($post->ID);

		// set the class based on if the hover preview option is set to "yes"
		if (get_option('cp_ad_image_preview') == 'yes')	$prevclass = 'preview'; else $prevclass = 'nopreview';

		if ( $image_id > 0 ) {

			// get 50x50 v3.0.5+ image size
			$adthumbarray = wp_get_attachment_image($image_id, 'ad-small');

			// grab the large image for onhover preview
			$adlargearray = wp_get_attachment_image_src($image_id, 'large');
			$img_large_url_raw = $adlargearray[0];

			// must be a v3.0.5+ created ad
			if($adthumbarray) {
				echo '<a href="'. get_permalink() .'" title="'. the_title_attribute('echo=0') .'" class="'.$prevclass.'" data-rel="'.$img_large_url_raw.'">'.$adthumbarray.'</a>';

			// maybe a v3.0 legacy ad
			} else {
				$adthumblegarray = wp_get_attachment_image_src($image_id, 'ad-small');
				$img_thumbleg_url_raw = $adthumblegarray[0];
				echo '<a href="'. get_permalink() .'" title="'. the_title_attribute('echo=0') .'" class="'.$prevclass.'" data-rel="'.$img_large_url_raw.'">'.$adthumblegarray.'</a>';
			}

		// no image so return the placeholder thumbnail
		} else {
			echo '<a href="'. get_permalink() .'" title="'. the_title_attribute('echo=0') .'"><img class="attachment-sidebar-thumbnail" alt="" title="" src="'. get_stylesheet_directory_uri() .'/images/no-thumb-100.jpg" /></a>';
		}

	}
endif;


// load the css files correctly
//if ( !function_exists('tb_load_styles') ) :
function tb_load_styles()
{
	$dir = dirname( get_bloginfo('stylesheet_url') )."/";

	//wp_register_style('classiclean-bnw', get_stylesheet_directory_uri().'/styles/blacknwhite.css');
	//wp_enqueue_style('classiclean-bnw');

	wp_register_style('bootstrap', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css');
	wp_enqueue_style('bootstrap');

	wp_register_style('jquery-fileupload', $dir.'css/jquery.fileupload.css');
	wp_enqueue_style('jquery-fileupload');


}
add_action('wp_enqueue_scripts', 'tb_load_styles');
//endif;

function tb_print_styles() {

	wp_register_style('googleFonts', 'http://fonts.googleapis.com/css?family=Trocchi');
    wp_enqueue_style( 'googleFonts');

    wp_register_style('FontAwesome', '//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css');
    wp_enqueue_style( 'FontAwesome');


}
add_action('wp_print_styles', 'tb_print_styles', 20);

// to speed things up, don't load these scripts in the WP back-end (which is the default)
if ( !is_admin() ) {


}

function tb_insert_close_button( $item_output, $item, $depth, $args ) {
	$page_id = tb_get_page_id_for_template( 'tpl-categories.php' );
	if ( $item->object_id == $page_id ) {
		$item_output .= '<a href="#" id="close_categories" class="close_trigger"><img border="0" alt="Close" title="Close" src="' . get_template_directory_uri() .'/images/cross.png"></a>';
	}
	return $item_output;
}
add_filter( 'walker_nav_menu_start_el', 'tb_insert_close_button', 9, 4 );

function tb_get_page_id_for_template( $template ) {
	$page_q = new WP_Query( array(
		'post_type' => 'page',
		'meta_key' => '_wp_page_template',
		'meta_value' => $template,
		'posts_per_page' => 1,
		'suppress_filters' => true
	) );

	if ( empty( $page_q->posts ) )
		$page_id = 0;
	else
		$page_id = $page_q->posts[0]->ID;

	$page_id = apply_filters( 'appthemes_page_id_for_template', $page_id, $template );
	return $page_id;
}

function tb_insert_script( $template_path ) {
    wp_enqueue_script( 'classiclean-script', get_stylesheet_directory_uri() . '/js/scripts.js', array( 'jquery' ), null, true );

}
add_action('wp_enqueue_scripts', 'tb_insert_script');


function remove_ad_ref()
{
	remove_action( 'appthemes_after_post_content', 'cp_do_ad_ref_id' );

}
add_action('init', 'remove_ad_ref');


// Mod @lucas: Enqueue Scripts
function load_fileupload_scripts()
{
	$dir = dirname( get_bloginfo('stylesheet_url') )."/";

	wp_enqueue_script( 'theme-scripts', get_template_directory_uri() . '/includes/js/theme-scripts.js', array( 'jquery' ), '3.3.3', true );


	if ( $cp_options->enable_featured && is_page_template( 'tpl-ads-home.php' ) ) {

		wp_enqueue_script( 'jqueryeasing', get_template_directory_uri() . '/includes/js/easing.js', array( 'jquery' ), '1.3', true );

		wp_enqueue_script( 'jcarousellite', get_template_directory_uri() . '/includes/js/jcarousellite.min.js', array( 'jquery', 'jquery-ui-slider' ), '1.8.3', true );

	}

	wp_enqueue_script( 'tinynav', get_template_directory_uri() . '/includes/js/jquery.tinynav.js', array( 'jquery' ), '1.1', true );

	wp_enqueue_script('jquery-ui', 'https://code.jquery.com/ui/1.11.1/jquery-ui.min.js', array('jquery'), '1.11.1', false);

	wp_enqueue_script( 'bootstrap', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js', array('jquery'), '3.2', true);

//	if (is_single())
		wp_enqueue_script('jquery.fileupload', $dir.'js/jquery.fileupload.min.js', array('jquery'), '5.42', true);

}

add_action('wp_enqueue_scripts', 'load_fileupload_scripts', 10);

// @lucas: Custom email function
function cp_contact_ad_owner_email2( $post_id , $files) {
	$errors = new WP_Error();

	// check for required post data
	$expected = array( 'from_name', 'from_email', 'subject', 'message' );
	foreach ( $expected as $field_name ) {
		if ( empty( $_POST[ $field_name ] ) ) {
			$errors->add( 'empty_field', __( 'ERROR: All fields are required.', APP_TD ) );
			return $errors;
		}
	}

	// check for required anti-spam post data
	$expected_numbers = array( 'rand_total', 'rand_num', 'rand_num2' );
	foreach ( $expected_numbers as $field_name ) {
		if ( ! isset( $_POST[ $field_name ] ) || ! is_numeric( $_POST[ $field_name ] ) ) {
			$errors->add( 'invalid_captcha', __( 'ERROR: Incorrect captcha answer.', APP_TD ) );
			return $errors;
		}
	}

	// verify captcha answer
	$rand_post_total = (int) $_POST['rand_total'];
	$rand_total = (int) $_POST['rand_num'] + (int) $_POST['rand_num2'];
	if ( $rand_total != $rand_post_total )
		$errors->add( 'invalid_captcha', __( 'ERROR: Incorrect captcha answer.', APP_TD ) );

	// verify email
	if ( ! is_email( $_POST['from_email'] ) )
		$errors->add( 'invalid_email', __( 'ERROR: Incorrect email address.', APP_TD ) );

	// verify post
	$post = get_post( $post_id );
	if ( ! $post )
		$errors->add( 'invalid_post', __( 'ERROR: Ad does not exist.', APP_TD ) );

	if ( $errors->get_error_code() )
		return $errors;

	$mailto = get_the_author_meta( 'user_email', $post->post_author );

	$from_name = appthemes_filter( appthemes_clean( $_POST['from_name'] ) );
	$from_email = appthemes_clean( $_POST['from_email'] );
	$subject = appthemes_filter( appthemes_clean( $_POST['subject'] ) );
	$posted_message = appthemes_filter( appthemes_clean( $_POST['message'] ) );

	$sitename = wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES );
	$siteurl = home_url('/');
	$permalink = get_permalink( $post_id );

	$message = sprintf( __( 'Someone is interested in your ad listing: %s', APP_TD ), $permalink ) . "\r\n\r\n";
	$message .= '"' . wordwrap( $posted_message, 70 ) . '"' . "\r\n\r\n";
	$message .= sprintf( __( 'Name: %s', APP_TD ), $from_name ) . "\r\n";
	$message .= sprintf( __( 'E-mail: %s', APP_TD ), $from_email ) . "\r\n\r\n";
	$message .= '-----------------------------------------' . "\r\n";
	$message .= sprintf( __( 'This message was sent from %s', APP_TD ), $sitename ) . "\r\n";
	$message .=  $siteurl . "\r\n\r\n";
	$message .= __( 'Sent from IP Address: ', APP_TD ) . appthemes_get_ip() . "\r\n\r\n";

	$email = array( 'to' => $mailto, 'subject' => $subject, 'message' => $message, 'from' => 	$from_email, 'from_name' => $from_name );
	$email = apply_filters( 'cp_email_user_ad_contact', $email, $post_id );

	APP_Mail_From::apply_once( array( 'email' => $email['from'], 'name' => $email['from_name'], 'reply' => true ) );

	$resumes = explode(',', $files[0]);
	$attachments = array();
	foreach ($resumes as $resume)
		array_push($attachments, WP_CONTENT_DIR.'/themes/classiclean/server/files/'.$resume);

	wp_mail( $email['to'], $email['subject'], $email['message'] , null , $attachments);

	return $errors;
}

// display the login message in the header
function cpro_login_head() {


		if ( is_user_logged_in() ) :
			global $current_user;
			$current_user = wp_get_current_user();
			$display_user_name = cp_get_user_name();
			$logout_url = cp_logout_url();
			?>
			<?php _e( 'Welcome,', APP_TD ); ?> <strong><?php echo $display_user_name; ?></strong> [ <a href="<?php echo CP_DASHBOARD_URL; ?>"><?php _e( 'My Dashboard', APP_TD ); ?></a> | <a href="<?php echo $logout_url; ?>"><?php _e( 'Log out', APP_TD ); ?></a> ]&nbsp;
		<?php else : ?>
			<?php _e( 'Welcome,', APP_TD ); ?> <strong><?php _e( 'visitor!', APP_TD ); ?></strong> [ <a href="<?php echo appthemes_get_registration_url(); ?>"><?php _e( 'Register', APP_TD ); ?></a> | <a href="<?php echo wp_login_url(); ?>"><?php _e( 'Login', APP_TD ); ?></a> ]&nbsp;
		<?php endif;


}
