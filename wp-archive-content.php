<?php if(!defined('ABSPATH')) exit;

/**
 * Plugin Name: Archive Content
 * Description: Edit the content on archive pages
 * Version: 1.0.0
 * Author: Craig Robson
 */

/**
 * Make "the_archive_description" function return our description
 * Use the "get_the_archive_description" filter to get the custom description
 * @param string $description
 * @return string
 */
function wpac_get_archive_description($description) {
  if(is_post_type_archive()) {
    $post_type = get_post_type();
    $post_type_object = get_post_type_object($post_type);
    // Get the option, but return the current description if it's set
    $description = get_option("wpac_{$post_type}_archive_description", $post_type_object->description);
  }
  // Return what we've got
  return add_filter('wpac_description', $description);
}
add_filter('get_the_archive_description', 'wpac_get_archive_description');

/**
 * Make "the_archive_title" function return our title
 * Use the "get_the_archive_title" filter to get the custom title
 * @param string $title
 * @return string
 */
function wpac_get_archive_title($title) {
  if(is_post_type_archive()) {
    $post_type = get_post_type();
    $post_type_object = get_post_type_object($post_type);
    // Get the option, but return the post_type label if it's not set
    $title = get_option("wpac_{$post_type}_archive_title", $post_type_object->label);
  }
  // Return what we've got
  return add_filter('wpac_title', $title);
}
add_filter('get_the_archive_title', 'wpac_get_archive_title');

/**
 * Maybe save the archive form
 * @return void
 */
function wpac_maybe_process_form() {
  // Check for the hidden input
  if(!isset($_POST['wpac_save_form'])) {
    return;
  }
  // Get the current post_type
  $post_type = get_post_type();
  // Check for the title and description
  $title = isset($_POST['_archive_title']) ? $_POST['_archive_title'] : get_the_archive_title();
  $description = isset($_POST['_archive_description']) ? $_POST['_archive_description'] : '';
  // Update the options
  update_option("wpac_{$post_type}_archive_title", $title);
  update_option("wpac_{$post_type}_archive_description", $description);
}
add_action('admin_init', 'wpac_maybe_process_form');

/**
 * Add the form to the post_types edit.php
 * @return void
 */
function wpac_add_post_type_form() {
  global $pagenow, $wp;
  // Stop if we're not on the right page
  // Or if the post_type query_var is not set
  if($pagenow !== "edit.php" || !isset($wp->query_vars['post_type'])) {
    return;
  }

  $post_type = $wp->query_vars['post_type'];
  // If this post_type doesn't "has_archive"
  $post_type_object = get_post_type_object($post_type);
  var_dump($post_type_object);
  ob_start();
?>
<div id="">

</div>
<?php
  echo ob_get_clean();


}
add_action('admin_notices', 'wpac_add_post_type_form');
