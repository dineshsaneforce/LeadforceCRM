<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<script>	
document.addEventListener("DOMContentLoaded", function(event){
    $('body').on('click', '.edit-task', function(e) {
        e.preventDefault();
        var taskTypeId = $(this).closest('.task-type-link').data('taskypeid');
		var selectedIcon = $(this).data('icon');
		var modalTitle = document.getElementById('modalTitle');
		modalTitle.textContent = 'Edit Activity Type';
		if(taskTypeId == 1){
			document.getElementById('status-control').style.display = 'none';
		}else{
			document.getElementById('status-control').style.display = 'block';
		}
        $.ajax({
            url: admin_url+('tasktype/edit/' + taskTypeId),
            type: "POST",
            data: {id: taskTypeId},
            dataType: "json",
            success: function(response){
                if (response.success == true){
					$('#tasktype-form [name="id"]').val(taskTypeId);
					$('#addTaskTypeModal input[name="name"]').val(response.data.name);
					$('.icons-list i').removeClass('selected');
					$('#addTaskTypeModal [name="icon"]').val(response.data.icon);
					$('.icons-list i.' + response.data.icon).addClass('selected');
					$('#addTaskTypeModal [name="status"]').val(response.data.status).selectpicker('refresh');
                    $('#addTaskTypeModal').modal('show');

                }
            },
        });
    });
$('.icons-list li').click(function() {
	$('.icons-list i').removeClass('selected');
    var selectedIcon = $(this).data('icon');
	$('.icons-list i.' + selectedIcon).addClass('selected');
	$('#tasktype-form [name="icon"]').val(selectedIcon);
});


});
function check_name(){
	var name_val = $('#name').val();
	$('#name_id').hide();
	if (  name_val.match(/^[a-zA-Z0-9]+/)  ) {
	}else if ( name_val==''  ) {
	} 
	else {
	$('#name_id').show();
	}
}
function change_name(a,ch_id){
	$('#'+ch_id).val(a.trim());
}
</script>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="col-md-12">
				<div class="panel_s">
					<div class="panel-body">
						<div class="_buttons">
							<?php if(has_permission('tasktype','','create')) { ?>
								<button type="button" class="btn btn-info pull-left display-block" data-toggle="modal" id="newTaskTypeModal" data-target="#addTaskTypeModal"><?php echo _l('new_tasktype'); ?></button>
							<?php } else { ?>
								<p class="btn btn-info pull-left display-block"><?php echo _l('tasktype'); ?></p>
							<?php } ?>
						</div>
						<div class="clearfix"></div>
							<hr class="hr-panel-heading"/>
						<div class="clearfix"></div>
							<?php render_datatable(array(
								_l('name'),
								_l('icon'),
								_l('status'),
								_l('option'),
								),'tasktypes',[],[
								'data-last-order-identifier' => 'tasktype',
								'data-default-order'         => get_table_last_order('tasktype'),
							]); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- end tasktype records -->
<div class="modal fade" id="addTaskTypeModal" style="display: none;">
	<div class="modal-dialog">
		<div class="modal-content">		
			<?php echo form_open(admin_url("tasktype/save"), array('id' => 'tasktype-form', 'method' => 'post')); ?>
			<?php echo form_hidden('id', 0);?>
			<?php echo form_hidden('icon', 'fa-tasks'); ?>
			<div class="modal-header">
				<h5 class="modal-title" id="modalTitle"></h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="clearfix"></div>
					<?php $attrs = array('required' => true, 'onblur' => "change_name(this.value,'name')", 'onkeyup' => 'check_name()','maxlength'=>50); ?>
					<?php echo render_input('name','name','','text',$attrs); ?>
					<div class="text-danger" id="name_id" style="display:none">Please enter valid name</div>
					<div class="icon-group" id="icon-control" style="bottom: 7px; position: relative;">
						<?php echo _l('icon'); ?>
						<ul class="icons-list" name="icon" style="border-radius: 5px;">
							<li id="icon-envelope" data-icon="fa-envelope"><i class="fa fa-envelope" title="Envelope" aria-hidden="true"></i></li>
							<li id="icon-tasks" data-icon="fa-tasks"><i class="fa fa-tasks selected" title="Tasks" aria-hidden="true"></i></li>
							<li id="icon-phone" data-icon="fa-phone"><i class="fa fa-phone" title="Phone" aria-hidden="true"></i></li>
							<li id="icon-video-camera" data-icon="fa-video-camera"><i class="fa fa-video-camera" title="Video call" aria-hidden="true"></i></li>
							<li id="icon-users" data-icon="fa-users"><i class="fa fa-users" title="Users" aria-hidden="true"></i></li>
							<li id="icon-clock-o" data-icon="fa-clock-o"><i class="fa fa-clock-o" title="Clock" aria-hidden="true"></i></li>
							<li id="icon-coffee" data-icon="fa-coffee"><i class="fa fa-coffee" title="Coffee" aria-hidden="true"></i></li>
							<li id="icon-exchange" data-icon="fa-exchange"><i class="fa fa-exchange" title="Exchange" aria-hidden="true"></i></li>
							<li id="icon-paper-plane" data-icon="fa-paper-plane"><i class="fa fa-paper-plane" title="Paper plane" aria-hidden="true"></i></li>
							<li id="icon-shopping-cart" data-icon="fa-shopping-cart"><i class="fa fa-shopping-cart" title="Shopping cart" aria-hidden="true"></i></li>
							<li id="icon-car" data-icon="fa-car"><i class="fa fa-car" title="Car" aria-hidden="true"></i></li>
							<li id="icon-truck" data-icon="fa-truck"><i class="fa fa-truck" title="truck" aria-hidden="true"></i></li>
							<li id="icon-archive" data-icon="fa-archive"><i class="fa fa-archive" title="Archive" aria-hidden="true"></i></li>
						</ul>
					</div>
					<div class="form-group select-placeholder" id="status-control">
						<label for="status" class="control-label" ><?php echo _l('status'); ?></label>
						<select required="1" name="status" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('status'); ?>">
						<option value=""><?php echo _l('tasktype_option_select'); ?></option>
						<option value="active" selected  ><?php echo _l('tasktype_option_active'); ?></option>
						<option value="inactive" ><?php echo _l('tasktype_option_inactive'); ?></option>
						</select>
					</div>
			</div>
			<div class="modal-footer">
					<div>
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					<button type="submit" class="btn btn-primary" id="saveTaskType" >Save</button>
					</div>
			</div>
			<?php echo form_close();?>
		</div>
	</div>
</div>
<?php init_tail(); ?>
<script>
$(function(){
	initKnowledgeBaseTablePipelines();
function initKnowledgeBaseTablePipelines(){
	var KB_Pipelines_ServerParams = {};
	$.each($('._hidden_inputs._filters input'),
	function () {
		KB_Pipelines_ServerParams[$(this).attr('name')] = '[name="' + $(this).attr('name') + '"]';
	});
	$('._filter_data').toggleClass('hide');
	initDataTable('.table-tasktypes', window.location.href, undefined, undefined, KB_Pipelines_ServerParams, [0, 'asc']);
}
});
document.addEventListener("DOMContentLoaded", function(event){
	var modalTitle = document.getElementById('modalTitle');
	$('#newTaskTypeModal').click(function(){
		document.getElementById('status-control').style.display = 'block';
		modalTitle.textContent = 'Add Activity Type';
		$('.tasktype-form_errors').remove();
		$('#tasktype-form').trigger('reset');
		$('#tasktype-form [name="id"]').val(0);
	});
	appValidateForm(
		'#tasktype-form',
			{
			name: {
			required: true
			},
			status: {
			required: true
			}
			},
		function(form){
		$('.tasktype-form_errors').remove();
			$.ajax({
				url: form.action,
				type: form.method,
				data: $(form).serialize(),
				dataType : "json",
				success: function (response){
					if(response.success ==true){
						alert_float('success', response.msg);
						setTimeout(function(){
						window.location.reload();
						},1000);
					}else{
						alert_float('warning', response.msg);
						$.each(response.errors, function(k, v){
						$('[name="'+k+'"]').parent().append(`<p class="text-danger tasktype-form_errors">`+v+`</p>`)
						});
					}
				},
			});
		}
	);
});
</script>
<style>
.modal-title{
	top: 22px;
    position: relative;
}
.modal-header{
    background: var(--theme-primary-dark);
    border-radius: 5px;
    padding: 10px;
	border-bottom: 10px solid rgb(3 18 51);
}
ul.icons-list{
	border: 1px solid #bfcbd9; 
	padding: 0px;
}
.icons-list {
	list-style: none;
	display: flex;
	flex-wrap: wrap;
	margin: 0;
	padding: 0;
}
.icons-list li {
	margin: 5px;
}
.icons-list i {
	font-size: 20px;
	padding: 5px;
	cursor: pointer;
}
.icons-list i.selected {
	background-color: #031233;
	color: white;
}
.table>tbody>tr>td, .table>tfoot>tr>td {
    padding: 10px 78px 15px 20px;
}
.table>tbody>tr>td {
    border-top: 0px solid #cfcfcf;
}
</style>
</body>
</html>