<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
  <?php foreach($this->tasks_model->get_statuses() as $summary){ ?>
    <div class="col-md-2 col-xs-6 border-right">     
      <p class="font-medium no-mbot">
        <a style="color:<?php echo $summary['color']; ?>" href="#" id="task_status_<?php echo $summary['id']; ?>" data-cview="task_status_<?php echo $summary['id']; ?>" onclick="dt_custom_view('task_status_<?php echo $summary['id']; ?>','<?php echo $view_table_name; ?>','task_status_<?php echo $summary['id']; ?>'); return false;"><?php echo $summary['name']; echo " - ";?><span class="<?php echo $summary['count_class'] ?>">0</span></a> 
      </p>
      <p class="font-medium-xs no-mbot text-muted" id="me_<?php echo $summary['name']; ?>">
        <?php echo _l('tasks_view_assigned_to_user'); ?>: 0
      </p>
    </div>
  <?php } ?>
<input type="hidden" id="assign_me_text" value="<?php echo _l('tasks_view_assigned_to_user'); ?>">