<?php
/*
	Plugin Name: Individual Author Link
	Plugin URI: https://www.mikesarhage.de
	Description: This plugin enables you to switch the author link url. So you can choose a static Page of your WordPress installation or an individual url. 
	Version: 1.1
	Author: Mike Sarhage
	Author URI: https://www.mikesarhage.de
	Text Domain: msar-individual-author-link
	License: GPLv3 or later
*/


/**
 * replace the author link url in frontend
 */
function msar_new_authorLink_frontend( $link, $author_id, $author_nicename ) {
    
    if ( get_the_author_meta('msar_authorLink_activated') == 'true') {
        //$link = get_the_author_meta( 'msar_authorLink_activated' );
		//$link ='www.google.de';
		$link = get_page_link(get_the_author_meta('msar_authorLink_pageId' ));
    }
    return $link;

}
add_filter( 'author_link', 'msar_new_authorLink_frontend', 10, 3 );


/**
 * generate the necessary fields in the backend
 */
add_action( 'show_user_profile', 'msar_new_authorLink_backend' );
add_action( 'edit_user_profile', 'msar_new_authorLink_backend' );

function msar_new_authorLink_backend( $user ) { 
	if(get_the_author_meta( 'msar_authorLink_activated', $user->ID)=='true') {
		$checked='checked';
	}  else {
		$checked='';
	}

	$preselect=get_the_author_meta('msar_authorLink_pageId', $user->ID);
	?>

	<h3>Individual Authorlink Url</h3>
	<table class="form-table">
		<tr>
			<th><label for="author-link"><?php _e('Activate the redirection','msar-individual-author-link'); ?></label></th>
			<td> <input type="checkbox" name="msar_authorLink_activated" value="true" <?php echo $checked; ?>/><span class="description"><?php _e('Check to change the Author Link Url to the following Page.','msar-individual-author-link'); ?></span></td>
		</tr>
		<tr>
			<th><label for="author-link"><?php _e('Author link','msar-individual-author-link'); ?></label></th>
			<td>
				<?php 
					$args = array(
					'depth'                 => 0,
					'child_of'              => 0,
					'selected'              => $preselect,
					'echo'                  => 1,
					'name'                  => 'msar_authorLink_pageId',
					'id'                    => 'msar_authorLink_pageId', // string
					'class'                 => 'msar_authorLink_pageId', // string
					'show_option_none'      => NULL, // string
					'show_option_no_change' => NULL, // string
					'option_none_value'     => NULL, // string
					);
					wp_dropdown_pages($args);
				?>
				<span class="description"><?php _e('Please select a page as Author-Link-Destination','msar-individual-author-link'); ?></span>
			</td>
		</tr>

	</table>
<?php }

/**
 * save the field-values
 */
function msar_authorLink_fields_save( $user_id ) {
	if ( !current_user_can( 'edit_user', $user_id ) )
		return false;
	update_user_meta( $user_id, 'msar_authorLink_activated', $_POST['msar_authorLink_activated'] );
	update_user_meta( $user_id, 'msar_authorLink_pageId', $_POST['msar_authorLink_pageId'] );
}
add_action( 'personal_options_update', 'msar_authorLink_fields_save' );
add_action( 'edit_user_profile_update', 'msar_authorLink_fields_save' );

/**
 * load translation / text-domain
 */
add_action('plugins_loaded', 'msar_load_textdomain');
function msar_load_textdomain() {
	load_plugin_textdomain( 'msar-individual-author-link', false, dirname( plugin_basename(__FILE__) ) . '/lang/' );
}