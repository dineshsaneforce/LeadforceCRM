<?php

hooks()->add_action('app_init', 'app_init_workflow_actions');

function app_init_workflow_actions()
{
    $CI = &get_instance();
    $CI->load->model('workflow_model');
    
    $actions = [
        'workflow/app_workflow',
        // 'workflow/lead_created_workflow',
        // 'workflow/deal_created_workflow',
        // 'workflow/activity_reminder_workflow',
        'workflow/deal_approval_workflow',


        'workflow/workflow_app',
        'workflow/lead_workflow',
        'workflow/project_workflow',
    ];

    foreach ($actions as $action) {
        $CI->load->library($action);
    }

}

hooks()->add_action('lead_created','workflow_lead_created');

function workflow_lead_created($lead_id)
{
    $CI = &get_instance();
    $CI->lead_workflow->lead_created($lead_id);
}

hooks()->add_action('after_add_project','workflow_deal_created');

function workflow_deal_created($deal_id)
{
    $CI = &get_instance();
    $CI->project_workflow->project_created($deal_id);
}

hooks()->add_action('after_update_project','workflow_deal_updated');

function workflow_deal_updated($data)
{
    if(is_array($data)){
        $project_id =$data['project_id'];
        if(isset($data['change_log_id'])){
            $change_log_id =$data['change_log_id'];
        }
    }else{
        $project_id =$data;
        $change_log_id =0;
    }
    
    $CI = &get_instance();
    $CI->project_workflow->project_updated($project_id,$change_log_id);
}

hooks()->add_action('after_added_project_note','workflow_deal_note_added');

function workflow_deal_note_added($timeline_id)
{
    $CI = &get_instance();
    $CI->project_workflow->project_note_added($timeline_id);
}

hooks()->add_action('after_delete_project','workflow_deal_deleted');

function workflow_deal_deleted($deal_id)
{
    $CI = &get_instance();
    $CI->project_workflow->project_deleted($deal_id);
}

hooks()->add_action('after_add_project_approval','workflow_deal_created_approval');

function workflow_deal_created_approval($deal_id)
{

    $CI = &get_instance();
    $CI->load->model('workflow_model');
    $approval_success =$CI->deal_approval_workflow->trigger($deal_id);
    if($approval_success ===true){
        set_alert('success', _l('deal_sent_for_approval'));
        redirect(admin_url('projects/view/'.$deal_id));
    }
    
}