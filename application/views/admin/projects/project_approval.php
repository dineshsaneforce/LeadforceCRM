<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php 
$can_user_edit =false;
if(is_admin(get_staff_user_id()) || $project->teamleader == get_staff_user_id() || in_array(get_staff_user_id(),$ownerHierarchy) || (!empty($my_staffids) && in_array($project->teamleader,$my_staffids) && !in_array($project->teamleader,$viewIds))){
   $can_user_edit =true;
}
if($project->approved==0 && !$deal_rejected){
   $can_user_edit =false;
}

if($project->approved==0 && $deal_rejected && get_staff_user_id() != $project->created_by){
   $can_user_edit =false;
}
$hasHIstory =$this->approval_model->hasHistory('projects',$project->id)?true:false;
$hasApprovalFlow = $this->workflow_model->getflows('deal_approval',0,['service'=>'approval_level']);

?>
<?php if($hasApprovalFlow && ($hasHIstory || $project->approved ==0)): ?>
   <?php if($deal_rejected): ?>
      <?php approvalFlowTree($approval_history) ?>
      <?php $reopenedHistory =$this->approval_model->getReopenedHistory('projects',$project->id); ?>
      <?php if($reopenedHistory):?>
         <?php approvalFlowTree($reopenedHistory,'Previous History') ?>
      <?php endif;?>
   <?php elseif($approval_flow): 
      $approval_count =$lastApprovalLevel =0;
      if($approval_history){
         $lastApprovalLevel =count($approval_history);
      }
   ?>
   <p class="project-info bold font-size-14">Approval Status</p>
   <div class="activity-feed">
   <?php foreach($approval_flow as $key => $approval): 
      if($approval->service !='approval_level')
      {
         continue;
      }
      $approval_count++;
      $currentHistory =array();
      if(isset($approval_history[$approval_count-1])){
         $currentHistory =$approval_history[$approval_count-1];
         if($currentHistory->status ==1){
            $approval_status ='approved';
         }else{
            $approval_status ='rejected';
         }
         
         
      }else{
         $approval_status ='pending';
      }
      $currentLevelStaff =false;
      if($currentHistory){
         $currentLevelStaff =$this->staff_model->get($currentHistory->approved_by);
      }elseif(isset($staff_hierarchy[$approval_count-1])){
         $currentLevelStaff =$staff_hierarchy[$approval_count-1];
      }
      ?>
      <div class="feed-item <?php echo ($approval_status =='approved')?'approved-status':'pending-status'; ?>">
        <div class="row">
            <div class="col-md-8">

               <?php if($approval_status =='approved'): ?>
                  <div class="date"><span class="text-has-action text-success" data-toggle="tooltip" data-title="<?php echo _dt($currentHistory->approved_at); ?>" data-original-title="" title="">Approved -  <?php echo time_ago($currentHistory->approved_at); ?></span></div>
               <?php elseif($approval_status =='rejected'): ?>
                  <div class="date"><span class="text-has-action text-danger" data-toggle="tooltip" data-title="<?php echo _dt($currentHistory->approved_at); ?>" data-original-title="" title="">Rejected -  <?php echo time_ago($currentHistory->approved_at); ?></span></div>
               <?php else: ?>
                  <div class="date">Pending</div>
               <?php endif; ?>
                
               <div class="text">
                  <?php if($currentLevelStaff): ?>
                  <div style="display: flex;">
                     <div>
                        <a href="<?php echo admin_url('profile/'.$currentLevelStaff->staffid) ?>"><?php echo staff_profile_image($currentLevelStaff->staffid,array('staff-profile-image-small','media-object')); ?></a>
                     </div>
                     <div>
                        <p class="mbot10 no-mtop"><?php echo $currentLevelStaff->full_name; ?></p>
                        <p class="text-muted"><?php echo $currentLevelStaff->designation_name ?></p>
                        <?php if ($approval_status == 'approved') : ?>
                           <?php if($currentHistory->remarks):?>
                           <p class="mbot10 no-mtop"><?php echo _l('remarks') ?> : <?php echo $currentHistory->remarks ?></p>
                           <?php endif; ?>
                        <?php elseif ($approval_status == 'rejected') : ?>
                           <?php $reason =$this->DealRejectionReasons_model->getDealRejectionReasonsbyId($currentHistory->reason);
                           if($reason):?>
                           <p class="mbot10 no-mtop text-danger"><?php echo _l('reason') ?> : <?php echo $reason->name ?></p>
                           <?php endif; ?>
                           <p class="mbot10 no-mtop"><?php echo _l('remarks') ?> : <?php echo $currentHistory->remarks ?></p>
                        <?php endif; ?>
                     </div>
                  </div>
                  
                  <?php if($approval_status ==' approved'): ?>
                     
                  <?php else: ?>
                     <?php if($lastApprovalLevel ==$approval_count-1 && $currentLevelStaff && $currentLevelStaff->staffid ==get_staff_user_id()): ?>
                        <br>
                        <button class="btn btn-success deal-approve" data-deal-id="<?php echo $project->id ?>" >Approve</button>
                        <button class="btn btn-danger deal-reject" data-deal-id="<?php echo $project->id ?>" >Reject</button>
                     <?php endif; ?>
                  <?php endif; ?>
                  <?php else: ?>
                     <p class="mtop10 no-mbot">Auto approval</b></p>
                  <?php endif; ?>
               </div>
                
            </div>
            <div class="clearfix"></div>
            <div class="col-md-12">
                <hr class="hr-10">
            </div>
        </div>
      </div>
      <?php if($approval_status =='rejected'){break;} ?>
   <?php endforeach; ?>
   </div>
   <?php $reopenedHistory =$this->approval_model->getReopenedHistory('projects',$project->id); ?>
   <?php if($reopenedHistory):?>
      <?php approvalFlowTree($reopenedHistory,'Previous History') ?>
   <?php endif;?>
   <?php else: ?>
      <h3>Approval doesnot provided</h3>
   <?php endif; ?>
<?php endif; ?>