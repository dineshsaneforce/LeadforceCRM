<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php
$can_access_this_deal = false;
if (is_admin(get_staff_user_id()) || $project->teamleader == get_staff_user_id() || in_array(get_staff_user_id(), $ownerHierarchy) || (!empty($my_staffids) && in_array($project->teamleader, $my_staffids) && !in_array($project->teamleader, $viewIds))) {
   $can_access_this_deal = true;
}

if(!$can_access_this_deal && $members){
    foreach($members as $member){
        if($member['staff_id'] ==get_staff_user_id() || in_array($member['staff_id'], $ownerHierarchy)||((!empty($my_staffids) && in_array($member['staff_id'], $my_staffids) && !in_array($member['staff_id'], $viewIds)))){
            $can_access_this_deal =true;
            break;
        }
    }
}

if($can_access_this_deal ==false && $project->approved ==0){
    $this->db->where('staffid',get_staff_user_id());
    $check_staff_role = $this->db->get(db_prefix().'staff')->row();
    if($check_staff_role->role ==1 || $check_staff_role->role ==2){
        $can_access_this_deal = true;
    }

}

if($can_access_this_deal ==false){ ?>
    <?php init_head(); ?>
    <div id="wrapper">
      <div class="content">
      <div class="container">
         <div class="text-center" style="height:100%">
            <div style="margin:10vh 0px">
                  <i class="fa fa-lock  fa-fw fa-lg" style="font-size:100px;"></i>
                  <h3>You dont have access to this deal.</h3>
                  <a class="btn btn-info" href="<?php echo admin_url('projects') ?>">Go to Deals</a>
            </div>
         </div>
      </div>
      </div>
   </div>
    <?php init_tail(); ?>
    </body>
    </html>
<?php 
    exit();
}

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

if (!has_permission('projects', '', 'edit')) {
    $can_user_edit = false;
}

$hasHIstory = $this->approval_model->hasHistory('projects', $project->id) ? true : false;
$hasApprovalFlow = $this->workflow_model->getflows('deal_approval', 0, ['service' => 'approval_level','inactive'=>0]);
?>
<?php init_head(); ?>
<link rel="stylesheet" href="https://cdn3.devexpress.com/jslib/19.1.5/css/dx.common.css">
<link rel="stylesheet" href="https://cdn3.devexpress.com/jslib/19.1.5/css/dx.light.css">
<style>
    .hover-show .hover-edit {
        margin: 0;
        display: none;
    }

    .hover-show:hover .hover-edit {
        display: inline-block;
    }

    #projectOverviewWrapper {
        height: calc(100vh - 220px);
        overflow-y: auto;
        margin-bottom: 0px;
    }

    #projectTabContentWrapper {
        height: calc(100vh - 305px);
        overflow-y: auto;
        margin-bottom: 0px;
    }

    .deal-field-update-dropdown{
        display: none;
        min-width: 260px;
        margin: 0;
        margin-top: 0px;
        margin-top: 5px;
        padding: 0;
        border-radius: 6px;
        z-index: 9000;
        -webkit-box-shadow: 1px 2px 3px rgba(0,0,0,.125);
        box-shadow: 1px 2px 3px rgba(0,0,0,.125);
        border-color: #bfcbd9;
        right: auto;
        left: 0;
        position: absolute;
        top: 100%;
        float: left;
        font-size: 14px;
        text-align: left;
        list-style: none;
        background-color: #fff;
        background-clip: padding-box;
        border: 1px solid #ccc;
        border: 1px solid rgba(0,0,0,.15);
    }
    .deal-field-update-dropdown .input-group{
        width: 100%;
    }

    .data_display_btn{
        color: var(--theme-info-dark);
    }
    .clientiddiv .dropdown-menu{
        bottom: auto;
    }
</style>
<div id="wrapper">
    <?php echo form_hidden('project_id', $project->id) ?>
    <div class="content">
        <div id="overlay" class="overlay" style="display:none">
            <div class="spinner"></div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s project-top-panel panel-full">
                    <div class="panel-body _buttons">
                        <div class="row">
                            <div class="col-md-7 project-heading">
                                <h3 class="hide project-name"><?php echo $project->name; ?></h3>
                                <?php
                                if ($productscnt > 0) {
                                    if ($productscnt == 1)
                                        $projectcnt = ' - <a data-toggle="modal" data-target="#dealproduct_Modal">' . $productscnt . ' Items</a>';
                                    else
                                        $projectcnt = ' - <a data-toggle="modal" data-target="#dealproduct_Modal">' . $productscnt . ' Items</a>';
                                } else {
                                    $projectcnt = ' - <a data-toggle="modal" data-target="#dealproduct_Modal">0 item</a>';
                                }
                                if ($can_user_edit == false) {
                                    $projectcnt = ' - ' . $productscnt . ' Items';
                                }
                                ?>
                                <div class="" style="display:flex;">
                                    <div class="data_display dropdown">
                                        <span class="h4 updated_text">
                                            <?php echo $project->name; ?>
                                        </span>
                                        <?php if ($can_user_edit == true) { ?>
                                            <a href="#" class="data_display_btn">
                                                <i class="fa fa-pencil"></i>
                                            </a>
                                            <div class="deal-field-update-dropdown" >
                                                <div class="panel_s no-mbot">
                                                    <div class="panel-body">
                                                        <form class="data-edit-form">
                                                        <p class="text-muted"><small class="req text-danger">* </small>Update Deal Name</p>
                                                        <div class="input-group date">
                                                            <input required type="text" id="name" name="name" class="form-control" value="<?php echo (isset($project) ? $project->name : 'Deal '); ?>" autocomplete="off" aria-invalid="false">
                                                        </div>
                                                        <div id="company_exists_info" class="hide"></div>
                                                        <br>
                                                        <button class="btn btn-info pull-right data_edit_btn" data-val="name">Save Changes</button>
                                                        <a  class="btn pull-right mright5 close-dropdown">Cancel</a>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>

                                    <?php if (!empty($deal_need_fields) && in_array("project_cost", $deal_need_fields)) : ?>
                                        <div class="data_display mleft15 dropdown" style="display: flex;" >
                                            <span class="h4 updated_text" style="margin:0 5px 0 0">
                                                <?php echo app_format_money($project->project_cost, $currency); ?>
                                            </span>
                                            <?php if ($can_user_edit == true) { ?>
                                                <a href="#" class="data_display_btn">
                                                    <i class="fa fa-pencil"></i>
                                                </a>
                                                <div class="deal-field-update-dropdown" >
                                                    <div class="panel_s no-mbot">
                                                        <div class="panel-body">
                                                            <form class="data-edit-form">
                                                            <p class="text-muted"><?php echo in_array('project_cost',$mandatory_fields)?'<small class="req text-danger">* </small>':''  ?>Update Deal Value</p>
                                                            <div class="input-group date">
                                                                <input type="text" id="project_cost" name="project_cost" class="form-control" value="<?php echo (isset($project) ? $project->project_cost : ''); ?>" autocomplete="off" aria-invalid="false">
                                                            </div>
                                                            <br>
                                                            <button class="btn btn-info pull-right data_edit_btn" data-val="project_cost">Save Changes</button>
                                                            <a  class="btn pull-right mright5 close-dropdown">Cancel</a>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div style="display:flex;">
                                    <div class="data_display mright5 dropdown">
                                        <span class="h5 text-muted">
                                            <i class="fa fa-user mright5" aria-hidden="true"></i><?php echo (isset($teamleader) && isset($teamleader->firstname)) ? ($teamleader->firstname . ' ' . $teamleader->lastname) : ''; ?>
                                        </span>
                                        <?php if ($can_user_edit == true) { ?>
                                            <a href="#" class="data_display_btn">
                                                <i class="fa fa-pencil"></i>
                                            </a>
                                            <div class="deal-field-update-dropdown">
                                                <div class="panel_s no-mbot">
                                                    <div class="panel-body">
                                                        <form class="data-edit-form">
                                                        <p class="text-muted"><small class="req text-danger">* </small>Update Deal Owner</p>
                                                        <div class="select-placeholder form-group-select-input-groups_in[] input-group-select">
                                                            <div style="width: 100%;" class="input-group input-group-select select-groups_in[]">
                                                                <select id="teamleader" name="teamleader" data-live-search="true" data-width="100%" class=" selectpicker _select_input_group">
                                                                    <?php
                                                                    if($teamleaders){
                                                                        foreach ($teamleaders as $pikay => $pival) {
                                                                            $selected = '';
                                                                            $teamleader = (isset($project) ? $project->teamleader : '');
                                                                            if ($teamleader == $pival['staffid']) {
                                                                                $selected = 'selected="selected"';
                                                                            }
                                                                            echo '<option value="' . $pival['staffid'] . '" ' . $selected . '>' . $pival['firstname'] . ' ' . $pival['lastname'] . '</option>';
                                                                        }
                                                                    }
                                                                    ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <br>
                                                        <button class="btn btn-info pull-right data_edit_btn" data-val="teamleader">Save Changes</button>
                                                        <a  class="btn pull-right mright5 close-dropdown">Cancel</a>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                    <?php if (!empty($deal_need_fields) && in_array("pipeline_id", $deal_need_fields)) : ?>
                                        <div class="data_display">
                                            <span class="text-muted h5">
                                                |<i class="fa fa-bar-chart mleft5 mright5 fa-flip-vertical fa-rotate-180" aria-hidden="true"></i><?php echo (isset($pipeline) && isset($pipeline->name)) ? $pipeline->name : ''; ?>
                                            </span>
                                            <?php if ($can_user_edit == true) { ?>
                                                <a onclick="changeStage()" class="data_display_btn"><i class="fa fa-pencil"></i></a>
                                            <?php } ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="visible-xs">
                                    <div class="clearfix"></div>
                                </div>

                            </div>
                            <div class="col-md-5 text-right">

                                <?php
                                if (is_admin(get_staff_user_id()) || $project->teamleader == get_staff_user_id() || in_array(get_staff_user_id(), $ownerHierarchy) || (!empty($my_staffids) && in_array($project->teamleader, $my_staffids) && !in_array($project->teamleader, $viewIds))) { ?>
                                    <div class="btn-group">
                                        <?php if ($hasApprovalFlow && !$hasHIstory && $project->approved == 1 && $project->stage_of == 0) : ?>
                                            <a href="<?php echo admin_url('projects/sendtoapproval/' . $project->id); ?>" style="" class="btn btn-info"><?php echo _l('send_to_approval'); ?></a>
                                        <?php endif; ?>
                                        <?php if ($deal_rejected && (is_admin(get_staff_user_id()) ||get_staff_user_id() == $project->teamleader)) { ?>
                                            <a href="<?php echo admin_url('projects/approvalReopen/' . $project->id); ?>" style="" class="btn btn-info"><?php echo _l('approval_reopen'); ?></a>
                                        <?php } ?>
                                        <?php if ($project->deleted_status == 1 && $project->approved == 1) { ?>
                                            <a href="<?php echo admin_url('projects/restore_project/' . $project->id); ?>" style="" class="btn btn-info"><?php echo _l('restore'); ?></a>
                                        <?php } else { ?>
                                            <?php if ($project->stage_of == 0 && $project->approved == 1) { ?>
                                                <?php if (!$hasApprovalFlow || $hasHIstory) : ?>
                                                    <button type="button" class="btn btn-success" onclick="ch_deal_s_to('1')">
                                                        <?php echo _l('project-status-won'); ?>
                                                    </button>
                                                    <button type="button" class="btn btn-danger" onclick="ch_deal_s_to('2')">
                                                        <?php echo _l('project-status-loss'); ?>
                                                    </button>
                                                <?php endif; ?>
                                            <?php } elseif ($project->approved == 1) { ?>
                                                <span class="btn ">
                                                    <span style="margin: 5px ; font-weight:bold;" class="label label-<?php echo ($project->stage_of == 1) ? 'success' : 'danger'; ?>"><?php echo _l('project-status-' . $project->stage_of); ?></span>
                                                </span>
                                                <button type="button" class="btn btn-default" onclick="ch_deal_s_to(0)">
                                                    <?php echo _l('project-status-reopen'); ?>
                                                </button>
                                        <?php }
                                        } ?>
                                    </div>
                                <?php } ?>
                                <?php /* if(has_permission('tasks','','create')){ ?>
                        <a href="#" onclick="new_task_from_relation(undefined,'project',<?php echo $project->id; ?>); return false;" class="btn btn-info"><?php echo _l('new_task'); ?></a>
                        <?php } */ ?>
                                <?php
                                if ($project->deleted_status == 0 && $project->approved == 1) {
                                    $invoice_func = 'pre_invoice_project';
                                ?>
                                    <?php if (has_permission('invoices', '', 'create')) { ?>
                                        <!-- <a href="#" onclick="<?php echo $invoice_func; ?>(<?php echo $project->id; ?>); return false;" class="invoice-project btn btn-info<?php if ($project->client_data->active == 0) {
                                                                                                                                                                                    echo ' disabled';
                                                                                                                                                                                } ?>"><?php echo _l('invoice_project'); ?></a> -->
                                    <?php } ?>
                                    <?php
                                    $project_pin_tooltip = _l('pin_project');
                                    if (total_rows(db_prefix() . 'pinned_projects', array('staff_id' => get_staff_user_id(), 'project_id' => $project->id)) > 0) {
                                        $project_pin_tooltip = _l('unpin_project');
                                    }
                                    ?>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-right width200 project-actions">
                                            <li>
                                                <a href="<?php echo admin_url('projects/pin_action/' . $project->id); ?>">
                                                    <?php echo $project_pin_tooltip; ?>
                                                </a>
                                            </li>
                                            <?php if (has_permission('projects', '', 'edit') && $project->lead_id > 0 && $project->deleted_status == 0) { ?>
                                                <!-- <li>
                                 <a href="<?php echo admin_url('leads/convert_to_lead/' . $project->id); ?>">
                                    Convert to Lead
                                 </a>
                              </li> -->
                                            <?php } ?>
                                            <?php if (has_permission('projects', '', 'create')) { ?>
                                                <li>
                                                    <a href="#" onclick="copy_project(); return false;">
                                                        <?php echo _l('copy_project'); ?>
                                                    </a>
                                                </li>
                                            <?php } ?>
                                            <?php if (has_permission('projects', '', 'create') || has_permission('projects', '', 'edit')) { ?>
                                                <!-- <li class="divider"></li> -->
                                                <?php //foreach($statuses as $status){
                                                //if($status['id'] == $project->status){continue;}
                                                ?>
                                                <!-- <li>
                                 <a href="#" data-name="<?php echo _l('project_status_' . $status['id']); ?>" onclick="project_mark_as_modal(<?php echo $status['id']; ?>,<?php echo $project->id; ?>, this); return false;"><?php echo _l('project_mark_as', $status['name']); ?></a>
                              </li> -->
                                                <?php //} 
                                                ?>
                                            <?php } ?>
                                            <li class="divider"></li>
                                            <?php if (has_permission('projects', '', 'create')) { ?>
                                                <!-- <li>
                                 <a href="<?php echo admin_url('projects/export_project_data/' . $project->id); ?>" target="_blank"><?php echo _l('export_project_data'); ?></a>
                              </li> -->
                                            <?php } ?>
                                            <?php if (is_admin()) { ?>
                                                <!-- <li>
                                 <a href="<?php echo admin_url('projects/view_project_as_client/' . $project->id . '/' . $project->clientid); ?>" target="_blank"><?php echo _l('project_view_as_client'); ?></a>
                              </li> -->
                                            <?php } ?>
                                            <?php if (has_permission('projects', '', 'delete')) {
                                                if (is_admin(get_staff_user_id()) || $project->teamleader == get_staff_user_id() || in_array(get_staff_user_id(), $ownerHierarchy) || (!empty($my_staffids) && in_array($project->teamleader, $my_staffids) && !in_array($project->teamleader, $viewIds))) { ?>
                                                    <li>
                                                        <a href="<?php echo admin_url('projects/delete/' . $project->id); ?>" class="_delete">
                                                            <span class="text-danger"><?php echo _l('delete_project'); ?></span>
                                                        </a>
                                                    </li>
                                            <?php }
                                            } ?>
                                        </ul>
                                    </div>
                                <?php } ?>
                            </div>
                            <?php if (!empty($need_fields) && in_array("status", $need_fields)) : ?>


                                <div class="col-xs-12">
                                    <ul class="arrow-progress">
                                        <?php $activestatus = true; ?>
                                        <?php foreach ($statuses as $status) : ?>
                                            <li data-toggle="tooltip" data-title="<?php echo $status['name']; ?>" class="<?php echo $activestatus ? 'active' : '' ?>" <?php echo ($can_user_edit)?'onclick="changeProjectStatus('.$project->id.','.$status['id'].')"': '' ?>>
                                                <?php if ($project->status == $status['id']) {
                                                    $activestatus = false;
                                                } ?>
                                                <p><?php echo $status['name']; ?></p>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="panel_s" style="margin-bottom: 0px;">
                            <div class="panel-body" id="projectOverviewWrapper">
                                <?php echo $this->load->view('admin/projects/deal_overview'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <?php if (true || $project->approved == 1) : ?>
                            <div class="panel_s project-menu-panel">
                                <div class="panel-body">
                                    <?php hooks()->do_action('before_render_project_view', $project->id); ?>
                                    <?php $this->load->view('admin/projects/project_tabs'); ?>
                                </div>
                            </div>
                        <?php endif; ?>
                        <?php
                        if ((has_permission('projects', '', 'create') || has_permission('projects', '', 'edit'))
                            && $project->status == 1
                            && $this->projects_model->timers_started_for_project($project->id)
                            && $tab['slug'] != 'project_milestones'
                        ) {
                        ?>
                            <div class="alert alert-warning project-no-started-timers-found mbot15">
                                <?php echo _l('project_not_started_status_tasks_timers_found'); ?>
                            </div>
                        <?php } ?>
                        <?php
                        if (
                            $project->deadline && date('Y-m-d') > $project->deadline
                            && floor((abs(time() - strtotime($project->deadline))) / (60 * 60 * 24)) >= 9
                        ) {
                        ?>
                            <div class="alert alert-warning bold project-due-notice mbot15">
                                <?php echo _l('project_due_notice', floor((abs(time() - strtotime($project->deadline))) / (60 * 60 * 24))); ?>
                            </div>
                        <?php } ?>
                        <?php /*
                    if(!has_contact_permission('projects',get_primary_contact_user_id($project->clientid))
                        && total_rows(db_prefix().'contacts',array('userid'=>$project->clientid)) > 0
                        && $tab['slug'] != 'project_milestones') {
                    ?>
                    <div class="alert alert-warning project-permissions-warning mbot15">
                    <?php echo _l('project_customer_permission_warning'); ?>
                    </div>
                    <?php } */ ?>
                        <div class="panel_s" style="margin-bottom: 0px;">
                            <div class="panel-body" id="projectTabContentWrapper">
                                <?php echo $this->load->view(($tab['view'] ? $tab['view'] : 'admin/projects/project_tasks')); ?>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
</div>
</div>
<?php
if (isset($discussion)) {
    echo form_hidden('discussion_id', $discussion->id);
    echo form_hidden('discussion_user_profile_image_url', $discussion_user_profile_image_url);
    echo form_hidden('current_user_is_admin', $current_user_is_admin);
}
echo form_hidden('project_percent', $percent);
?>
<div id="invoice_project"></div>
<div id="pre_invoice_project"></div>
<?php $this->load->view('admin/projects/milestone'); ?>
<?php $this->load->view('admin/projects/copy_settings'); ?>
<?php $this->load->view('admin/projects/_mark_tasks_finished'); ?>

<!-- Modal -->
<div class="modal fade" id="deallossreasons_Modal" tabindex="-1" role="dialog" aria-labelledby="deallossreasons_ModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="DealLossReasons_form" method='post' name="DealLossReasons_form">
                <div class="modal-header">
                    <h5 class="modal-title" id="deallossreasons_ModalLabel"><?php echo _l('DealLossReasons'); ?></h5>
                </div>
                <div class="modal-body">
                    <?php
                    $tm = array("id" => "", "name" => "Nothing Selected");
                    array_unshift($all_deallossreasons, $tm);
                    echo render_select('deallossreasons_id', $all_deallossreasons, array('id', 'name'), 'DealLossReasons', '', array('required' => 'required')); ?>
                    <?php echo render_textarea('lossremark', 'Remark', ''); ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
                <?php echo form_close(); ?>
        </div>
    </div>
</div>

<div class="modal fade" id="changeStage" tabindex="-1" role="dialog" aria-labelledby="changeStage_ModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="changeStageForm" method='post'>
                <div class="modal-header">
                    <h5 class="modal-title" id="changeStage_ModalLabel">Change Pipeline and Stage</h5>
                </div>
                <div class="modal-body">
                    <div class="col-md-12">
                        <div class="form-group select-placeholder pipeselect">
                            <label for="status">Pipeline</label>
                            <div class="input-group">
                                <select id="pipeli_id" name="pipeline_id" data-live-search="true" class=" selectpicker">
                                    <?php
                                    if($pipelines){
                                        foreach ($pipelines as $pikay => $pival) {
                                            $selected = '';
                                            $pipeline_id = (isset($project) ? $project->pipeline_id : '');
                                            if ($pipeline_id == $pival['id']) {
                                                $selected = 'selected="selected"';
                                            }
                                            echo '<option value="' . $pival['id'] . '" ' . $selected . '>' . $pival['name'] . '</option>';
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group select-placeholder formstage">
                            <label for="status">Stage</label>
                            <div class="input-group">
                                <select id="stage_id" name="stage_id" data-live-search="true" class="selectpicker" required>
                                    <option></option>
                                    <?php
                                    if($pipestage){
                                        foreach ($pipestage as $pikay => $pival) {
                                            $selected = '';
                                            $stage_id = (isset($project) ? $project->status : '');
                                            if ($stage_id == $pival['id']) {
                                                $selected = 'selected="selected"';
                                            }
                                            echo '<option value="' . $pival['id'] . '" ' . $selected . '>' . $pival['name'] . '</option>';
                                        }
                                    }
                                    
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer" style="display:flow-root">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary savePipelineStage">Save changes</button>
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
            <?php echo form_open('admin/clients/ajax_client', array('id' => 'clientid_add_group_modal1')); ?>
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
<div class="modal fade" id="project_contacts_modal1" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button group="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <span class="edit-title"><?php echo _l('add_new', _l('contact')); ?></span>
                </h4>
            </div>
            <?php echo form_open('admin/clients/form_contact/undefined', array('id' => 'project_contacts_add1')); ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <?php echo form_hidden('clientid', ''); ?>
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
                <button group="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>

            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>
<div class="modal fade" id="play_record" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" style="width:340px;">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">
                    <span class="edit-title">Play Recorded</span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12 text-center">
                        <div id="playhtml">

                        </div>
                    </div>

                </div>
            </div>
            <div class="modal-footer">
                <button group="button" id="closeaudio" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="view_history" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button group="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <span class="edit-title">Call History</span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div id="historyhtml">

                        </div>
                    </div>

                </div>
            </div>
            <div class="modal-footer">
                <button group="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
            </div>
        </div>
    </div>
</div>
<?php /*
<!-- jQuery library -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

<!-- Latest compiled JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>*/ ?>
<script>

</script>
<?php init_tail(); ?>
<!-- For invoices table -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/quill/1.3.6/quill.min.js"></script>
<script src="https://cdn3.devexpress.com/jslib/19.1.5/js/dx.all.js"></script>
<script>
    $(function() {
        appValidateForm($('#DealLossReasons_form'), {
            deallossreasons_id: 'required'
        });
    });
    <?php if ($_REQUEST['group'] == 'project_overview') { ?>
        tinymce.init({
            selector: 'textarea#description_new',
            height: 100,
            menubar: false,
            plugins: [
                'advlist autolink lists charmap print preview anchor',
                'searchreplace visualblocks code fullscreen',
                'insertdatetime media table paste code help wordcount'
            ],
            toolbar: 'undo redo | formatselect | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat'
        });
    <?php } ?>
</script>
<?php if ($_REQUEST['group'] == '' || $_REQUEST['group'] == 'project_tasks') { ?>
    <style>
        .check_text {
            color: #fff !important
        }

        th.sorting {
            white-space: nowrap
        }

        .single_linet {
            white-space: nowrap
        }
    </style>
<?php } ?>
<script>
    taskid = '<?php echo $this->input->get('taskid'); ?>';
</script>
<script>
    var gantt_data = {};
    <?php if (isset($gantt_data)) { ?>
        gantt_data = <?php echo json_encode($gantt_data); ?>;
    <?php } ?>
    var discussion_id = $('input[name="discussion_id"]').val();
    var discussion_user_profile_image_url = $('input[name="discussion_user_profile_image_url"]').val();
    var current_user_is_admin = $('input[name="current_user_is_admin"]').val();
    var project_id = $('input[name="project_id"]').val();
    if (typeof(discussion_id) != 'undefined') {
        discussion_comments('#discussion-comments', discussion_id, 'regular');
    }
    $(function() {
        var project_progress_color = '<?php echo hooks()->apply_filters('admin_project_progress_color', '#84c529'); ?>';
        var circle = $('.project-progress').circleProgress({
            fill: {
                gradient: [project_progress_color, project_progress_color]
            }
        }).on('circle-animation-progress', function(event, progress, stepValue) {
            $(this).find('strong.project-percent').html(parseInt(100 * stepValue) + '<i>%</i>');
        });
    });

    function discussion_comments(selector, discussion_id, discussion_type) {
        var defaults = _get_jquery_comments_default_config(<?php echo json_encode(get_project_discussions_language_array()); ?>);
        var options = {
            currentUserIsAdmin: current_user_is_admin,
            getComments: function(success, error) {
                $.get(admin_url + 'projects/get_discussion_comments/' + discussion_id + '/' + discussion_type, function(response) {
                    success(response);
                }, 'json');
            },
            postComment: function(commentJSON, success, error) {
                $.ajax({
                    type: 'post',
                    url: admin_url + 'projects/add_discussion_comment/' + discussion_id + '/' + discussion_type,
                    data: commentJSON,
                    success: function(comment) {
                        comment = JSON.parse(comment);
                        success(comment)
                    },
                    error: error
                });
            },
            putComment: function(commentJSON, success, error) {
                $.ajax({
                    type: 'post',
                    url: admin_url + 'projects/update_discussion_comment',
                    data: commentJSON,
                    success: function(comment) {
                        comment = JSON.parse(comment);
                        success(comment)
                    },
                    error: error
                });
            },
            deleteComment: function(commentJSON, success, error) {
                $.ajax({
                    type: 'post',
                    url: admin_url + 'projects/delete_discussion_comment/' + commentJSON.id,
                    success: success,
                    error: error
                });
            },
            uploadAttachments: function(commentArray, success, error) {
                var responses = 0;
                var successfulUploads = [];
                var serverResponded = function() {
                    responses++;
                    // Check if all requests have finished
                    if (responses == commentArray.length) {
                        // Case: all failed
                        if (successfulUploads.length == 0) {
                            error();
                            // Case: some succeeded
                        } else {
                            successfulUploads = JSON.parse(successfulUploads);
                            success(successfulUploads)
                        }
                    }
                }
                $(commentArray).each(function(index, commentJSON) {
                    // Create form data
                    var formData = new FormData();
                    if (commentJSON.file.size && commentJSON.file.size > app.max_php_ini_upload_size_bytes) {
                        alert_float('danger', "<?php echo _l("file_exceeds_max_filesize"); ?>");
                        serverResponded();
                    } else {
                        $(Object.keys(commentJSON)).each(function(index, key) {
                            var value = commentJSON[key];
                            if (value) formData.append(key, value);
                        });

                        if (typeof(csrfData) !== 'undefined') {
                            formData.append(csrfData['token_name'], csrfData['hash']);
                        }
                        $.ajax({
                            url: admin_url + 'projects/add_discussion_comment/' + discussion_id + '/' + discussion_type,
                            type: 'POST',
                            data: formData,
                            cache: false,
                            contentType: false,
                            processData: false,
                            success: function(commentJSON) {
                                successfulUploads.push(commentJSON);
                                serverResponded();
                            },
                            error: function(data) {
                                var error = JSON.parse(data.responseText);
                                alert_float('danger', error.message);
                                serverResponded();
                            },
                        });
                    }
                });
            }
        }
        var settings = $.extend({}, defaults, options);
        $(selector).comments(settings);
    }

    $("#DealLossReasons_form").submit(function(e) {
        e.preventDefault();
        ch_deal_s_to(2)
        return false;
    });



    function ch_deal_s_to(status) {

        var data = {
            project_id: <?php echo ($project->id); ?>,
            status_id: status
        };
        if (status == 2) {
            data.loss_reason = $('#deallossreasons_id').val();
            data.loss_remark = $('#lossremark').val();
            if (data.loss_reason == '' || data.loss_reason == 'undefined') {
                $('#deallossreasons_Modal').modal('show');
                return false;
            } else {
                $('#deallossreasons_Modal').modal('hide');
            }
        }
        $.ajax({
            type: 'POST',
            url: admin_url + 'projects/mark_as_won_loss_reopen',
            data: data,
            dataType: 'json',
            success: function(msg) {
                alert_float('success', msg.message);
                location.reload();
            }
        });
    }
</script>
<?php if ($_REQUEST['group'] == '' || $_REQUEST['group'] == 'project_tasks') { ?>
    <script>
        var originalLeave = $.fn.tooltip.Constructor.prototype.leave;
        $.fn.tooltip.Constructor.prototype.leave = function(obj) {
            var self = obj instanceof this.constructor ?
                obj : $(obj.currentTarget)[this.type](this.getDelegateOptions()).data('bs.' + this.type)
            var container, timeout;

            originalLeave.call(this, obj);

            if (obj.currentTarget) {
                container = $(obj.currentTarget).siblings('.tooltip')
                timeout = self.timeout;
                $('.check_text').click(function(e) {
                    $.fn.tooltip.Constructor.prototype.leave.call(self, self);
                });
                container.one('click', function() {
                    $("[data-toggle='tooltip']").tooltip('hide');
                    $.fn.tooltip.Constructor.prototype.leave.call(self, self);
                    container.one('mouseleave', function() {
                        $.fn.tooltip.Constructor.prototype.leave.call(self, self);

                    });
                });

                container.one('mouseenter', function() {
                    //We entered the actual popover – call off the dogs
                    clearTimeout(timeout);
                    //Let's monitor popover content instead
                    container.one('mouseleave', function() {
                        clearTimeout(timeout);
                        $.fn.tooltip.Constructor.prototype.leave.call(self, self);
                    });
                })
            }
        };


        $('body').tooltip({
            selector: '[data-toggle] , .tooltip',
            trigger: 'click hover',
            placement: 'auto',
            delay: {
                show: 50,
                hide: 400
            }
        });

        function copyToClipboard(element) {

            var str = element.id
            var req_txt = str.split('_');
            var str1 = req_txt[0].toLowerCase().replace(/\b[a-z]/g, function(letter) {
                return letter.toUpperCase();
            });
            var req_element = 'input_' + element.id;
            element = element.id;

            var copyText = document.getElementById(req_element);
            copyText.select();
            navigator.clipboard.writeText(copyText.value);
            alert_float('success', str1 + ' Copied Successfully');
            $("[data-toggle='tooltip']").tooltip('hide');
            setTimeout(function() {
                $("[data-toggle='tooltip']").tooltip('hide');
            }, 500);
            /* Alert the copied text */
        }
    </script>
<?php } ?>
<script>
    $(function() {
        $("#sortable").sortable();
        $("#sortable").disableSelection();
    });
</script>

<script>
    window.addEventListener('load', function() {

        appValidateForm($('#clientid_add_group_modal1'), {
            company: 'required'
        }, manage_customer_groups);

        function manage_customer_groups() {

        }



        $('.savePipelineStage').click(function(e) {

            $(".savePipelineStage").attr("disabled", true);
            var stage = $('#stage_id').val();
            var pipeline = $('#pipeli_id').val();
            if (stage) {
                //   var data = {project_id:<?php echo ($project->id); ?>,f:$('.'+f+' data_edit').val()};
                var data = {
                    project_id: <?php echo ($project->id); ?>,
                    pipeline_id: pipeline,
                    status: stage
                };
                $.ajax({
                    type: 'POST',
                    url: admin_url + 'projects/savepipelineAndstage',
                    data: data,
                    dataType: 'json',
                    success: function(msg) {
                        if (msg.err) {
                            alert_float('warning', msg.err);
                        }
                        if (msg.message) {
                            alert_float('success', msg.message);
                        }
                        location.reload();
                    }
                });
            } else {
                alert('Please Select Stage.');
                return false;
            }
        });
        $('#name').on('keyup', function() {
            var name = $('#name').val();
            var pid = $('#projectid').val();
            var $companyExistsDiv = $('#company_exists_info');
            var data = {
                name: name
            };
            if (pid) {
                data['pid'] = pid;
            }
            $.ajax({
                type: 'POST',
                url: admin_url + 'projects/checkduplicate',
                data: data,
                dataType: 'json',
                success: function(msg) {
                    if (msg.message != 'no') {
                        $companyExistsDiv.removeClass('hide');
                        $companyExistsDiv.html('<div class="info-block mbot15">' + msg.message + '</div>');
                    } else {
                        $companyExistsDiv.addClass('hide');
                    }
                }
            });
        });

        $('#clientid_add_group_modal1').on('show.bs.modal', function(e) {
            var invoker = $(e.relatedTarget);
            var group_id = $(invoker).data('id');
            $('#clientid_add_group_modal1 input[name="company"]').val('');
            // is from the edit button
            if (typeof(group_id) !== 'undefined') {
                $('#clientid_add_group_modal1 input[name="company"]').val($(invoker).parents('tr').find('td')
                    .eq(0).text());
            }
        });

    });

    $('#clientid_add_group_modal1').submit(function(e) {
        e.preventDefault();
        var form = $(this);
        var data = getFormData(form);
        if (data.company) {
            $.ajax({
                type: form.attr('method'),
                url: form.attr('action'),
                data: form.serialize(),
                dataType: 'json',
                success: function(msg) {
                    $('#clientid').append('<option value="' + msg.id + '" selected="selected">' + msg
                        .company + '</option>');
                    $('#clientid').val(msg.id);
                    $('#clientid_add_group_modal1 input[name="company"]').val('');
                    alert_float('success', msg.message);
                    $('.contactsdiv1 select').html('');
                    $('.primarydiv1 select').html('');
                    setTimeout(function() {
                        $('#clientid').selectpicker('refresh');
                        $('.clientiddiv div.filter-option-inner-inner').html(msg.company)
                        $('.contactsdiv1 select').selectpicker('refresh');
                        $('.primarydiv1 select').selectpicker('refresh');
                    }, 500);
                    $('#clientid_add_modal').modal('hide');
                }
            });
        }
    });

    $('#clientid').on('change', function() {
        var clientId = this.value;
        var data = {
            clientId: clientId
        };

        $.ajax({
            type: 'POST',
            url: admin_url + 'projects/getContactpersonList',
            data: data,
            dataType: 'json',
            success: function(msg) {
                $('.contactsdiv1 select').html(msg.success);
                $('.primarydiv1 select').html('');
                setTimeout(function() {
                    $('.contactsdiv1 select').selectpicker('refresh');
                    $('.primarydiv1 select').selectpicker('refresh');
                }, 500);
            }
        });
    });



    window.addEventListener('load', function() {
        appValidateForm($('#project_contacts_add1'), {
            firstname: 'required'
        }, manage_project_contacts_add1);

        function manage_project_contacts_add1(form) {}

        $('#project_contacts_modal1').on('show.bs.modal', function(e) {
            var invoker = $(e.relatedTarget);
            var group_id = $(invoker).data('id');
            $('#project_contacts_add1 input[name="firstname"]').val('');
            $('#project_contacts_add1 input[name="email"]').val('');
            $('#project_contacts_add1 input[name="phonenumber"]').val('');
            // is from the edit button
            if (typeof(group_id) !== 'undefined') {
                $('#project_contacts_add1 input[name="firstname"]').val($(invoker).parents('tr').find('td')
                    .eq(0).text());
            }
        });

    });
    $('#project_contacts_add1').submit(function(e) {
        e.preventDefault();
        var form = $(this);
        var data = getFormData(form);
        data.clientid = $('#clientid').val();
        if (data.firstname) {
            $.ajax({
                type: form.attr('method'),
                url: form.attr('action'),
                data: data,
                dataType: 'json',
                success: function(msg) {
                    //alert(msg.firstname);
                    $('.contactsdiv1 select').html(msg.firstname);
                    $('.primarydiv1 select').html(msg.firstname);
                    alert_float('success', msg.message);
                    setTimeout(function() {
                        $('.contactsdiv1 select').selectpicker('refresh');
                        $('.primarydiv1 select').selectpicker('refresh');
                    }, 500);
                    $('#project_contacts_modal1').modal('hide');
                }
            });
        }
    });

    // $('.s').on("change", function(e) {

    //     var selected=[];
    //  $('#project_contacts :selected').each(function(){
    //      selected[$(this).val()]=$(this).text();
    //     });
    // console.log(selected);

    // });

    $(".contactsdiv1 .selectpicker").change(function() {
        $('#primary_contact1').empty().append('<option value="">Nothing Selected</option>');
        $('#primary_contact1').selectpicker('refresh');
        var option_all = $(".contactsdiv1 .selectpicker option:selected").map(function() {
            $('#primary_contact1').append('<option value="' + $(this).val() + '">' + $(this).text() + '</option>');
        });
        $('#primary_contact1').selectpicker('refresh');
    });

    function getFormData($form) {
        var unindexed_array = $form.serializeArray();
        var indexed_array = {};

        $.map(unindexed_array, function(n, i) {
            indexed_array[n['name']] = n['value'];
        });

        return indexed_array;
    }
</script>


<script>
    <?php
    if (isset($project)) {
    ?>
        var original_project_status = '<?php echo $project->status; ?>';
    <?php
    } ?>
    $(function() {


        $("#start_date1").on("change", function(e) {
            var obj = $("#deadline1");
            obj.datepicker('destroy').attr("data-date-min-date", $(this).val());
            init_datepicker(obj);
        });
        $("#deadline1").on("change", function(e) {
            var obj = $("#start_date1");
            obj.datepicker('destroy').attr("data-date-end-date", $(this).val());
            init_datepicker(obj);
        });


        $('select[name="billing_type"]').on('change', function() {
            var type = $(this).val();
            if (type == 1) {
                $('#project_cost').removeClass('hide');
                $('#project_rate_per_hour').addClass('hide');
            } else if (type == 2) {
                $('#project_cost').addClass('hide');
                $('#project_rate_per_hour').removeClass('hide');
            } else {
                $('#project_cost').addClass('hide');
                $('#project_rate_per_hour').addClass('hide');
            }
        });



        $('select[name="status1"]').on('change', function() {
            var status = $(this).val();
            var mark_all_tasks_completed = $('.mark_all_tasks_as_completed');
            var notify_project_members_status_change = $('.notify_project_members_status_change');
            mark_all_tasks_completed.removeClass('hide');
            if (typeof(original_project_status) != 'undefined') {
                if (original_project_status != status) {

                    mark_all_tasks_completed.removeClass('hide');
                    notify_project_members_status_change.removeClass('hide');

                    if (status == 4 || status == 5 || status == 3) {
                        $('.recurring-tasks-notice').removeClass('hide');
                        var notice = "<?php echo _l('project_changing_status_recurring_tasks_notice'); ?>";
                        notice = notice.replace('{0}', $(this).find('option[value="' + status + '"]').text()
                            .trim());
                        $('.recurring-tasks-notice').html(notice);
                        $('.recurring-tasks-notice').append(
                            '<input type="hidden" name="cancel_recurring_tasks" value="true">');
                        mark_all_tasks_completed.find('input').prop('checked', true);
                    } else {
                        $('.recurring-tasks-notice').html('').addClass('hide');
                        mark_all_tasks_completed.find('input').prop('checked', false);
                    }
                } else {
                    mark_all_tasks_completed.addClass('hide');
                    mark_all_tasks_completed.find('input').prop('checked', false);
                    notify_project_members_status_change.addClass('hide');
                    $('.recurring-tasks-notice').html('').addClass('hide');
                }
            }

            if (status == 4) {
                $('.project_marked_as_finished').removeClass('hide');
            } else {
                $('.project_marked_as_finished').addClass('hide');
                $('.project_marked_as_finished').prop('checked', false);
            }
            $('#status-error').hide();
        });

        $('form').on('submit', function() {
            $('select[name="billing_type"]').prop('disabled', false);
            $('#available_features,#available_features option').prop('disabled', false);
            $('input[name="project_rate_per_hour"]').prop('disabled', false);
        });

        var progress_input = $('input[name="progress"]');
        var progress_from_tasks = $('#progress_from_tasks');
        var progress = progress_input.val();

        $('.project_progress_slider').slider({
            min: 0,
            max: 100,
            value: progress,
            disabled: progress_from_tasks.prop('checked'),
            slide: function(event, ui) {
                progress_input.val(ui.value);
                $('.label_progress').html(ui.value + '%');
            }
        });

        progress_from_tasks.on('change', function() {
            var _checked = $(this).prop('checked');
            $('.project_progress_slider').slider({
                disabled: _checked
            });
        });

        $('#project-settings-area input').on('change', function() {
            if ($(this).attr('id') == 'view_tasks' && $(this).prop('checked') == false) {
                $('#create_tasks').prop('checked', false).prop('disabled', true);
                $('#edit_tasks').prop('checked', false).prop('disabled', true);
                $('#view_task_comments').prop('checked', false).prop('disabled', true);
                $('#comment_on_tasks').prop('checked', false).prop('disabled', true);
                $('#view_task_attachments').prop('checked', false).prop('disabled', true);
                $('#view_task_checklist_items').prop('checked', false).prop('disabled', true);
                $('#upload_on_tasks').prop('checked', false).prop('disabled', true);
                $('#view_task_total_logged_time').prop('checked', false).prop('disabled', true);
            } else if ($(this).attr('id') == 'view_tasks' && $(this).prop('checked') == true) {
                $('#create_tasks').prop('disabled', false);
                $('#edit_tasks').prop('disabled', false);
                $('#view_task_comments').prop('disabled', false);
                $('#comment_on_tasks').prop('disabled', false);
                $('#view_task_attachments').prop('disabled', false);
                $('#view_task_checklist_items').prop('disabled', false);
                $('#upload_on_tasks').prop('disabled', false);
                $('#view_task_total_logged_time').prop('disabled', false);
            }
        });

        // Auto adjust customer permissions based on selected project visible tabs
        // Eq Project creator disable TASKS tab, then this function will auto turn off customer project option Allow customer to view tasks

        $('#available_features').on('change', function() {
            $("#available_features option").each(function() {
                if ($(this).data('linked-customer-option') && !$(this).is(':selected')) {
                    var opts = $(this).data('linked-customer-option').split(',');
                    for (var i = 0; i < opts.length; i++) {
                        var project_option = $('#' + opts[i]);
                        project_option.prop('checked', false);
                        if (opts[i] == 'view_tasks') {
                            project_option.trigger('change');
                        }
                    }
                }
            });
        });
        $("#view_tasks").trigger('change');
        <?php
        if (!isset($project)) {
        ?>
            $('#available_features').trigger('change');
        <?php
        } ?>
    });
</script>


<script>
    function changeStage() {
        if ($('#pipeli_id').length > 0) {
            $('.pipeselect .selectpicker').addClass("formnewpipeline");
        }
        $('.formnewpipeline').selectpicker('destroy');
        $('.formnewpipeline').html('').selectpicker('refresh');

        if ($('#stage_id').length > 0) {
            $('.formstage .selectpicker').addClass("formnewstatus");
        }
        $('.formnewstatus').selectpicker('destroy');
        $('.formnewstatus').html('').selectpicker('refresh');
        var pipeid = <?php echo $project->pipeline_id; ?>;
        var status = <?php echo $project->status; ?>;
        $.ajax({
            url: admin_url + 'pipeline/pickpipelineandstage',
            type: 'POST',
            data: {
                'pipeline_id': pipeid,
                'status': status
            },
            dataType: 'json',
            success: function success(result) {
                $('.formnewpipeline').selectpicker('destroy');
                $('.formnewpipeline').html(result.pipelines).selectpicker('refresh');

                $('.formnewstatus').selectpicker('destroy');
                $('.formnewstatus').html(result.statuses).selectpicker('refresh');
                // $('.formstage').html(result.statuses).selectpicker('refresh');
                $('#changeStage').modal('show');
            }
        });
    }
    $(function() {
        if ($('#stage_id').length > 0) {
            $('.formstage .selectpicker').addClass("formnewstatus");
        }
        if ($('.form_assigned1 .selectpicker').length > 0) {
            $('.form_assigned1 .selectpicker').addClass("formassigned1");
        }

        if ($('#teamleader1').length > 0) {
            $('.form_teamleader1 .selectpicker').addClass("formteamleader");
        }

        $('#pipeli_id').change(function() {
            $('.formnewstatus').selectpicker('destroy');
            $('.formnewstatus').html('').selectpicker('refresh');
            var pipeid = $('#pipeli_id').val();
            $.ajax({
                url: admin_url + 'leads/changepipeline',
                type: 'POST',
                data: {
                    'pipeline_id': pipeid
                },
                dataType: 'json',
                success: function success(result) {
                    $('.formnewstatus').selectpicker('destroy');
                    $('.formnewstatus').html(result.statuses).selectpicker('refresh');
                    // $('.formstage').html(result.statuses).selectpicker('refresh');
                }
            });
        });

        // if ($('#status').length > 0) {
        //     $('.formstatus').selectpicker('destroy');
        //     $('.formstatus').html('').selectpicker('refresh');
        // }

        if ($('#status1').length > 0) {
            $('.form_status .selectpicker').addClass("form_status1");
        }

        $('#pipeid').change(function() {
            $('.formstatus').selectpicker('destroy');
            $('.formstatus').html('').selectpicker('refresh');

            $('.form_status1').selectpicker('destroy');
            $('.form_status1').html('').selectpicker('refresh');

            // $('.formassigned1').selectpicker('destroy');
            // $('.formassigned1').html('').selectpicker('refresh');

            // $('.formteamleader').selectpicker('destroy');
            // $('.formteamleader').html('').selectpicker('refresh');

            var pipeid = $('#pipeid').val();
            $.ajax({
                url: admin_url + 'leads/changepipeline',
                type: 'POST',
                data: {
                    'pipeline_id': pipeid
                },
                dataType: 'json',
                success: function success(result) {
                    $('.formstatus').selectpicker('destroy');
                    $('.formstatus').html(result.statuses).selectpicker('refresh');
                    $('.form_status1').selectpicker('destroy');
                    $('.form_status1').html(result.statuses).selectpicker('refresh');

                }
            });
        });

        $('#teamleader1').change(function() {
            $('.formassigned1').selectpicker('destroy');
            $('.formassigned1').html('').selectpicker('refresh');
            var pipeid = $('#pipeid').val();
            var teamleader = $('#teamleader1').val();
            $.ajax({
                url: admin_url + 'leads/getpipelineteamember',
                type: 'POST',
                data: {
                    'leaderid': teamleader,
                    'pipeline': pipeid
                },
                dataType: 'json',
                success: function success(result) {
                    $('.formassigned1').selectpicker('destroy');
                    $('.formassigned1').html(result.teammembers).selectpicker('refresh');
                    $('#teamleader1-error').hide();
                }
            });
        });
        var pipelines_count = <?php echo count((array)$pipelines); ?>;
        if (pipelines_count == 1) {
            $('#pipeid option[value="<?php echo $pipelines[0]['id']; ?>"]').attr('selected', 'selected')
            $('#pipeid').selectpicker('refresh');
            $('#pipeid').trigger('change');
        }

        $('#company').on('keyup', function() {
            var company = $(this).val();
            var $companyExistsDiv = $('#companyname_exists_info');

            if (company == '') {
                $companyExistsDiv.addClass('hide');
                return;
            }

            $.post(admin_url + 'clients/check_duplicate_customer_name', {
                    company: company
                })
                .done(function(response) {
                    if (response) {
                        response = JSON.parse(response);
                        if (response.exists == true) {
                            $companyExistsDiv.removeClass('hide');
                            $companyExistsDiv.html('<div class="info-block mbot15">' + response.message + '</div>');
                        } else {
                            $companyExistsDiv.addClass('hide');
                        }
                    }
                });
        });

        $('#firstname').on('keyup', function() {
            var name = $('#firstname').val();
            var pid = $('#contactid').val();
            var $companyExistsDiv = $('#contact_exists_info');
            var data = {
                name: name
            };
            if (pid) {
                data['pid'] = pid;
            }
            $.ajax({
                type: 'POST',
                url: admin_url + 'clients/checkduplicate_contact',
                data: data,
                dataType: 'json',
                success: function(msg) {
                    if (msg.message != 'no') {
                        $companyExistsDiv.removeClass('hide');
                        $companyExistsDiv.html('<div class="info-block mbot15">' + msg.message + '</div>');
                    } else {
                        $companyExistsDiv.addClass('hide');
                    }
                }
            });
        });

        $('input#project_cost').on('keypress', function() {
            return event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57);
        });


        $("select[id^=project_contacts1]").change(function() {
            $('.contactsdiv p.text-danger').hide();
        });

        $("select[id^=project_members]").change(function() {
            $('.form_assigned1 p.text-danger').hide();
        });
    });
</script>
<?php if ($_REQUEST['group'] == 'project_email') { ?>
    <script>
        var BASE_URL = "<?php echo base_url(); ?>";

        var header = document.getElementById("myHeader");
        var sticky = header.offsetTop;

        function sync_mail() {
            document.getElementById('overlay').style.display = '';
            $.ajax({
                url: BASE_URL + 'admin/cronjob/store_local_mails',
                type: 'POST',
                data: {},
                success: function(data) {
                    alert_float('success', 'Mail Fetched Successfully');
                    location.reload();
                    document.getElementById('overlay').style.display = 'none';
                },
                error: function(data) {
                    document.getElementById('overlay').style.display = 'none';
                }
            });

        }
        $(document).ready(function() {
            $("#message-modal").on("hidden.bs.modal", function() {
                $("#message_id").html("");
            });
        });

    </script>

    <script type='text/javascript'>

        $(document).ready(function() {
            $('#pipeline_id').selectpicker('refresh');
        });
    </script>
<?php } ?>

<script>
    $(document).ready(function() {
        // $('.data_display_btn').click(function(e) {
        //     var f = $(this).attr("data-val");

        //     $('.' + f + ' .data_display').hide();
        //     $('.' + f + ' .data_edit').show();
        // });
    
        $('.data-edit-form').each(function() {
            appValidateForm($(this), {
                <?php if($mandatory_fields): ?>
                    <?php foreach ($mandatory_fields as $field): ?>
                        <?php if($field =='clientid'){$field ='clientid_copy_project';} ?>
                        '<?php echo $field ?>' : 'required',
                    <?php endforeach; ?>
                <?php endif; ?>
            }, handel_data_edit_btn);
        });
        

        function handel_data_edit_btn(form) {
            var f =$(form).children('.data_edit_btn').attr("data-val");
            var data = {
                project_id: <?php echo ($project->id); ?>,
                status: $('#status').val()
            };
            data['status'] = $('#status').val();
            data[f] = $('#' + f).val();
            field_update(data, f);
            
        }
        $('.data-edit-form-custom-fields').each(function() {
            appValidateForm($(this), {
                <?php 
                    $custom_fields1 = get_custom_fields('projects');
                    $custom_fields2 = get_custom_fields('leads');
                    $custom_fields = array_merge($custom_fields1, $custom_fields2);
                    if($custom_fields): 
                ?>
                    <?php foreach ($custom_fields as $field): ?>
                        <?php if($field['required']){ if($field['type']){$field['slug'] .='[]';}?>
                            '<?php echo $field['slug'] ?>' : 'required',
                        <?php } ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            }, handel_data_edit_btn_custom_field);
        });

        function handel_data_edit_btn_custom_field(form) {
            var btn =$(form).children('.data_edit_btn_custom');
            var f =btn.attr("data-val");
            if (typeof btn.attr("data-val-type") != 'undefined' && btn.attr("data-val-type") == 'multi-checkbox') {
                var f_val = $('input[name="' + f + '[]"]:checked').map(function(_, el) {
                    return $(el).val();
                }).get();

            } else {
                var f_val = $('#' + f).val();
                if (typeof f_val == 'undefined') {
                    var f_val = $('[name="' + f + '"]').val();
                }
            }
            var data = {
                project_id: <?php echo ($project->id); ?>,
                slug: f,
                f_val: f_val,
                custom_field: '2'
            };
            field_update(data, f);
            
        }

        // $('.data_edit_btn').click(function(e) {
            
        //     var f = $(this).attr("data-val");
        //     var data = {
        //         project_id: <?php echo ($project->id); ?>,
        //         status: $('#status').val()
        //     };
        //     data['status'] = $('#status').val();
        //     data[f] = $('#' + f).val();
        //     field_update(data, f);
        // });

        function field_update(data, f) {
            document.getElementById('overlay').style.display = '';
            $.ajax({
                type: 'POST',
                url: admin_url + 'projects/dyfieldupdate',
                data: data,
                dataType: 'json',
                success: function(msg) {
                    if (msg.err) {
                        alert_float('warning', msg.err);
                    }
                    if (msg.message) {
                        alert_float('success', msg.message);
                    }
                    $('.' + f + ' .data_edit').hide();
                    $('.' + f + ' .data_display .updated_text').html(msg.updated_text);
                    $('.' + f + ' .data_display').show();
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                }
            });
        }
        // $('.data_edit_btn_custom').click(function(e) {
        //     var f = $(this).attr("data-val");
        //     if (typeof $(this).attr("data-val-type") != 'undefined' && $(this).attr("data-val-type") == 'multi-checkbox') {
        //         var f_val = $('input[name="' + f + '[]"]:checked').map(function(_, el) {
        //             return $(el).val();
        //         }).get();

        //     } else {
        //         var f_val = $('#' + f).val();
        //         if (typeof f_val == 'undefined') {
        //             var f_val = $('[name="' + f + '"]').val();
        //         }
        //     }
        //     var data = {
        //         project_id: <?php echo ($project->id); ?>,
        //         slug: f,
        //         f_val: f_val,
        //         custom_field: '2'
        //     };
        //     field_update(data, f);
        // });
        $('.getcontactsbyorg').click(function(e) {
            //   var data = {project_id:<?php echo ($project->id); ?>,f:$('.'+f+' data_edit').val()};
            var data = {
                project_id: <?php echo ($project->id); ?>,
                clientid: $('#clid').val()
            };
            $.ajax({
                type: 'POST',
                url: admin_url + 'projects/getcontactsbyorg',
                data: data,
                dataType: 'json',
                success: function(msg) {
                    $('.contactsdiv select').html(msg.contact);
                    $('.primarydiv select').html(msg.primarycontact);
                    setTimeout(function() {
                        $('.contactsdiv select').selectpicker('refresh');
                        $('.primarydiv select').selectpicker('refresh');
                    }, 1000);
                }
            });

        });
        $(".contactsdiv .selectpicker").change(function() {
            $('#primary_contact').empty().append('<option value="">Nothing Selected</option>');
            $('#primary_contact').selectpicker('refresh');
            var option_all = $(".contactsdiv .selectpicker option:selected").map(function() {
                $('#primary_contact').append('<option value="' + $(this).val() + '">' + $(this).text() + '</option>');
            });
            $('#primary_contact').selectpicker('refresh');
        });

        <?php if (isset($project_overview_chart)) { ?>
            var project_overview_chart = <?php echo json_encode($project_overview_chart); ?>;
        <?php } ?>


        $('#clientid_copy_project').on('change', function() {
            var clientId = this.value;
            var data = {
                clientId: clientId
            };

            $.ajax({
                type: 'POST',
                url: admin_url + 'projects/getContactpersonList',
                data: data,
                dataType: 'json',
                success: function(msg) {
                    $('.contactsdiv1 select').html(msg.success);
                    setTimeout(function() {
                        $('.contactsdiv1 select').selectpicker('refresh');
                    }, 500);
                }
            });
        });

        window.addEventListener('load', function() {
            appValidateForm($('#clientid_add_group_modal'), {
                company: 'required'
            }, manage_customer_groups);

            function manage_customer_groups() {

            }
            $('#clientid_add_group_modal').on('show.bs.modal', function(e) {
                var invoker = $(e.relatedTarget);
                var group_id = $(invoker).data('id');
                $('#clientid_add_group_modal input[name="company"]').val('');
                // is from the edit button
                if (typeof(group_id) !== 'undefined') {
                    $('#clientid_add_group_modal input[name="company"]').val($(invoker).parents('tr').find('td').eq(0).text());
                }
            });

        });

        $('#clientid_add_group_modal').submit(function(e) {
            e.preventDefault();
            var form = $(this);
            var data = getFormData(form);
            if (data.company) {
                $.ajax({
                    type: form.attr('method'),
                    url: form.attr('action'),
                    data: form.serialize(),
                    dataType: 'json',
                    success: function(msg) {
                        $('#clientid_copy_project').append('<option value="' + msg.id + '" selected="selected">' + msg.company + '</option>');
                        $('#clientid_copy_project').val(msg.id);
                        $('#clientid_add_group_modal input[name="company"]').val('');
                        alert_float('success', msg.message);
                        setTimeout(function() {
                            $('#clientid_copy_project').selectpicker('refresh');
                            $('.clientiddiv div.filter-option-inner-inner').html(msg.company)
                        }, 500);
                        $('#clientid_add_modal').modal('hide');
                    }
                });
            }
        });


        window.addEventListener('load', function() {
            appValidateForm($('#project_contacts_add'), {
                firstname: 'required'
            }, manage_project_contacts_add);

            function manage_project_contacts_add(form) {}
            $('#project_contacts_modal').on('show.bs.modal', function(e) {
                var invoker = $(e.relatedTarget);
                var group_id = $(invoker).data('id');
                $('#project_contacts_add input[name="firstname"]').val('');
                $('#project_contacts_add input[name="email"]').val('');
                $('#project_contacts_add input[name="phonenumber"]').val('');
                // is from the edit button
                if (typeof(group_id) !== 'undefined') {
                    $('#project_contacts_add input[name="firstname"]').val($(invoker).parents('tr').find('td').eq(0).text());
                }
            });

        });



        $('#project_contacts_add').submit(function(e) {
            e.preventDefault();
            var form = $(this);
            var data = getFormData(form);
            data.clientid = $('#clid').val();
            if (data.firstname) {
                $.ajax({
                    type: form.attr('method'),
                    url: form.attr('action'),
                    data: data,
                    dataType: 'json',
                    success: function(msg) {
                        $('.team-contacts.project-overview-team-contacts').append(msg.card);
                        alert_float('success', msg.message);

                        $('#project_contacts_modal').modal('hide');
                    }
                });
            }
        });

    });

    function getFormData($form) {
        var unindexed_array = $form.serializeArray();
        var indexed_array = {};

        $.map(unindexed_array, function(n, i) {
            indexed_array[n['name']] = n['value'];
        });

        return indexed_array;
    }
</script>
<style>
    .single_linet {
        white-space: unset;
    }
</style>

<style>
    .data_display {
        padding-bottom: 5px;
        cursor: pointer;
    }

    .data_display .data_display_btn {
        display: none;
        padding: 0px;
    }

    .data_display:hover .data_display_btn {
        display: inline-block;
    }
</style>
<script>
    function addFooterEmptyCell() {
        var headercount = $('#topheading > div').length;
        $('.footer-empty-cells').remove();
        for (let index = 0; index < headercount - 2; index++) {
            $('#particularsrowfooter').prepend(`<div class="footer-empty-cells"></div>`);
        }
    }

    document.addEventListener("DOMContentLoaded", function(event) {
        addFooterEmptyCell();
    });

    function changeProjectStatus(projectId, statusId) {
        document.getElementById('overlay').style.display = '';
        $.ajax({
            type: 'POST',
            url: "<?php echo admin_url('projects/update_project_status') ?>",
            data: {
                projectid: projectId,
                status: statusId
            },
            success: function(resultData) {
                alert_float("success", 'Stage updated successfully');
                setTimeout(function() {
                    location.reload();
                }, 20);
            }
        });
    }

    $('.data_display_btn').on('click', function(event) {
        $('.deal-field-update-dropdown').hide();
        $(this).parents('.dropdown').find('.deal-field-update-dropdown').show();
    });


    

    $('.deal-field-update-dropdown .close-dropdown').click(function(e) {
        $(this).parents('.dropdown').find('.deal-field-update-dropdown').hide();
    });
</script>

<?php 
if($tab['view'] =='project_email' || $this->input->get('group') == 'project_email'){
    $this->load->view('admin/staff/emailcomposerjs');
}
 
?>
</body>

</html>