<?php defined('BASEPATH') or exit('No direct script access allowed');
$is_admin = is_admin();
$i = 0;

foreach ($statuses as $status) {
  
  $total_pages = ceil($this->projects_model->do_kanban_query($status['id'],$this->input->get('search'),1,array(),true)/get_option('projects_kanban_limit'));
  $settings = '';
  foreach(get_system_favourite_colors() as $color){
    $color_selected_class = 'cpicker-small';
    if($color == $status['color']){
      $color_selected_class = 'cpicker-big';
    }
    $settings .= "<div class='kanban-cpicker cpicker ".$color_selected_class."' data-color='".$color."' style='background:".$color.";border:1px solid ".$color."'></div>";
  }
  ?>
  <ul class="kan-ban-col" data-col-status-id="<?php echo $status['id']; ?>" data-total-pages="<?php echo $total_pages; ?>" >
    <li class="kan-ban-col-wrapper">
      <div class="border-right panel_s">
        <?php
        $status_color = '';
        if(!empty($status["color"])){
          $status_color = 'style="border-top:5px solid '.$status['color'].';"';
        }
       
		    $projects = $this->projects_model->do_kanban_query_count($status['id'],$this->input->get('search'),1,array('sort_by'=>$this->input->get('sort_by'),'sort'=>$this->input->get('sort')));
			$tot_amt = array_sum(array_column($projects, 'project_cost'));
			
        $total_projects = count($projects);
		// $projects = $this->projects_model->do_kanban_query($status['id'],$this->input->get('search'),1,array('sort_by'=>$this->input->get('sort_by'),'sort'=>$this->input->get('sort')));
        $base_currency = $this->projects_model->get_currency('');
        $amt = 0;
		//$total_projects = count($projects);
       /* foreach ($projects as $project) {
          $conversion_rate = $this->projects_model->conversionrate($base_currency->name,$project['project_currency']);
         // pre($conversion_rate);
          if($conversion_rate) {
            if($conversion_rate[0]['operation'] == '*')
                $amt = $amt + ($project['project_cost']*$conversion_rate[0]['rate']);
            else
                $amt = $amt + ($project['project_cost']/$conversion_rate[0]['rate']);
          } else {
              $amt = $amt + $project['project_cost'];
          }
        }
        $tot_amt = $amt;*/
		    $projects = $this->projects_model->do_kanban_query($status['id'],$this->input->get('search'),1,array('sort_by'=>$this->input->get('sort_by'),'sort'=>$this->input->get('sort')));
        ?>
        <div class="panel-heading-bg panel-body" <?php if($status['isdefault'] == 1){ ?>data-toggle="tooltip" data-title="<?php echo _l('projects_converted_to_client') . ' - '. _l('client'); ?>"<?php } ?> <?php echo $status_color; ?> data-status-id="<?php echo $status['id']; ?>">
          <div class="" style="display: flex;">
            <div class="" style="
flex-grow: 1;
overflow: hidden;
text-overflow: ellipsis;
white-space: nowrap;
width: 1%;">
            <p class="heading pointer" <?php if($is_admin){ ?> dat data-order="<?php echo $status['statusorder']; ?>" data-color="<?php echo $status['color']; ?>" data-name="<?php echo $status['name']; ?>" <?php } ?> title="<?php echo $status['name'].' ('.$total_projects.')'; ?>" data-title="<?php echo $status['name']; ?>">
		          <?php echo $status['name']; ?>
            </p>
            </div>
            <div class="text-right" style="white-space: nowrap;">
              <span class="kanban-stage-deal-count"><?php echo $total_projects ?></span>
              <?php if (has_permission('projects', '', 'create')): ?>
              <a href ="#" onclick="setDealStage('<?php echo $status['id'] ?>','<?php echo $status['name']; ?>')" data-toggle="modal" data-target="#newDealModal" style="color:var(--theme-info-dark)" class="mleft4"><i class="fa fa-plus" aria-hidden="true"></i></a>
              <?php endif; ?>
            </div>
          </div>
          <p style="margin:0px" class="text-muted"><?php echo $base_currency->symbol;?> <?php echo number_format($tot_amt,2); ?></p>
          <!-- <a href="#" onclick="return false;" class="pull-right kanban-color-picker kanban-stage-color-picker<?php if($status['isdefault'] == 1){ echo ' kanban-stage-color-picker-last'; } ?>" data-placement="bottom" data-toggle="popover" data-content="
            <div class='text-center'>
              <button type='button' return false;' class='btn btn-success btn-block mtop10 new-project-from-status'>
                <?php echo _l('new_project'); ?>
              </button>
            </div>
            <hr />
            <div class='kan-ban-settings cpicker-wrapper'>
              <?php echo $settings; ?>
            </div>" data-html="true" data-trigger="focus">
            
          </a> -->
        </div>
        <div class="kan-ban-content-wrapper" style="width:100%">
          <div class="kan-ban-content">
      <!-- <ul class="<?php if($is_admin){ ?>status<?php } ?> projects-status sortable" data-project-status-id="<?php echo $status['id']; ?>"> -->
      <ul class="status projects-status sortable" data-project-status-id="<?php echo $status['id']; ?>" id="status_<?php echo $status['id'];?>">
              <?php
              foreach ($projects as $project) {
                
                $this->load->view('admin/projects/_kan_ban_card',array('project'=>$project,'status'=>$status,'staff_allowed_to_call'=>$this->callsettings_model->accessToCall()));
              } ?>
              <?php if($total_projects > 0 ){ ?>
              <!-- <li class="text-center not-sortable kanban-load-more" data-load-status="<?php echo $status['id']; ?>">
              <a href="#" class="btn btn-default btn-block<?php if($total_pages <= 1){echo ' disabled';} ?>" data-page="1" onclick="kanban_load_more(<?php echo $status['id']; ?>,this,'projects/projects_kanban_load_more',315,360); return false;";>
              <?php echo _l('load_more'); ?>
              </a>
             </li> -->
             <?php } ?>
            
             <li class="text-center not-sortable mtop30 kanban-empty<?php if($total_projects > 0){echo ' hide';} ?>" id="results">
              <h4>
                <i class="fa fa-circle-o-notch" aria-hidden="true"></i><br /><br />
                <?php echo _l('no_projects_found'); ?></h4>
              </li>
            </ul>
          </div>
        </div>
      </li>
    </ul>
    <?php $i++; } ?>
