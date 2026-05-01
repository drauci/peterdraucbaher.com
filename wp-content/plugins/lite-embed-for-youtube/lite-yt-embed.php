<?php
/*
Plugin Name: Lite YouTube Embed
Plugin URI:
Description: "Renders faster than a sneeze." This plugin is based on <a href="https://github.com/paulirish/lite-youtube-embed">Paul Irish's script</a> for embedding YouTube videos. Provide videos with a supercharged focus on visual performance. This custom element renders just like the real thing but approximately 224X faster.
Version: 3.3
Author: Azim Hikmatov
Author URI:
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/
?>
<?php
// Add button to code editor;
function shortcode_btn_script() {
	if(wp_script_is("quicktags")) {
?>
<script type="text/javascript">
// this function is used to retrieve the selected text from the text editor;
function getSel() {
	var txtarea = document.getElementById("content");
	var start = txtarea.selectionStart;
	var finish = txtarea.selectionEnd;
	return txtarea.value.substring(start, finish);
}
QTags.addButton(
	"code_shortcode",
	"Lite YT Embed",
	callback
);
function callback() {
	var selected_text = getSel();
	QTags.insertContent("<lite-youtube videoid=\"\">" +  selected_text + "</lite-youtube>");
}
</script>
<?php }
}
add_action("admin_print_footer_scripts", "shortcode_btn_script");
?>
<?php
// enqueue plugin script and style;
function lye_enqueue() {
	wp_enqueue_style( 'lye-style', plugins_url( 'lite-yt-embed.css', __FILE__ ), '', '1.0' );
	wp_enqueue_script( 'lye-script', plugins_url( 'lite-yt-embed.js', __FILE__ ), null, '1.0', true );
}
add_action( 'wp_enqueue_scripts', 'lye_enqueue' );

// load localization - for the future;
/*
function lye_load_textdomain() {
	load_plugin_textdomain( 'lye', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' ); 
	}
add_action( 'plugins_loaded', 'lye_load_textdomain' );
*/
?>