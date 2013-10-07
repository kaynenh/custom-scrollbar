<?php
/*
Plugin Name: Custom Scrollbar
Plugin URI: 
Description: A simple wordpress plugin for adding custom scrollbars to the css overflow portions of a webpage
Version: 1.0
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

?>