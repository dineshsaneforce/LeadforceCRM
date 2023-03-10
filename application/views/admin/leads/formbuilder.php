<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<?php if (isset($form)) {
    echo form_hidden('form_id', $form->id);
} ?>
<div id="wrapper">
   <div class="content">
      <div class="row">
         <div class="col-md-12">
            <div class="panel_s">
               <div class="panel-body">
               <?php if (isset($form)) { ?>
                  <ul class="nav nav-tabs" role="tablist">
                     <li role="presentation" class="active">
                        <a href="#tab_form_build" aria-controls="tab_form_build" role="tab" data-toggle="tab">
                        <?php echo _l('form_builder'); ?>
                        </a>
                     </li>
                     <li role="presentation">
                        <a href="#tab_form_information" aria-controls="tab_form_information" role="tab" data-toggle="tab">
                        <?php echo _l('form_information'); ?>
                        </a>
                     </li>
                     <li role="presentation">
                        <a href="#tab_form_integration" aria-controls="tab_form_integration" role="tab" data-toggle="tab">
                        <?php echo _l('form_integration_code'); ?>
                        </a>
                     </li>
                  </ul>
                  <?php } ?>
                  <div class="tab-content">
                     <?php if (isset($form)) { ?>
                     <div role="tabpanel" class="tab-pane active" id="tab_form_build">
                        <div id="build-wrap"></div>
                     </div>
                     <div role="tabpanel" class="tab-pane" id="tab_form_integration">
                        <p><?php echo _l('form_integration_code_help'); ?></p>
                       <p>
                         <span class="label label-default">
                            <a href="<?php echo site_url('forms/wtl/'.$form->form_key); ?>" target="_blank">
                          <?php echo site_url('forms/wtl/'.$form->form_key); ?>
                         </span>
                        </a>
                       </p>
                        <textarea class="form-control" rows="2"><iframe width="600" height="850" src="<?php echo site_url('forms/wtl/'.$form->form_key); ?>" frameborder="0" allowfullscreen></iframe></textarea>

                        <div class="row">
                              <div class="col-md-6">
                              <div class="panel_s mtop15">
                              <div class="panel-body">
                                 <div class="row">
                                    <div class="col-xs-4">
                                       <p class="text-muted">Webhook URL</p>
                                    </div>
                                    <div class="col-xs-8">
                                       <p class=""><?php echo base_url('webhooks/webforms/lead/' . $configure_id) ?></p>
                                    </div>

                                    <div class="col-xs-4">
                                       <p class="text-muted">Method</p>
                                    </div>
                                    <div class="col-xs-8">
                                       <p class="">POST</p>
                                    </div>

                                    <div class="col-xs-4">
                                       <p class="text-muted">Format</p>
                                    </div>
                                    <div class="col-xs-8">
                                       <p class="">JSON</p>
                                    </div>

                                    <div class="col-xs-4">
                                       <p class="text-muted">Fields</p>
                                    </div>
                                    <div class="col-xs-8">
                                       <?php if ($form->form_data) {
                                          $form_fields = array();
                                          foreach (json_decode($form->form_data) as $field) {
                                             if ($field->name == '')
                                                continue;
                                             $form_fields[$field->name] = $field->label;
                                          }
                                          echo '<pre><code style="color: #c7254e;">' . trim(json_encode($form_fields, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)) . '</code></pre>';
                                       } ?>
                                    </div>
                                 </div>
                              </div>
                           </div>
                              </div>
                           </div>


                     </div>
                     <?php } ?>
                     <div role="tabpanel" class="tab-pane<?php if (!isset($form)) { echo ' active'; } ?>" id="tab_form_information">
                        <?php if (!isset($form)) { ?>
                        <h4 class="font-medium-xs bold no-mtop"><?php echo _l('form_builder_create_form_first'); ?></h4>
                        <hr />
                        <?php } ?>
                        <?php echo form_open($this->uri->uri_string(), array('id'=>'form_info')); ?>
                        <div class="row">
                           <div class="col-md-6">
                              <div class="leads-filter-column">
                              <?php $value = (isset($form) ? $form->lead_source : ''); ?>
                              <?php
                                 echo render_select('lead_source',$sources,array('id','name'),_l('Source'),$value,array('required'=>true),array());
                              ?>
                              </div>
                              <?php $value = (isset($form) ? $form->name : ''); ?>
                              <?php echo render_input('name', 'form_name', $value,'',['maxlength'=>100]); ?>
                              <?php
                                 if (get_option('recaptcha_secret_key') != '' && get_option('recaptcha_site_key') != '') { ?>
                              <div class="form-group">
                                 <label for=""><?php echo _l('form_recaptcha'); ?></label><br />
                                 <div class="radio radio-inline radio-danger">
                                    <input type="radio" name="recaptcha" id="racaptcha_0" value="0"<?php if (isset($form) && $form->recaptcha == 0 || !isset($form)) {
                                         echo ' checked';
                                     } ?>>
                                    <label for="recaptcha_0"><?php echo _l('settings_no'); ?></label>
                                 </div>
                                 <div class="radio radio-inline radio-success">
                                    <input type="radio" name="recaptcha" id="recaptcha_1" value="1"<?php if (isset($form) && $form->recaptcha == 1) {
                                         echo ' checked';
                                     } ?>>
                                    <label for="recaptcha_1"><?php echo _l('settings_yes'); ?></label>
                                 </div>
                              </div>
                              <?php } ?>
                              <!-- <div class="form-group select-placeholder">
                                 <label for="language" class="control-label"><i class="fa fa-question-circle" data-toggle="tooltip" data-title="<?php echo _l('form_lang_validation_help'); ?>"></i> <?php echo _l('form_lang_validation'); ?></label>
                                 <select name="language" id="language" class="form-control selectpicker" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                    <option value=""></option>
                                    <?php foreach ($languages as $availableLanguage) {
                                     ?>
                                    <option value="<?php echo $availableLanguage; ?>"<?php if ((isset($form) && $form->language == $availableLanguage) || (!isset($form) && get_option('active_language') == $availableLanguage)) {
                                         echo ' selected';
                                     } ?>><?php echo ucfirst($availableLanguage); ?></option>
                                    <?php } ?>
                                 </select>
                              </div> -->
                              <?php $value = (isset($form) ? $form->submit_btn_name : 'Submit'); ?>
                              <?php echo render_input('submit_btn_name', 'form_btn_submit_text', $value,'',['maxlength'=>39]); ?>
                              <?php $value = (isset($form) ? $form->success_submit_msg : ''); ?>
                              <?php echo render_textarea('success_submit_msg', 'form_success_submit_msg', $value,['maxlength'=>250]); ?>
<!-- 
                             <div class="checkbox checkbox-primary">
                            <input type="checkbox" name="mark_public" id="mark_public" <?php if (isset($form) && $form->mark_public == 1) {
                                     echo 'checked';
                                 } ?>>
                            <label for="mark_public">
                                <?php echo _l('auto_mark_as_public'); ?></label>
                            </div>
                              <div class="checkbox checkbox-primary">
                                 <input type="checkbox" name="allow_duplicate" id="allow_duplicate" <?php if (isset($form) && $form->allow_duplicate == 1 || !isset($form)) {
                                     echo 'checked';
                                 } ?>>
                                 <label for="allow_duplicate"><?php echo _l('form_allow_duplicate', _l('lead_lowercase')); ?></label>
                              </div>
                              <div class="duplicate-settings-wrapper row<?php if (isset($form) && $form->allow_duplicate == 1 || !isset($form)) {
                                     echo ' hide';
                                 } ?>">
                                 <div class="col-md-12">
                                    <hr />
                                 </div>
                                 <div class="col-md-6">
                                    <div class="form-group">
                                       <label for="track_duplicate_field"><?php echo _l('track_duplicate_by_field'); ?></label><br />
                                       <select class="selectpicker track_duplicate_field" data-width="100%" name="track_duplicate_field" id="track_duplicate_field" data-none-selected-text="">
                                          <option value=""></option>
                                          <?php foreach ($db_fields as $field) {
                                     ?>
                                          <option value="<?php echo $field->name; ?>"<?php if (isset($form) && $form->track_duplicate_field == $field->name) {
                                         echo ' selected';
                                     }
                                     if (isset($form) && $form->track_duplicate_field_and == $field->name) {
                                         echo 'disabled';
                                     } ?>><?php echo $field->label; ?></option>
                                          <?php } ?>
                                       </select>
                                    </div>
                                 </div>
                                 <div class="col-md-6">
                                    <div class="form-group">
                                       <label for="track_duplicate_field_and"><?php echo _l('and_track_duplicate_by_field'); ?></label><br />
                                       <select class="selectpicker track_duplicate_field_and" data-width="100%" name="track_duplicate_field_and" id="track_duplicate_field_and" data-none-selected-text="">
                                          <option value=""></option>
                                          <?php foreach ($db_fields as $field) {
                                     ?>
                                          <option value="<?php echo $field->name; ?>"<?php if (isset($form) && $form->track_duplicate_field_and == $field->name) {
                                         echo ' selected';
                                     }
                                     if (isset($form) && $form->track_duplicate_field == $field->name) {
                                         echo 'disabled';
                                     } ?>><?php echo $field->label; ?></option>
                                          <?php } ?>
                                       </select>
                                    </div>
                                 </div>
                                 <div class="col-md-12">
                                    <div class="checkbox checkbox-primary">
                                       <input type="checkbox" name="create_task_on_duplicate" id="create_task_on_duplicate" <?php if (isset($form) && $form->create_task_on_duplicate == 1 || !isset($form)) {
                                     echo 'checked';
                                 } ?>>
                                       <label for="create_task_on_duplicate"><i class="fa fa-question-circle" data-toggle="tooltip" data-title="<?php echo _l('create_the_duplicate_form_data_as_task_help'); ?>"></i> <?php echo _l('create_the_duplicate_form_data_as_task', _l('lead_lowercase')); ?></label>
                                    </div>
                                 </div>
                              </div> -->
                           </div>
                           <div class="col-md-6">
								<?php $teamleaderselected = '';
								foreach ($teamleaders as $teamleader) {
									if (isset($form) && $form->teamleader == $teamleader['staffid']) {
										$teamleaderselected = $teamleader['staffid'];
									}
								}
								//echo render_select('teamleader', $teamleaders, array('staffid', array('firstname', 'lastname')), 'teamleader', $selected);

								//echo render_leads_status_select($statuses, (isset($form) ? $form->lead_status : get_option('leads_default_status')), 'lead_import_status', 'lead_status', [], true);

								$selected = '';
								foreach ($members as $staff) {
									if (isset($form) && $form->responsible == $staff['staffid']) {
										$selected = $staff['staffid'];
									}
								}
								
								echo render_select('responsible', $members, array('staffid', array('firstname', 'lastname')), 'leads_import_assignee', $selected); ?>
                              <hr />
                              
                                    <?php $value = (isset($form) ? $form->btn_color : ''); ?>
                                    <?php $attrs = array('class'=>' colorpicker-input','onkeyup'=>'check_pipline()','type'=>'text'); ?>
                                    <?php //echo render_color_picker('color','color',$value,'text',$attrs); ?>
                                    <?php echo render_color_picker('btn_color','Button Color',$value,$attrs); ?>
                                    <div class="text-danger" id="color_id" style="display:none">Please enter valid color</div>

                                    <?php $value = (isset($form) ? $form->txt_color : ''); ?>
                                    <?php $attrs = array('class'=>' colorpicker-input','onkeyup'=>'check_pipline()','type'=>'text'); ?>
                                    <?php //echo render_color_picker('color','color',$value,'text',$attrs); ?>
                                    <?php echo render_color_picker('txt_color','Button Text Color',$value,$attrs); ?>
                                    <div class="text-danger" id="color_id" style="display:none">Please enter valid color</div>

                                    <!-- <div class="text_shape">
                                        <div class="form-group">
                                            <label for="txt_shape" class="control-label">Field Shape</label><br />
                                            <select required="1" name="txt_shape" class="selectpicker" data-none-selected-text="Field Shape">
                                                <option value="0"></option>
                                                <option value="0" <?php if(isset($form) && $form->txt_shape==0) { echo 'selected'; } ?>>Box</option>
                                                <option value="1" <?php if(isset($form) && $form->txt_shape==1) { echo 'selected'; } ?>>Radious</option>
                                            </select>
                                        </div>
                                    </div> -->
                                </div>
                                
                                
                        </div>
                        <div class="btn-bottom-toolbar text-right">
                            <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
                        </div>
                        <?php echo form_close(); ?>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
     <div class="btn-bottom-pusher"></div>
   </div>
</div>
<?php init_tail(); ?>
<script src="<?php echo base_url('assets/plugins/form-builder/form-builder.min.js'); ?>"></script>
<script>
var buildWrap = document.getElementById('build-wrap');
var formData = <?php echo json_encode($formData); ?>;

if(formData.length){
  // If user paste with styling eq from some editor word and the Codeigniter XSS feature remove and apply xss=remove, may break the json.
  formData = formData.replace(/=\\/gm, "=''");
}
</script>
<?php $this->load->view('admin/includes/_form_js_formatter'); ?>
<script>

   $(function(){

    $('body').on('blur', '.form-field.editing', function () {
        $.Shortcuts.start();
    });

    $('body').on('focus', '.form-field.editing', function () {
        $.Shortcuts.stop();
    });

     var formBuilder = $(buildWrap).formBuilder(fbOptions);
     var $create_task_on_duplicate = $('#create_task_on_duplicate');

     $('#allow_duplicate').on('change',function(){
       $('.duplicate-settings-wrapper').toggleClass('hide');
     });

     $('#notify_lead_imported').on('change',function(){
          $('.select-notification-settings').toggleClass('hide');
     });

     $('#track_duplicate_field,#track_duplicate_field_and').on('change',function(){
       var selector = ($(this).hasClass('track_duplicate_field') ? 'track_duplicate_field_and' : 'track_duplicate_field')
         $('#'+selector+' option').removeAttr('disabled',true);
         var val = $(this).val();
         if(val !== ''){
            $('#'+selector+' option[value="'+val+'"]').attr('disabled',true);
         }
         $('#'+selector+'').selectpicker('refresh');
     });

     setTimeout(function(){
         $( ".form-builder-save" ).wrap( "<div class='btn-bottom-toolbar text-right'></div>" );
         $btnToolbar = $('body').find('#tab_form_build .btn-bottom-toolbar');
         $btnToolbar = $('#tab_form_build').append($btnToolbar);
         $btnToolbar.find('.btn').addClass('btn-info');
     },100);

     $('body').on('click','.save-template',function() {
       $.post(admin_url+'leads/save_form_data',{
        formData:formBuilder.formData,
        id:$('input[name="form_id"]').val()
      }).done(function(response){
         response = JSON.parse(response);
         if(response.success == true){
           alert_float('success',response.message);
         }
       });
     });

      jQuery.validator.addMethod("noSpace", function(value, element) { 
         return value == '' || value.trim().length != 0;  
      }, "No space please an don't leave it empty");

      jQuery.validator.addMethod("noNumeric", function(value, element) { 
         return value.match(/^\d*[a-z][a-z\d`!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?~\s]*$/i);  
      }, "Invalid format");


     appValidateForm('#form_info',{
       name:{
         required: true,
         noSpace:true,
         noNumeric:true,
       },
       teamleader: 'required',
       lead_status: 'required',
       language:'required',
       success_submit_msg:'required',
       submit_btn_name:{
         required: true,
         noSpace:true,
         noNumeric:true,
       },
       responsible: {
         required:true
       }
     });

     var $notifyTypeInput = $('input[name="notify_type"]');
     $notifyTypeInput.on('change',function(){
        $('#form_info').validate().checkForm()
     });
     $notifyTypeInput.trigger('change');

     $create_task_on_duplicate.on('change',function(){
        $('#form_info').validate().checkForm()
     });

     $create_task_on_duplicate.trigger('change');

   });

</script>
</body>
</html>
