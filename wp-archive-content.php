<?php if(!defined('ABSPATH')) exit;

/**
 * Plugin Name: Archive Content
 * Description: Allow the archive title and archive description to be changed. Adds a simple form to posts edit page to change the output of the archive title and archive description functions.
 * Version: 1.0.0
 * Author: Craig Robson
 * Author URI: http://craigrobson.github.io/
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
      // Maybe process the post
      add_action('admin_init', array($this, 'maybe_process_form'));

      // Filters that return the archive title and description
      add_filter('get_the_archive_title', array($this, 'get_the_archive_title'));
      add_filter('get_the_archive_description', array($this, 'get_the_archive_description'));
    }

    /**
     * Return the archive title
     * @param string $title
     * @return string
     */
    public function get_the_archive_title($title) {
      if(is_post_type_archive()) {
        $post_type = get_post_type();
        $post_type_object = get_post_type_object($post_type);
        // The default title is the post_type label
        $default = $post_type_object->label;
        $title = get_option("wpac_{$post_type}_title", $default);
      }
      // Filter what we've got and return
      return apply_filters('wpac_the_title', $title);
    }

    /**
     * Return the archive description
     * @param string $description
     * @return string
     */
    public function get_the_archive_description($description) {
      if(is_post_type_archive()) {
        $post_type = get_post_type();
        $post_type_object = get_post_type_object($post_type);
        // The default description is the post_type description
        $default = $post_type_object->description;
        $description = get_option("wpac_{$post_type}_description", $default);
      }
      // Filter what we've got and return
      return apply_filters('wpac_the_description', $description);
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
      // If the wpac-updated is set - display the message
      if(isset($_GET['wpac-updated'])) {
  ?>
  <div class="notice updated is-dismissible">
    <p>Archive content updated. <a href="<?php echo esc_url(get_post_type_archive_link($this->post_type)); ?>">View the Archive</a></p>
  </div>
  <?php
      }
?>
<div id="wpac-wrapper">
  <button class="button button-primary" type="button" id="wpac-trigger">
    Edit Archive Content
  </button>
  <div id="wpac-editor" class="hidden">
    <form action="" method="post">
      <div id="wpac-title-holder">
        <label for="title">
          Archive Title:
          <input type="text" name="title" value="<?php the_archive_title(); ?>" />
        </label>
      </div>
      <div id="wpac-wysiwyg-holder">
        <?php wp_editor(get_the_archive_description(), 'description', array(
          'teeny' => true
        )); ?>
      </div>
      <button type="submit" class="button">Update Content</button>
      <input type="hidden" name="wpac" value="<?php echo $this->post_type; ?>" />
    </form>
  </div>
</div>
<?php
      echo ob_get_clean();
    }

    /**
     * Maybe precess the form
     * @return void
     */
    public function maybe_process_form() {
      // Stop if the wpac POST isn't set
      if(!isset($_POST['wpac'])) {
        return;
      }

      $post_type = $_POST['wpac'];
      // Check this post_type
      $post_type_object = get_post_type_object($post_type);
      if(!$post_type) {
        return;
      }

      $title = isset($_POST['title']) ? $_POST['title'] : get_the_archive_title();
      $description = isset($_POST['description']) ? $_POST['description'] : '';

      update_option("wpac_{$post_type}_title", $title);
      update_option("wpac_{$post_type}_description", $description);
      wp_redirect(admin_url("edit.php?post_type={$post_type}&wpac-updated=true"));
      exit;
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
