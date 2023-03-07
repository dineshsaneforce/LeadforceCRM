<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
$can_user_edit = false;
if (is_admin(get_staff_user_id()) || $project->teamleader == get_staff_user_id() || in_array(get_staff_user_id(), $ownerHierarchy) || (!empty($my_staffids) && in_array($project->teamleader, $my_staffids) && !in_array($project->teamleader, $viewIds))) {
   $can_user_edit = true;
}
if ($project->approved == 0 && !$deal_rejected) {
   $can_user_edit = false;
}

if ($project->approved == 0 && $deal_rejected && get_staff_user_id() != $project->created_by) {
   $can_user_edit = false;
}
$hasHIstory = $this->approval_model->hasHistory('projects', $project->id) ? true : false;
$hasApprovalFlow = $this->workflow_model->getflows('deal_approval', 0, ['service' => 'approval_level']);

?>
<style>
   .deal-info-heading {
      background: #f9fafc;
      padding: 7px;
      padding-left: 0px;
      border-radius: 3px;
      margin-bottom: 15px;
   }

   .deal-info-heading h4 {
      color: var(--theme-primary-light);
   }

   .deal-detail-group {
      margin-bottom: 10px;
   }
</style>
<h4 class="text-primary">Overview</h4>
<?php if (!empty($deal_need_fields) && in_array("clientid", $deal_need_fields)) : ?>

   <div class="clientid deal-detail-group">
      <p class="text-muted "><?php echo _l('project_customer'); ?></p>
      <div class="data_display dropdown">
         <span class="updated_text">
            <input type="hidden" id="clid" value="<?php echo $project->clientid; ?>">
            <?php if ($project->client_data->company) : ?>
               <a class="h5" href="<?php echo ($can_user_edit) ? admin_url('clients/client/' . $project->clientid) : '#'; ?>">
                  <i class="fa fa-building-o  mright5"></i>
                  <?php echo $project->client_data->company; ?>
               </a>
            <?php else : ?>
               <a class="text-muted h5"><i class="fa fa-building-o  mright5"></i>Nothing selected</a>
            <?php endif; ?>
         </span>
         <?php if (has_permission('projects', '', 'edit')) { 
             if ($can_user_edit == true) { ?>
            <a href="#" class="data_display_btn">
               <i class="fa fa-pencil"></i>
            </a>
            <div class="deal-field-update-dropdown">
               <div class="panel_s no-mbot">
                  <div class="panel-body">
                     <form class="data-edit-form">
                     <p class="text-muted"><?php echo in_array('clientid',$mandatory_fields)?'<small class="req text-danger">* </small>':''  ?>Update Organization</p>
                     <?php $selected = (isset($project) ? $project->clientid : ''); ?>
                     <div class="select-placeholder clientiddiv form-group-select-input-groups_in[] input-group-select">
                        <div class="input-group input-group-select select-groups_in[]">
                           <select id="clientid_copy_project" name="clientid_copy_project" data-live-search="true" data-width="100%" class="ajax-search" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                              <?php $selected = (isset($project) ? $project->clientid : '');
                              if ($selected == '') {
                                 $selected = (isset($customer_id) ? $customer_id : '');
                              }
                              if ($selected != '' && $selected != 0) {
                                 $rel_data = get_relation_data('customer', $selected);
                                 $rel_val = get_relation_values($rel_data, 'customer');
                                 echo '<option value="' . $rel_val['id'] . '" selected>' . $rel_val['name'] . '</option>';
                              } ?>

                           </select>
                           <div class="input-group-addon" style="opacity: 1;"><a href="#" data-toggle="modal" data-target="#clientid_add_modal"><i class="fa fa-plus"></i></a></div>
                        </div>

                     </div>
                     <br>
                     <button class="btn btn-info pull-right data_edit_btn" data-val="clientid_copy_project">Save Changes</button>
                     <a class="btn pull-right mright5 close-dropdown">Cancel</a>
                     </form>
                  </div>
               </div>
            </div>
         <?php }} ?>

      </div>
      <div>
         <?php if (isset($project->client_data->website) && $project->client_data->website) : ?>
            <p class="text-muted h5"><i class="fa fa-globe mright5" aria-hidden="true"></i><a class="text-muted" href="<?php echo maybe_add_http($project->client_data->website); ?>" target="_blank"><?php echo $project->client_data->website; ?></a></p>
         <?php endif; ?>
         <?php if (isset($project->client_data->phonenumber) && $project->client_data->phonenumber) : ?>
            <p class="text-muted h5"><i class="fa fa-phone mright5" aria-hidden="true"></i><a class="text-muted" href="tel:<?php echo $project->client_data->phonenumber; ?>"><?php echo $project->client_data->phonenumber; ?></a></p>
         <?php endif; ?>
         <?php $client_address == array();
         if (isset($project->client_data->address) && $project->client_data->address) {
            $client_address[] = $project->client_data->address;
         }
         if (isset($project->client_data->city) && $project->client_data->city) {
            $client_address[] = $project->client_data->city;
         }
         if (isset($project->client_data->state) && $project->client_data->state) {
            $client_address[] = $project->client_data->state;
         }
         if (isset($project->client_data->zip) && $project->client_data->zip) {
            $client_address[] = $project->client_data->zip;
         }
         ?>
         <?php if ($client_address) : ?>
            <p class="text-muted h5"><i class="fa fa-map-marker mright5" aria-hidden="true"></i><span class="text-muted"><?php echo implode(',', $client_address); ?>.</span></p>
         <?php endif; ?>
      </div>
   </div>
<?php endif; ?>

<?php if (!empty($deal_need_fields) && in_array("project_start_date", $deal_need_fields)) : ?>
   <div class="deal-detail-group">
      <p class="text-muted"><?php echo _l('project_start_date'); ?></p>
      <p class="h5"><i class="fa fa-calendar mright5" aria-hidden="true"></i><?php echo _d($project->start_date); ?></p>
   </div>
<?php endif; ?>

<?php if (!empty($deal_need_fields) && in_array("project_deadline", $deal_need_fields)) : ?>
   <div class="deadline deal-detail-group">
      <p class="text-muted "><?php echo _l('expected_closing_date'); ?></p>
      <div class="data_display dropdown">
         <span class="updated_text">
            <?php if ($project->deadline) : 
                  $date=date_create($project->deadline);
                  $deadline_class="";
                  if($date < date_create()){
                     $deadline_class="text-danger";
                  }
               ?>
               <a class="h5 <?php echo $deadline_class ?>">
                  <i class="fa fa-flag-checkered mright5" aria-hidden="true"></i>
                  <?php echo _d($project->deadline); ?>
               </a>
            <?php else : ?>
               <a class="text-muted h5">
                  <i class="fa fa-flag-checkered mright5" aria-hidden="true"></i>
                  Nothing Selected
               </a>
            <?php endif; ?>
         </span>
         <?php if (has_permission('projects', '', 'edit')) { 
          if ($can_user_edit == true) { ?>
            <a href="#" class="data_display_btn">
               <i class="fa fa-pencil"></i>
            </a>
            <div class="deal-field-update-dropdown">
               <div class="panel_s no-mbot">
                  <div class="panel-body">
                     <form class="data-edit-form">
                     <p class="text-muted"><?php echo in_array('clientid',$mandatory_fields)?'<small class="req text-danger">* </small>':''  ?>Update Expected Closing Date</p>
                     <div class="input-group date">
                        <input type="text" id="deadline" name="deadline" class="form-control datepicker" data-date-min-date="<?php echo (isset($project) ? _d($project->start_date) : ' '); ?>" value="<?php echo (isset($project) ? _d($project->deadline) : ' '); ?>" autocomplete="off" readonly>
                     </div>
                     <br>
                     <button class="btn btn-info pull-right data_edit_btn" data-val="deadline">Save Changes</button>
                     <a class="btn pull-right mright5 close-dropdown">Cancel</a>
                     </form>
                  </div>
               </div>
            </div>
         <?php }} ?>
      </div>
   </div>
<?php endif; ?>
<?php if (!empty($deal_need_fields) && (in_array("project_contacts[]", $deal_need_fields) || in_array("primary_contact", $deal_need_fields))) : ?>
   <div class="team-contacts project-overview-team-contacts">
      <hr class="hr-panel-heading project-area-separation" />
      <?php if (has_permission('projects', '', 'edit')) { ?>
         <?php if ($can_user_edit == true) { ?>
            <div class="inline-block pull-right mright10 project-contact-settings" data-toggle="tooltip" data-title="<?php echo _l('add_new', _l('contact')); ?>">
               <?php if (!empty($deal_need_fields) && (in_array("project_contacts[]", $deal_need_fields))) { ?>
                  <a href="#" data-toggle="modal" data-target="#project_contacts_modal"><i class="fa fa-plus"></i></a>
               <?php } ?>
            </div>
            <div class="inline-block pull-right mright10 project-contact-settings" data-toggle="tooltip" data-title="<?php echo _l('change'); ?>">

               <a href="#" data-toggle="modal" class="pull-right getcontactsbyorg" data-target="#add-edit-contacts"><i class="fa fa-pencil"></i></a>
            </div>
      <?php }
      } ?>
      <p class="h4 text-primary">
         <?php echo _l('project_contacts'); ?>
      </p>
      <div class="clearfix"></div>
      <?php
      if (count($contacts) == 0) {
         echo '<p class="text-muted mtop10 no-mbot">' . _l('no_project_contacts') . '</p>';
      } else {
         foreach ($contacts as $contact) { ?>
            <div style="display:flex" class="media">
               <div>
                  <a href="<?php echo $can_user_edit ? admin_url('clients/view_contact/' . $contact["contacts_id"]) : '#'; ?>">
                     <img src="<?php echo contact_profile_image_url($contact['contacts_id'], array('staff-profile-image-small', 'media-object')); ?>" id="contact-img" class="staff-profile-image-small">
                  </a>
               </div>
               <div class="pleft5">
                  <div style="display:flex">
                     <h5 class="media-heading mtop5" style="width:auto; float:left;"><a class="h5" href="<?php echo $can_user_edit ? admin_url('clients/view_contact/' . $contact["contacts_id"]) : '#'; ?>"><?php echo get_contact_full_name($contact['contacts_id']); ?></a>
                        <?php if ((has_permission('projects', '', 'edit')) && $contact['is_primary'] == 0) { ?>
                           <?php if ($can_user_edit == true) { ?>
                              <a href="<?php echo admin_url('projects/remove_team_contact/' . $project->id . '/' . $contact['contacts_id']); ?>" class="text-danger _delete"><i class="fa fa fa-times"></i></a>
                        <?php }
                        } ?>
                     </h5>
                     <?php if ($can_user_edit && $project->approved == 1) : ?>
                        <a href="#" onclick="callfromdeal(<?php echo $contact['contacts_id'] . ',' . $contact['project_id'] . ',' . $contact['phonenumber'] . ',\'deal\''; ?>);" title="Call Now"><img src="<?php echo APP_BASE_URL ?>/assets/images/call.png" style="width:25px;margin-left:10px;"></a>
                     <?php endif; ?>
                     <?php
                     if ($contact['is_primary'] == 1) { ?>
                        <span style="margin:0; top:0" class="primarycontact"> Primary </span>
                     <?php } ?>
                  </div>
                  <div style="padding-top: 5px;">
                  <?php if ($contact['email']) : ?>
                     <a href="mailto:<?php echo $contact['email'] ?>" class="text-muted"><i class="fa fa-envelope mright5"></i><?php echo $contact['email'] ?></a>
                  <?php endif; ?>
                  <?php if ($contact['phonenumber']) : ?>
                     &nbsp<a href="tel:<?php echo $contact['phonenumber'] ?>" class="text-muted"><i class="fa fa-phone mright5"></i><?php echo $contact['phonenumber'] ?></a>
                  <?php endif; ?>
                  </div>
               </div>
            </div>
      <?php }
      } ?>
   </div>
<?php endif; ?>

<?php if (!empty($deal_need_fields) && in_array("project_members[]", $deal_need_fields)) : ?>
   <div class="team-members project-overview-team-members">
      <hr class="hr-panel-heading project-area-separation" />
      <?php if (has_permission('projects', '', 'edit')) { ?>
         <?php if ($can_user_edit == true) { ?>
            <div class="inline-block pull-right mright10 project-member-settings" data-toggle="tooltip" data-title="<?php echo _l('add_edit_members'); ?>">
               <a href="#" data-toggle="modal" class="pull-right" data-target="#add-edit-members"><i class="fa fa-plus"></i></a>
            </div>
      <?php }
      } ?>

      <p class="h4 text-primary">
         <?php echo _l('project_members'); ?>
      </p>

      <div class="clearfix"></div>
      <?php
      if (!$members || count($members) == 0) {
         echo '<p class="text-muted mtop10 no-mbot">' . _l('no_project_members') . '</p>';
      } else {
         foreach ($members as $member) { ?>
            <div class="media">
               <div class="media-left">
                  <a href="<?php echo $can_user_edit ? admin_url('staff/member/' . $member["staff_id"]) : '#'; ?>">
                     <?php echo staff_profile_image($member['staff_id'], array('staff-profile-image-small', 'media-object')); ?>
                  </a>
               </div>
               <div class="media-body">

                  <h5 class="media-heading mtop5"><a class="h5" href="<?php echo $can_user_edit ? admin_url('staff/member/' . $member["staff_id"]) : '#'; ?>"><?php echo get_staff_full_name($member['staff_id']); ?></a>
                     <?php if (has_permission('projects', '', 'create') || $member['staff_id'] == get_staff_user_id()) { ?>
                        <!-- <br /><small class="text-muted"><?php echo _l('total_logged_hours_by_staff') . ': ' . seconds_to_time_format($member['total_logged_time']); ?></small> -->
                     <?php } ?>

                     <?php if (has_permission('projects', '', 'edit')) { ?>
                        <?php if ($can_user_edit == true) { ?>
                           <a href="<?php echo admin_url('projects/remove_team_member/' . $project->id . '/' . $member['staff_id']); ?>" class="text-danger _delete"><i class="fa fa fa-times"></i></a>
                     <?php }
                     } ?>

                  </h5>
               </div>
            </div>
      <?php }
      } ?>
   </div>
<?php endif; ?>
<?php if (!empty($deal_need_fields) && in_array("description", $deal_need_fields)) : ?>
   <div class="tc-content project-overview-description">
      <hr class="hr-panel-heading project-area-separation" />
      <h4 class="text-primary hover-show">
         <?php echo _l('project_description'); ?>
         <?php if (has_permission('projects', '', 'edit')) { ?>
            <?php if ($can_user_edit == true) { ?>
               <a href="#" data-toggle="modal" class="h5 hover-edit" data-target="#edit_description"><i class=" fa fa-pencil"></i></a>
         <?php }
         } ?>
      </h4>
      <?php if (empty($project->description)) {
         echo '<p class="text-muted no-mbot mtop15">' . _l('no_description_project') . '</p>';
      }
      ?>
      <?php
      echo check_for_links($project->description); ?>
   </div>
<?php endif; ?>

<?php $tags = get_tags_in($project->id, 'project'); ?>
<?php if ($tags) : ?>
   <div class="data_display dropdown project-overview-tags">
      <hr class="hr-panel-heading project-area-separation hr-10" />
      <?php echo '<span class="h4 text-primary"><i class="fa fa-tag" aria-hidden="true"></i> ' . _l('tags')?>
      <?php echo '</span>'; ?>
      <?php if (has_permission('projects', '', 'edit')) { ?>
            <?php if ($can_user_edit == true) { ?>
               <a href="#" class="data_display_btn">
                  <i class="fa fa-pencil"></i>
               </a>
               <div class="deal-field-update-dropdown" >
                  <div class="panel_s no-mbot">
                        <div class="panel-body">
                           <p class="text-muted">Update Tags</p>
                           <div class="input-group date">
                              <input type="text" class="tagsinput" id="tags" name="tags" value="<?php echo prep_tags_input($tags); ?>" data-role="tagsinput">
                           </div>
                           <div id="company_exists_info" class="hide"></div>
                           <br>
                           <a class="btn btn-info pull-right data_edit_btn" data-val="tags">Save Changes</a>
                           <button  class="btn pull-right mright5 close-dropdown">Cancel</button>
                        </div>
                  </div>
               </div>
         <?php }
         } ?>
      <div class="tags-read-only-custom">
      <input type="text" class="tagsinput read-only" id="" name="" value="<?php echo prep_tags_input($tags); ?>" data-role="tagsinput">
      </div>
      <br>
   </div>
<?php endif; ?>
<?php $custom_fields1 = get_custom_fields('projects');
$custom_fields2 = get_custom_fields('leads');
$custom_fields = array_merge($custom_fields1, $custom_fields2);
?>
<?php if ($custom_fields) : ?>
   <hr class="hr-panel-heading project-area-separation hr-10" />
   <h4 class="text-primary">Custom Fields</h4>
   <?php foreach ($custom_fields as $field) : ?>
      <?php $value = get_custom_field_value($project->id, $field['id'], 'projects');
      if ($field['type'] == 'location') {
         $value = '<iframe src = "https://maps.google.com/maps?q=' . $value . '&hl=es;z=14&output=embed"></iframe>';
      }
      ?>


      <div class="<?php echo $field['slug']; ?> deal-detail-group">
         <p class="text-muted "><?php echo ucfirst($field['name']); ?></p>

         <div class="data_display dropdown">
            <span class="h5 updated_text">
               <?php if ($value) : ?>
                  <?php echo $value; ?>
               <?php else : ?>
                  _ _ _   &nbsp;  _ _ _
               <?php endif; ?>
            </span>
            <?php if (has_permission('projects', '', 'edit')) {
                if ($can_user_edit == true) { ?>
               <a href="#" class="data_display_btn">
                  <i class="fa fa-pencil"></i>
               </a>
               <div class="deal-field-update-dropdown">
                  <div class="panel_s no-mbot">
                     <div class="panel-body">
                        <form class="data-edit-form-custom-fields">
                        <p class="text-muted"><?php echo $field['required']?'<small class="req text-danger">* </small>':''  ?>Update <?php echo ucfirst($field['name']); ?></p>
                        <?php
                        switch ($field['type']) {
                           case 'textarea':
                        ?>
                              <div class="input-group">
                                 <textarea id="<?php echo $field['slug']; ?>" name="<?php echo $field['slug']; ?>" class="form-control "><?php echo (isset($value) ? $value : ' '); ?></textarea>
                              </div>
                              
                           <?php
                              break;
                           case 'date_picker':
                           ?>
                              <div class="input-group date">
                                 <input type="text" id="<?php echo $field['slug']; ?>" name="<?php echo $field['slug']; ?>" class="form-control datepicker" value="<?php echo (!empty($value) ? date('d-m-Y', strtotime($value)) : ' '); ?>" autocomplete="off" readonly>
                              </div>
                           <?php
                              break;
                           case 'date_range':
                           ?>
                              <div class="input-group date">
                                 <input type="text" id="<?php echo $field['slug']; ?>" name="<?php echo $field['slug']; ?>" class="form-control daterangepicker" value="<?php echo (!empty($value) ? date('d-m-Y', strtotime($value)) : ' '); ?>" autocomplete="off" readonly>
                              </div>
                           <?php
                              break;
                           case 'date_picker_time':
                           ?>
                              <div class="input-group date">
                                 <input type="text" id="<?php echo $field['slug']; ?>" name="<?php echo $field['slug']; ?>" class="form-control datetimepicker" value="<?php echo (!empty($value) ? date('d-m-Y H:i:s', strtotime($value)) : ' '); ?>" autocomplete="off" readonly>
                              </div>
                           <?php
                              break;
                           case 'date_time_range':
                           ?>
                              <div class="input-group date">
                                 <input type="text" id="<?php echo $field['slug']; ?>" name="<?php echo $field['slug']; ?>" class="form-control datetimerangepicker" value="<?php echo (!empty($value) ? date('d-m-Y', strtotime($value)) : ' '); ?>" autocomplete="off" readonly>
                              </div>
                           <?php
                              break;
                           case 'time_picker':
                           ?>
                              <div class="input-group date">
                                 <input type="text" id="<?php echo $field['slug']; ?>" name="<?php echo $field['slug']; ?>" class="form-control timepicker" value="<?php echo $value; ?>" autocomplete="off" readonly>
                              </div>
                           <?php
                              break;
                           case 'number':
                           ?>
                              <div class="input-group">
                                 <input type="number" id="<?php echo $field['slug']; ?>" name="<?php echo $field['slug']; ?>" class="form-control " value="<?php echo (isset($value) ? $value : ' '); ?>">
                              </div>
                           <?php
                              break;
                           case 'input':
                           ?>
                              <div class="input-group">
                                 <input type="text" id="<?php echo $field['slug']; ?>" name="<?php echo $field['slug']; ?>" class="form-control " value="<?php echo (isset($value) ? $value : ' '); ?>">
                              </div>
                           <?php
                              break;
                           case 'select':
                           ?>
                              <div class="input-group">
                                 <select id="<?php echo $field['slug']; ?>" name="<?php echo $field['slug']; ?>" data-live-search="true" data-width="100%" class=" selectpicker _select_input_group">
                                    <?php
                                    $all_vals = explode(',', $field['options']);
                                    foreach ($all_vals as $all_val1) {
                                       $selected = '';
                                       if ($all_val1 == $value) {
                                          $selected = 'selected="selected"';
                                       }
                                       echo '<option value="' . $all_val1 . '" ' . $selected . '>' . $all_val1 . '</option>';
                                    }
                                    ?>
                                 </select>
                              </div>
                           <?php
                              break;
                           case 'multiselect':
                           ?>
                              <div class="input-group">
                                 <select id="<?php echo $field['slug']; ?>" name="<?php echo $field['slug']; ?>" multiple="1" data-live-search="true" data-width="100%" class=" selectpicker form-control  custom-field-multi-select">
                                    <?php
                                    $all_vals = explode(',', $field['options']);
                                    foreach ($all_vals as $all_val1) {
                                       $all_val1 = trim($all_val1);
                                       $selected = '';
                                       if ($all_val1 == $value) {
                                          $selected = 'selected="selected"';
                                       } else {
                                          $cur_vals = explode(',', $value);
                                          if (!empty($cur_vals)) {
                                             if (in_array(trim($all_val1), $cur_vals)) {
                                                $selected = 'selected="selected"';
                                             }
                                          }
                                       }
                                       echo '<option value="' . $all_val1 . '" ' . $selected . '>' . $all_val1 . '</option>';
                                    }
                                    ?>
                                 </select>
                              </div>
                           <?php
                              break;
                           case 'checkbox':
                           ?>
                              <div class="input-group">
                                 <div class="form-group chk">
                                    <?php
                                    $all_vals = explode(',', $field['options']);
                                    foreach ($all_vals as $all_val1) {
                                       $all_val1 = trim($all_val1);
                                       $selected = '';
                                       if ($all_val1 == $value) {
                                          $selected = 'checked="checked"';
                                       } else {
                                          $cur_vals = explode(',', $value);
                                          if (!empty($cur_vals)) {
                                             if (in_array(trim($all_val1), $cur_vals)) {
                                                $selected = 'checked="checked"';
                                             }
                                          }
                                       }
                                       $input_id = 'cfc_' . $field['id'] . '_' . slug_it($all_val1) . '_' . app_generate_hash();

                                       $fields_html = '<div class="checkbox' . ($field['display_inline'] == 1 ? ' checkbox-inline' : '') . '">';
                                       $fields_html .= '<input class="custom_field_checkbox"  ' . $selected . ' value="' . $all_val1 . '" id="' . $input_id . '" type="checkbox" name="' . $field['slug'] . '[]">';
                                       if ($field['required'] == 1) {
                                          $fields_html .= '<label for="' . $input_id . '" class="cf-chk-label"> <small class="req text-danger">* </small>' . $all_val1 . '</label>';
                                       } else {
                                          $fields_html .= '<label for="' . $input_id . '" class="cf-chk-label">' . $all_val1 . '</label>';
                                       }
                                       // $fields_html .= '<input type="hidden" name="' . $field['slug'] . '[]" value="cfk_hidden">';
                                       $fields_html .= '</div>';

                                       echo $fields_html;
                                       // echo '<input type="checkbox" value="'.$all_val1.'" '.$selected.'>'.$all_val1.'</option>';
                                    }
                                    ?>
                                 </div>
                              </div>
                           <?php
                              break;
                           case 'link':
                           ?>
                              <div class="input-group">
                                 <?php
                                 $fields_html = '<div class="form-group cf-hyperlink" data-fieldto="' . $field['fieldto'] . '" data-field-id="' . $field['id'] . '" data-value="' . html_escape($value) . '" data-field-name="' . html_escape($field['name']) . '">';
                                 $fields_html .= '<label class="control-label" for="' . $field['slug'] . '">' . $field_name . '</label></br>';

                                 $fields_html .= '<a id="custom_fields_' . $field['fieldto'] . '_' . $field['id'] . '_popover" type="button" href="javascript:">' . _l('cf_translate_input_link_tip') . '</a>';

                                 $fields_html .= '<input type="hidden" ' . ($field['required'] == 1 ? 'data-custom-field-required="1"' : '') . ' value="" id="custom_fields[' . $field['fieldto'] . '][' . $field['id'] . ']" name="' . $field['slug'] . '">';

                                 $field_template = '';
                                 $field_template .= '<div id="custom_fields_' . $field['fieldto'] . '_' . $field['id'] . '_popover-content" class="hide cfh-field-popover-template"><div class="form-group">';
                                 $field_template .= '<div class="row"><div class="col-md-12"><label class="control-label" for="custom_fields_' . $field['fieldto'] . '_' . $field['id'] . '_title">' . _l('cf_translate_input_link_title') . '</label>';
                                 $field_template .= '<input type="text"' . ($field['disalow_client_to_edit'] == 1 && is_client_logged_in() ? ' disabled="true" ' : ' ') . 'id="custom_fields_' . $field['fieldto'] . '_' . $field['id'] . '_title" value="" class="form-control">';
                                 $field_template .= '</div>';
                                 $field_template .= '</div>';
                                 $field_template .= '</div>';
                                 $field_template .= '<div class="form-group">';
                                 $field_template .= '<div class="row">';
                                 $field_template .= '<div class="col-md-12">';
                                 $field_template .= '<label class="control-label" for="custom_fields_' . $field['fieldto'] . '_' . $field['id'] . '_link">' . _l('cf_translate_input_link_url') . '</label>';
                                 $field_template .= '<div class="input-group"><input type="text"' . ($field['disalow_client_to_edit'] == 1 && is_client_logged_in() ? ' disabled="true" ' : ' ') . 'id="custom_fields_' . $field['fieldto'] . '_' . $field['id'] . '_link" value="" class="form-control"><span class="input-group-addon"><a href="#" id="cf_hyperlink_open_' . $field['id'] . '" target="_blank"><i class="fa fa-globe"></i></a></span></div>';
                                 $field_template .= '</div>';
                                 $field_template .= '</div>';
                                 $field_template .= '</div>';
                                 $field_template .= '<div class="row">';
                                 $field_template .= '<div class="col-md-6">';
                                 $field_template .= '<button type="button" id="custom_fields_' . $field['fieldto'] . '_' . $field['id'] . '_btn-cancel" class="btn btn-default btn-md pull-left" value="">' . _l('cancel') . '</button>';
                                 $field_template .= '</div>';
                                 $field_template .= '<div class="col-md-6">';
                                 $field_template .= '<button type="button" id="custom_fields_' . $field['fieldto'] . '_' . $field['id'] . '_btn-save" class="btn btn-info btn-md pull-right" value="">' . _l('apply') . '</button>';
                                 $field_template .= '</div>';
                                 $field_template .= '</div>';
                                 $fields_html .= '<script>';
                                 $fields_html .= 'cfh_popover_templates[\'' . $field['id'] . '\'] = \'' . $field_template . '\';';
                                 $fields_html .= '</script>';
                                 $fields_html .= '</div>';
                                 echo $fields_html;
                                 ?>
                              </div>
                           <?php
                              break;
                           case 'location':
                           ?>
                              <div class="input-group">
                                 <?php echo render_location_picker($field['slug'], $field['name'], get_custom_field_value($project->id, $field['id'], 'projects')) ?>
                              </div>
                        <?php
                              break;
                        }
                        ?>
                        <br>
                        <?php 
                        $data_val_type ='';
                        if($field['type'] ='checkbox'){
                           $data_val_type ='data-val-type="multi-checkbox"'; 
                        }
                        ?>
                        <button class="btn btn-info pull-right data_edit_btn_custom" data-val="<?php echo $field['slug']; ?>" <?php echo $data_val_type; ?>>Save Changes</button>
                        <a class="btn pull-right mright5 close-dropdown">Cancel</a>
                        </form>
                     </div>
                  </div>
               </div>
            <?php } } ?>
         </div>
      </div>
   <?php endforeach; ?>
<?php endif; ?>


<div class="modal fade" id="add-edit-members" tabindex="-1" role="dialog">
   <div class="modal-dialog">
      <?php echo form_open(admin_url('projects/add_edit_members/' . $project->id)); ?>
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title"><?php echo _l('project_members'); ?></h4>
         </div>
         <div class="modal-body">
            <?php
            $selected = array();
            foreach ($members as $member) {
               array_push($selected, $member['staff_id']);
            }
            echo render_select('project_members[]', $staff, array('staffid', array('firstname', 'lastname')), 'project_members', $selected, array('multiple' => true, 'data-actions-box' => true), array(), '', '', false);
            ?>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
            <button type="submit" class="btn btn-info" autocomplete="off" data-loading-text="<?php echo _l('wait_text'); ?>"><?php echo _l('submit'); ?></button>
         </div>
      </div>
      <!-- /.modal-content -->
      <?php echo form_close(); ?>
   </div>
   <!-- /.modal-dialog -->
</div>
<!-- /.modal -->

<div class="modal fade" id="edit_description" tabindex="-1" role="dialog">
   <div class="modal-dialog">
      <?php echo form_open(admin_url('projects/edit_description/' . $project->id)); ?>
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title"><?php echo _l('project_description'); ?></h4>
         </div>
         <div class="modal-body">
            <label for="description" class="control-label">Description</label>
            <textarea id="description_new" name="description" class="form-control tinymce" rows="5"><?php echo $project->description; ?></textarea>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
            <button type="submit" class="btn btn-info" autocomplete="off" data-loading-text="<?php echo _l('wait_text'); ?>"><?php echo _l('submit'); ?></button>
         </div>
      </div>
      <!-- /.modal-content -->
      <?php echo form_close(); ?>
   </div>
   <!-- /.modal-dialog -->
</div>
<!-- /.modal -->

<div class="modal fade" id="add-edit-contacts" tabindex="-2" role="dialog">
   <div class="modal-dialog">
      <?php echo form_open(admin_url('projects/add_edit_contacts/' . $project->id)); ?>
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title"><?php echo _l('project_contacts'); ?></h4>
         </div>
         <div class="modal-body">
            <div class="contactsdiv">
               <?php
               $selected = array();
               foreach ($contacts as $contact) {
                  array_push($selected, $contact['contacts_id']);
               }
               echo render_select('project_contacts[]', $client_contacts, array('id', array('firstname', 'lastname')), 'project_contacts', $selected, array('multiple' => true, 'data-actions-box' => true), array(), '', '', false);
               ?>
            </div>
            <div class='row primarydiv'>
               <div class="col-md-12">
                  <div class="form-group select-placeholder">
                     <label for="status"><?php echo _l('project_primary_contacts'); ?></label>
                     <div class="clearfix"></div>
                     <select name="primary_contact" id="primary_contact" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                        <option></option>
                        <?php
                        foreach ($client_contacts as $cckey => $ccval) {
                           foreach ($contacts as $scckey => $sccval) {
                              if ($sccval['contacts_id'] == $ccval['id']) {
                                 $selected = '';
                                 if ($sccval['is_primary'] == 1) {
                                    $selected = 'selected';
                                 }
                                 echo '<option value="' . $ccval['id'] . '" ' . $selected . ' >' . $ccval['firstname'] . ' ' . $ccval['lastname'] . '</option>';
                              }
                           }
                        }
                        ?>
                        <?php //foreach($statuses as $status){ 
                        ?>
                        <!-- <option value="<?php echo $status['id']; ?>"
                                <?php if (!isset($project) && $status['id'] == 2 || (isset($project) && $project->status == $status['id'])) {
                                    echo 'selected';
                                 } ?>>
                                <?php echo $status['name']; ?></option> -->
                        <?php //} 
                        ?>
                     </select>
                  </div>
               </div>
            </div>

         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
            <button type="submit" class="btn btn-info" autocomplete="off"><?php echo _l('submit'); ?></button>
         </div>
      </div>
      <!-- /.modal-content -->
      <?php echo form_close(); ?>
   </div>
</div>
<!-- /.modal-dialog -->
<div class="modal fade" id="project_contacts_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
   <div class="modal-dialog" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <button group="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="myModalLabel">
               <span class="edit-title"><?php echo _l('add_new', _l('contact')); ?></span>
            </h4>
         </div>
         <?php echo form_open('admin/clients/form_contact/undefined', array('id' => 'project_contacts_add')); ?>
         <div class="modal-body">
            <div class="row">
               <div class="col-md-12">
                  <?php echo form_hidden('clientid', ''); ?>
                  <?php echo form_hidden('project_id', $project->id); ?>
                  <?php $attrs = array('autofocus' => true, 'required' => true); ?>
                  <?php echo render_input('firstname', 'client_firstname', '', '', $attrs); ?>
                  <div id="contact_exists_info" class="hide"></div>
                  <?php echo render_input('title', 'contact_position', ''); ?>
                  <?php echo render_input('email', 'client_email', '', 'email'); ?>
                  <?php echo render_input('phonenumber', 'client_phonenumber', '', 'text', array('autocomplete' => 'off')); ?>

               </div>
            </div>
         </div>
         <div class="modal-footer">
            <button group="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
            <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>

         </div>
         <?php echo form_close(); ?>
      </div>
   </div>
</div>



<div class="modal fade" id="clientid_add_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
   <div class="modal-dialog" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <button group="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="myModalLabel">
               <span class="edit-title"><?php echo _l('add_new', _l('proposal_for_customer')); ?></span>
            </h4>
         </div>
         <?php echo form_open('admin/clients/ajax_client', array('id' => 'clientid_add_group_modal')); ?>
         <div class="modal-body">
            <div class="row">
               <div class="col-md-12">
                  <?php $attrs = array('autofocus' => true, 'required' => true); ?>
                  <?php echo render_input('company', 'client_company', '', 'text', $attrs); ?>
                  <div id="companyname_exists_info" class="hide"></div>
               </div>
            </div>
         </div>
         <div class="modal-footer">
            <button group="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
            <button group="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>

         </div>
         <?php echo form_close(); ?>
      </div>
   </div>
</div>

<div class="modal" id="approveReasonModel" style="display: none;">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-body">
            <div class="form-group">
               <label for="approveremark" class="control-label"><?php echo _l('remarks') ?></label>
               <textarea id="approveremark" name="approveremark" class="form-control" rows="8"></textarea>
            </div>
            <button type="button" class="btn btn-success" id="approveReason">Approve</button>
         </div>
      </div>
   </div>
</div>

<div class="modal" id="rejectReasonModel" style="display: none;">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-body">
            <?php
            $tm = array("id" => "", "name" => "Nothing Selected");
            array_unshift($all_dealrejectionreasons, $tm);
            echo render_select('dealrejectionreason_id', $all_dealrejectionreasons, array('id', 'name'), 'deal_reject_reason', '', array('required' => 'required')); ?>

            <div class="form-group">
               <label for="rejectremark" class="control-label"><small class="req text-danger">* </small><?php echo _l('remarks') ?></label>
               <textarea id="rejectremark" name="rejectremark" class="form-control" rows="8"></textarea>
            </div>
            <button type="button" class="btn btn-danger" id="rejectReason">Reject</button>
         </div>
      </div>
   </div>
</div>

<script>
   document.addEventListener("DOMContentLoaded", function(event) {
      $('.deal-approve').click(function() {
         if (confirm("Do you want to approve this?")) {
            $('#approveReasonModel').modal('show');
         }
      });

      $('.deal-reject').click(function() {
         if (confirm("Do you want to reject this?")) {
            $('#rejectReasonModel').modal('show');
         }
      });

      $('#rejectReason').click(function() {
         var remarks = $('#rejectremark').val();
         var reason = $('#dealrejectionreason_id').val();
         $.ajax({
            url: '<?php echo admin_url('projects/approve/' . $project->id) ?>',
            type: "post",
            dataType: "json",
            data: {
               remarks: remarks,
               status: 0,
               reason: reason
            },
            success: function(response) {
               if (response.success == true) {
                  alert_float('success', response.msg);
                  setTimeout(function() {
                     window.location.reload();
                  }, 1000);
               } else {
                  alert_float('danger', response.msg);
               }
            },
         });
      });

      $('#approveReason').click(function() {
         var remarks = $('#approveremark').val();
         $.ajax({
            url: '<?php echo admin_url('projects/approve/' . $project->id) ?>',
            type: "post",
            dataType: "json",
            data: {
               remarks: remarks,
               status: 1,
               reason: 0
            },
            success: function(response) {
               if (response.success == true) {
                  alert_float('success', response.msg);
                  setTimeout(function() {
                     if (typeof response.redirect != 'undefined') {
                        window.location = response.redirect;
                     } else {
                        window.location.reload();
                     }

                  }, 1000);
               } else {
                  alert_float('danger', response.msg);
               }
            },
         });
      });

   });
</script>