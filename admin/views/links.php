<?php
/**
 * Represents the view for quick links management.
 */
?>
<div class="wrap" ng-app="qaapp">

<div ng-controller="LinkController">
  <h2><?php _e('Quick Links', 'quick-admin' ); ?><a class="add-new-h2" href="javascript:void(0)" ng-click="resetForm()"><?php _e('Add New', 'quick-admin' ); ?></a></h2>

  <div id="poststuff">

    <div id="post-body" class="metabox-holder columns-2">

      <!-- main content -->
      <div id="post-body-content">


              <form  class="qa-form-wrap">

                <div class="form-row">
                  <div class="grid one-half">
                    <div class="form-field-label"><label for=""><?php _e('Title', 'quick-admin') ; ?>:<span class="asterisk">*</span></label></div>
                    <div class="form-field-value">
                      <input type="text" name="qa_link_title" ng-model="myForm.qa_link_title" class="regular-text code" placeholder="<?php _e('Enter Title', 'quick-admin') ; ?>" required autocomplete="off" />
                    </div>
                  </div>
                  <div class="grid one-half">
                    <div class="form-field-label"><label for=""><?php _e('Link', 'quick-admin') ; ?>:<span class="asterisk">*</span></label></div>
                    <div class="form-field-value">
                      <input type="url" name="qa_link_url" ng-model="myForm.qa_link_url" placeholder="<?php _e('Enter Link', 'quick-admin') ; ?>"  class="regular-text code" required autocomplete="off"  />
                    </div>
                  </div>
                </div> <!-- .form-row -->

                <div class="form-row">
                  <div class="grid one-half">
                    <div class="form-field-label"><label for=""><?php _e('Menu Order', 'quick-admin') ; ?>:</label></div>
                    <div class="form-field-value">
                      <input type="text" name="qa_link_menu_order" ng-model="myForm.qa_link_menu_order" class="regular-text code" autocomplete="off" placeholder="<?php _e('Enter a number', 'quick-admin') ; ?>"   />
                    </div>

                  </div><!-- .grid one-half -->
                  <div class="grid one-half">
                    <div class="form-field-label"><label for=""><?php _e('Open in new window', 'quick-admin') ; ?>:</label></div>
                    <div class="form-field-value">
                      <input type="checkbox" ng-model="myForm.qa_link_open_new" ng-true-value="1" ng-false-value="0" />&nbsp;<?php _e('Check to Enable', 'quick-admin') ; ?>
                    </div>

                  </div><!-- .grid one-half -->
                </div><!-- .form-row -->


                <input type="hidden" id="qa_form_action" name="qa_form_action" ng-model="myForm.qa_form_action"  />
                <input type="hidden" id="qa_link_id" name="qa_link_id" ng-model="myForm.qa_link_id" />
                <button ng-click="myForm.submitTheForm()" class="button button-primary" ng-disabled="myForm.$invalid">{{buttonText}}</button>
              </form>


        <div class="meta-box-sortables ui-sortable">

          <div class="postbox">

          <div>
            <button ng-click="loadData()" style="display:none;">Refresh</button>
               <table  class="widefat" style="">
                 <thead>
                  <tr>
                    <th><?php _e('Link', 'quick-admin') ; ?></th>
                    <th><?php _e('URL', 'quick-admin') ; ?></th>
                    <th style="width:70px;"><?php _e('New Tab', 'quick-admin') ; ?></th>
                    <th style="width:60px;"><?php _e('Order', 'quick-admin') ; ?></th>
                    <th style="width:100px;"><?php _e('Action', 'quick-admin') ; ?></th>
                  </tr>

                 </thead>

                 <tbody>
                   <tr ng-repeat="d in linkData">
                      <td class="row-title">{{d.title | limitTo: 40 }}</td>
                      <td>{{d.href | limitTo: 40 }}</td>
                      <td>{{d.open_new}}</td>
                      <td>{{d.menu_order}}</td>
                      <td><a href="javascript:void(0)" ng-click="btnEditForm(d)">Edit</a>&nbsp;<a href="javascript:void(0)" ng-click="btnDeleteForm(d)">Delete</a></td>
                    </tr>
                 </tbody>
               </table>
              </div>

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

</div> <!-- .LinkController -->
</div> <!-- .wrap -->
