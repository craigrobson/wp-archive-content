<?php if(!defined('ABSPATH')) exit;

/**
 * Plugin Name: Archive Content
 * Description: Edit the content on archive pages
 * Version: 1.0.0
 * Author: Craig Robson
 */

define('WPAC_URL', plugin_dir_url(__FILE__));

if(!class_exists('WPAC')) {

  class WPAC {
    /**
     * Holds the current post_type
     * @var string
     */
    private $post_type = '';

    /**
     * Class constructor
     * Setup the actions and filters
     * @return void
     */
    public function __construct() {
      // Enqueue the CSS and JS for the admin
      add_action('admin_enqueue_scripts', array($this, 'enqueue_assets'));
      // Add the form to edit.php
      add_action('admin_notices', array($this, 'render_form'));
    }

    /**
     * Enqueue the CSS and JS files
     * @return void
     */
    public function enqueue_assets() {
      // Stop if we're not on the right page
      if(!$this->wpac_page()) {
        return;
      }

      wp_enqueue_style('wpac-style', WPAC_URL . "/wpac.css");
      wp_enqueue_script('wpac-scripts', WPAC_URL . "/wpac.js", array('jquery'), null, true);
    }

    /**
     * Render the form to edit the content
     * @return void
     */
    public function render_form() {
      // Stop if we're not on the right page
      if(!$this->wpac_page()) {
        return;
      }

      ob_start();
?>
<div id="wpac-wrapper">
  <button class="button button-primary" type="button">
    Edit Archive Content
  </button>
  <div id="wpac-editor">
    <div id="wpac-title-holder">
      <input type="text" name="title" value="<?php the_archive_title(); ?>" />
    </div>
    <div id="wpac-wysiwyg-holder">
      <?php wp_editor(the_archive_description(), 'description'); ?>
    </div>
    <button type="submit" class="button">Update Content</button>
    <input type="hidden" name="wpac" value="<?php echo $this->post_type; ?>" />
  </div>
</div>
<?php
      echo ob_get_clean();


    }

    /**
     * Check if the current page is a WPAC page
     * @return bool
     */
    private function wpac_page() {
      global $pagenow, $wp;
      if($pagenow !== "edit.php" || !isset($wp->query_vars['post_type'])) {
        return false;
      }

      $this->post_type = $wp->query_vars['post_type'];
      $post_type_object = get_post_type_object($this->post_type);
      if(!$post_type_object) {
        return false;
      }
      // If this post_type doesn't "has_archive"
      return $post_type_object->has_archive;
    }
  }

  global $wpac;
  $wpac = new WPAC;

}
