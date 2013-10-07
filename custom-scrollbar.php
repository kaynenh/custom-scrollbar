<?php
/*
Plugin Name: Custom Scrollbar
Plugin URI: https://github.com/kaynenh/custom-scrollbar
Description: A simple wordpress plugin for adding custom-skinned css scrollbars
Version: 0.3
Author: Kaynen Heikkinen
Author URI: http://www.kaynen.com
License: GPL2
*/

/*
Copyright 2013

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as 
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/*
Credits:

1. Plugin built with Plugin Options Start Kit, http://wordpress.org/plugins/plugin-options-starter-kit/
2. jQuery Custom Scrollbar is built by msubala on github, https://github.com/mzubala/jquery-custom-scrollbar

*/

// register jquery and style on initialization
add_action('init', 'cs_register_script');
function cs_register_script() {
		wp_register_script( 'custom_scrollbar_jquery', plugins_url('/js/jquery.custom-scrollbar.js', __FILE__), array('jquery'), '2.5.1' );

		wp_register_style( 'custom_scrollbar_style', plugins_url('/css/jquery.custom-scrollbar.css', __FILE__), false, '1.0.0', 'all');
		
}

// use the registered jquery and style above
add_action('wp_enqueue_scripts', 'cs_enqueue_style');

function cs_enqueue_style(){
	 wp_enqueue_script('custom_scrollbar_jquery');

	 wp_enqueue_style( 'custom_scrollbar_style' );
}

// Add custom classnames to the jQuery document.ready in WP Footer
function cs_inline_script() {
	$cs_classnames = cs_classnames_array();
	if( wp_script_is( 'jquery', 'done' ) ) {
	?>
	<script type="text/javascript">
		jQuery(document).ready(function ($) {
			<?php
			foreach ($cs_classnames as $cssname)
			echo "$('.$cssname').customScrollbar();\n";
			?>
		});
	</script>
	<?php
	}
}
add_action( 'wp_footer', 'cs_inline_script' );

// Set-up Action and Filter Hooks
register_activation_hook(__FILE__, 'cs_add_defaults');
register_uninstall_hook(__FILE__, 'cs_delete_plugin_options');
add_action('admin_init', 'cs_init' );
add_action('admin_menu', 'cs_add_options_page');
add_filter( 'plugin_action_links', 'cs_plugin_action_links', 10, 2 );

// --------------------------------------------------------------------------------------
// CALLBACK FUNCTION FOR: register_uninstall_hook(__FILE__, 'cs_delete_plugin_options')
// --------------------------------------------------------------------------------------
// THIS FUNCTION RUNS WHEN THE USER DEACTIVATES AND DELETES THE PLUGIN. IT SIMPLY DELETES
// THE PLUGIN OPTIONS DB ENTRY (WHICH IS AN ARRAY STORING ALL THE PLUGIN OPTIONS).
// --------------------------------------------------------------------------------------

// Delete options table entries ONLY when plugin deactivated AND deleted
function cs_delete_plugin_options() {
	delete_option('cs_options');
}

// ------------------------------------------------------------------------------
// CALLBACK FUNCTION FOR: register_activation_hook(__FILE__, 'cs_add_defaults')
// ------------------------------------------------------------------------------
// THIS FUNCTION RUNS WHEN THE PLUGIN IS ACTIVATED. IF THERE ARE NO THEME OPTIONS
// CURRENTLY SET, OR THE USER HAS SELECTED THE CHECKBOX TO RESET OPTIONS TO THEIR
// DEFAULTS THEN THE OPTIONS ARE SET/RESET.
//
// OTHERWISE, THE PLUGIN OPTIONS REMAIN UNCHANGED.
// ------------------------------------------------------------------------------

// Define default option settings
function cs_add_defaults() {
	$tmp = get_option('cs_options');
    if(($tmp['chk_default_options_db']=='1')||(!is_array($tmp))) {
		delete_option('cs_options'); // so we don't have to reset all the 'off' checkboxes too! (don't think this is needed but leave for now)
		$arr = array(
						"classnames" => "This type of control allows a large amount of information to be entered all at once. Set the 'rows' and 'cols' attributes to set the width and height.",
						"second_text_area" => "This type of control allows a large amount of information to be entered all at once. Set the 'rows' and 'cols' attributes to set the width and height."
		);
		update_option('cs_options', $arr);
	}
}

function cs_classnames_array() {
	//get css class names
	$options = get_option('cs_options');
	$classnames = $options['classnames'];
	$optionarray = Array();
	$optionarray = explode(", ", $classnames);
	return $optionarray;
}

// ------------------------------------------------------------------------------
// CALLBACK FUNCTION FOR: add_action('admin_init', 'cs_init' )
// ------------------------------------------------------------------------------
// THIS FUNCTION RUNS WHEN THE 'admin_init' HOOK FIRES, AND REGISTERS YOUR PLUGIN
// SETTING WITH THE WORDPRESS SETTINGS API. YOU WON'T BE ABLE TO USE THE SETTINGS
// API UNTIL YOU DO.
// ------------------------------------------------------------------------------

// Init plugin options to white list our options
function cs_init(){
	register_setting( 'cs_plugin_options', 'cs_options', 'cs_validate_options' );
}

// ------------------------------------------------------------------------------
// CALLBACK FUNCTION FOR: add_action('admin_menu', 'cs_add_options_page');
// ------------------------------------------------------------------------------
// THIS FUNCTION RUNS WHEN THE 'admin_menu' HOOK FIRES, AND ADDS A NEW OPTIONS
// PAGE FOR YOUR PLUGIN TO THE SETTINGS MENU.
// ------------------------------------------------------------------------------

// Add menu page
function cs_add_options_page() {
	add_options_page('Custom Scrollbar Settings', 'Custom Scrollbar Settings', 'manage_options', __FILE__, 'cs_render_form');
}

// ------------------------------------------------------------------------------
// CALLBACK FUNCTION SPECIFIED IN: add_options_page()
// ------------------------------------------------------------------------------
// THIS FUNCTION IS SPECIFIED IN add_options_page() AS THE CALLBACK FUNCTION THAT
// ACTUALLY RENDER THE PLUGIN OPTIONS FORM AS A SUB-MENU UNDER THE EXISTING
// SETTINGS ADMIN MENU.
// ------------------------------------------------------------------------------

// Render the Plugin options form
function cs_render_form() {
	?>
	<div class="wrap">
		
		<!-- Display Plugin Icon, Header, and Description -->
		<div class="icon32" id="icon-options-general"><br></div>
		<h2>Custom Scrollbar Settings</h2>
		<p>Options for Custom Scrollbar</p>

		<!-- Beginning of the Plugin Options Form -->
		<form method="post" action="options.php">
			<?php settings_fields('cs_plugin_options'); ?>
			<?php $options = get_option('cs_options'); ?>

			<!-- Table Structure Containing Form Controls -->
			<!-- Each Plugin Option Defined on a New Table Row -->
			<table class="form-table">

				<!-- Text Area Control -->
				<tr>
					<th scope="row">Class Names to use for Custom Scrollbar</th>
					<td>
						<textarea name="cs_options[classnames]" rows="7" cols="100" type='textarea'><?php echo $options['classnames']; ?></textarea><br /><span style="color:#666666;margin-left:2px;">Separate Different Classes by commas - ex. class1, class2</span>
					</td>
				</tr>
			</table>
			<p class="submit">
			<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
			</p>
		</form>

	</div>
	<?php	
}

// Sanitize and validate input. Accepts an array, return a sanitized array.
function cs_validate_options($input) {
	 // strip html from textboxes
	$input['classnames'] =  wp_filter_nohtml_kses($input['classnames']); // Sanitize textarea input (strip html tags, and escape characters)
	return $input;
}

// Display a Settings link on the main Plugins page
function cs_plugin_action_links( $links, $file ) {

	if ( $file == plugin_basename( __FILE__ ) ) {
		$cs_links = '<a href="'.get_admin_url().'options-general.php?page=custom-scrollbar/custom-scrollbar.php">'.__('Settings').'</a>';
		// make the 'Settings' link appear first
		array_unshift( $links, $cs_links );
	}

	return $links;
}


?>