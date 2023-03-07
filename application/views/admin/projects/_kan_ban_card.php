<?php defined('BASEPATH') or exit('No direct script access allowed');
$project_already_client_tooltip = '';
$project_is_client = 1;
if ($project['status'] == $status['id']) { ?>

<li data-project-id="<?php echo $project['id']; ?>"<?php echo $project_already_client_tooltip; ?> class="project-kan-ban<?php if($project_is_client && get_option('project_lock_after_convert_to_customer') == 1 && !is_admin()){echo ' not-sortable';} ?>">
	<div class="panel-body project-body">
		<div class="row">
			<div class="col-md-12 project-name">
				<div class="row">
					<div class="col-xs-9">
						<a href="<?php echo admin_url('projects/view/'.$project['id']); ?>" onclick="init_project(<?php echo $project['id']; ?>);return false;" class="pull-left">
							<span class="inline-block mbot10 text-bold"><i class="fa fa-handshake-o mright5"></i><?php echo $project['project_name']; ?></span>
						</a>
					</div>
					<div class="col-xs-3">
						<?php 
							if($project['taskscount'] > 0){
								$gas = $this->projects_model->get_activity_status($project['id']);
								//pre($gas);
								$today = $upcoming = $overdue = '';
								foreach($gas as $val) {
									$sdate = date('Y-m-d', strtotime($val['startdate'])); 
									if((strtotime($sdate) == strtotime(date('Y-m-d'))) && $val['status'] != 5) {
										$today = 3;
									}
									if((strtotime($sdate) > strtotime(date('Y-m-d'))) && $val['status'] != 5) {
										$upcoming = 1;
									}
									if((strtotime($sdate) < strtotime(date('Y-m-d'))) && $val['status'] != 5) {
										$overdue = 2;
									}
								}
							}
						?>
						<?php
							if($overdue == '' && $today == '' && $upcoming == ''){
								echo '<span style="color: #d2be19;" data-toggle="tooltip" data-placement="top" data-html="true" data-original-title="'._l('no_tasks_found').'" class="pull-right"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i></span>';
							}elseif(!empty($overdue)){
								echo '<span style="color: red; " data-toggle="tooltip" data-placement="top" data-html="true" data-original-title="'._l('overdue_deal').'" class="pull-right"><i class="fa fa-arrow-circle-left" aria-hidden="true"></i></span>';
							}elseif($overdue == '' && !empty($today)){
								echo '<span style="color: green; " data-toggle="tooltip" data-placement="top" data-html="true" data-original-title="'._l('today_deal').'" class="pull-right"><i class="fa fa-arrow-circle-right" aria-hidden="true"></i></span>';
							}elseif($overdue == '' && $today == '' && !empty($upcoming)){
								echo '<span style="color: #ccc; " data-toggle="tooltip" data-placement="top" data-html="true" data-original-title="'._l('future_deal').'"  class="pull-right"><i class="fa fa-arrow-circle-right" aria-hidden="true"></i></span>';
							}
						?>
					</div>
				</div>
			</div>
			<?php if($project['project_cost']>0): ?>
			<div class="col-md-12 text-muted">
				<span  class="text-dark"><?php echo app_format_money($project['project_cost'], $project['project_currency']); ?></span>
			</div>
			<?php endif;?>
			<?php if($project['company']): ?>
			<div class="col-md-12 text-muted single-line">
				<span  class="text-dark" data-toggle="tooltip" data-placement="top" data-html="true" data-original-title="<?php echo $project['company']; ?>"><i class="fa fa-building-o mright5"></i><?php echo $project['company']; ?></span>
			</div>
			<?php endif;?>
			<?php if($project['contact_name']): ?>
				<div class="col-md-12 text-muted">
					<div class="row">
						<div class="col-xs-9 single-line">
							<span  class="text-dark" data-toggle="tooltip" data-placement="top" data-html="true" data-original-title="<?php echo $project['contact_name'];?>"><i class="fa fa-id-card-o mright5"></i><?php echo $project['contact_name'];?></span>
						</div>
						<div class="col-xs-3">
							<?php if(isset($project['contact_phonenumber']) && !empty($project['contact_phonenumber']) && $staff_allowed_to_call == 1) { 
								$calling_code =$this->callsettings_model->getCallingCode($project['contacts_phone_country_code']);
								echo '<a class="deal-kanban-call-btn pull-right" href="#" onclick="callfromdeal('.$project['contact_id'].','.$project['id'].','.$project['contact_phonenumber'].',\'deal\',\''.$calling_code.'\');" data-toggle="tooltip" data-placement="top" data-html="true" data-original-title="Call now"><i class="fa fa-phone" aria-hidden="true"></i></a>';
							}?>
						</div>
					</div>
					
				</div>
			<?php endif;?>
			<?php if($team_leader =get_staff_full_name($project['teamleader'])): ?>
			<div class="col-md-12 text-muted single-line">
				<span  class="text-dark" data-toggle="tooltip" data-placement="top" data-html="true" data-original-title="<?php echo $team_leader; ?>"><i class="fa fa-user mright5"></i><?php echo $team_leader; ?></span>
			</div>
			<?php endif;?>
			<?php if(false && $project['deadline']): ?>
			<div class="col-md-12 text-muted">
				<?php $date=date_create($project['deadline']);
                	$deadline =date_format($date,"M d , Y");
					$deadline_class="text-dark";
					if($date < date_create()){
						$deadline_class="text-danger";
					}
				?>
				
				<span  class="<?php echo $deadline_class ?>"><i class="fa fa-clock-o mright5"></i><?php echo $deadline; ?></span>
			</div>
			<?php endif;?>
			<?php if($this->session->has_userdata('projects_kanban_view') && $this->session->userdata('projects_kanban_view') == 'true') { ?>
				
				<div class="col-md-12 text-right text-muted">
					<span class="mright5  inline-block text-muted" data-toggle="tooltip" data-placement="left" data-title="<?php echo _l('projects_canban_notes',$project['total_notes']); ?>">
						<i class="fa fa-sticky-note-o"></i> <?php echo $project['total_notes']; ?>
					</span>
					<span class=" inline-block text-muted mright5" data-placement="left" data-toggle="tooltip" data-title="<?php echo _l('project_kan_ban_attachments',$project['total_files']); ?>">
						<i class="fa fa-paperclip"></i>
						<?php echo $project['total_files']; ?>
					</span>
				</div>
				<?php if(isset($project['tags']) && $project['tags']){ ?>
					<div class="col-md-12">
						<div class="mtop5 kanban-tags">
							<?php echo render_tags($project['tags']); ?>
						</div>
					</div>
				<?php } ?>
			<?php } ?>
		</div>
   </div>
</li>
<?php }
