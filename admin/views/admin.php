<?php
/**
 * Represents the view for the admin settings.
 */
?>

<div class="wrap">

  <h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

  <div id="poststuff">

    <div id="post-body" class="metabox-holder columns-2">

      <!-- main content -->
      <div id="post-body-content">

        <div class="meta-box-sortables ui-sortable">

          <div class="postbox">


            <div class="inside">


              <form action="options.php" method="post">
              <?php settings_fields('qa-plugin-options-group'); ?>
              <?php do_settings_sections('quick-admin-general'); ?>


              <?php submit_button(__('Save Changes', 'quick-admin')); ?>
              </form>



            </div> <!-- .inside -->

          </div> <!-- .postbox -->

        </div> <!-- .meta-box-sortables .ui-sortable -->

      </div> <!-- post-body-content -->

      <!-- sidebar -->
      <div id="postbox-container-1" class="postbox-container">

        <?php require_once( ( plugin_dir_path(__FILE__) ) . 'admin-right.php'); ?>

      </div> <!-- #postbox-container-1 .postbox-container -->

    </div> <!-- #post-body .metabox-holder .columns-2 -->

    <br class="clear">
  </div> <!-- #poststuff -->

</div> <!-- .wrap -->
