<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<br>
<?php echo form_open(admin_url('workflow/saveconfig/'), array('id' => 'ProjectAssignStaffConfig')); ?>
<div class="form-group">
    <label for="type" class="control-label">Assign Type</label>
    <select name="type" id="type" class="form-control selectpicker" required>
        <option >Nothing selected</option>
        <option value="direct_assign" selected>Direct assign</option>
        <option value="round_robin_method">Round-robin method</option>
        <option value="having_less_no_of_projects">Having less no of deals</option>
        <option value="having_more_wins">Having more wins</option>
    </select>
</div>


<div class="form-group dynamic-form-group" id="stafftypeFormGroup">
    <label for="stafftype" class="control-label">Staff Type</label>
    <select name="stafftype" id="stafftype" class="form-control selectpicker" required>
        <option >Nothing selected</option>
        <option value="staff" selected>Staffs</option>
        <option value="roles">Roles</option>
        <option value="designation">Designation</option>
    </select>
</div>


<div class="form-group dynamic-form-group" id="assigntoFormGroup">
    <label for="assignto" class="control-label">Select Staff</label>
    <select name="assignto" id="assignto" class="form-control selectpicker" data-live-search="true" required>
    <option value="">Select Staff</option>
    <?php foreach($staffs as $staffid => $staffname): ?>
        <option value="<?php echo $staffid ?>"><?php echo $staffname ?></option>
    <?php endforeach; ?>
    </select>
</div>


<div class="form-group dynamic-form-group dynamic-stafftype-group" id="assigntogroupFormGroup">
    <label for="assigntogroup" class="control-label">Select Staffs</label>
    <select name="assigntogroup[]" id="assigntogroup" class="form-control selectpicker" data-live-search="true" multiple>
    <?php foreach($staffs as $staffid => $staffname): ?>
        <option value="<?php echo $staffid ?>"><?php echo $staffname ?></option>
    <?php endforeach; ?>
    </select>
</div>

<div class="form-group dynamic-form-group dynamic-stafftype-group" id="assigntoroleFormGroup">
    <label for="assigntorole" class="control-label">Select Role</label>
    <select name="assigntorole[]" id="assigntorole" class="form-control selectpicker" multiple>
    <?php foreach($staff_role as $role): ?>
        <option value="<?php echo $role['roleid'] ?>"><?php echo $role['name'] ?></option>
    <?php endforeach; ?>
    </select>
</div>

<div class="form-group dynamic-form-group dynamic-stafftype-group" id="assigntodesignationFormGroup">
    <label for="assigntodesignation" class="control-label">Select Designation</label>
    <select name="assigntodesignation[]" id="assigntodesignation" class="form-control selectpicker" multiple>
    <?php foreach($staff_designation as $designation): ?>
        <option value="<?php echo $designation['designationid'] ?>"><?php echo $designation['name'] ?></option>
    <?php endforeach; ?>
    </select>
</div>

<br>
<button type="submit" class="btn btn-primary" id="saveEmailConfig">Save Configuration</button>
<?php echo form_close(); ?>


<script>
    function reloadByType(type_val){
        $('#ProjectAssignStaffConfig [name="assignto"]').removeAttr('required');
        $('#ProjectAssignStaffConfig [name="stafftype"]').removeAttr('required');
        $('#ProjectAssignStaffConfig [name="assigntogroup[]"]').removeAttr('required');
        $('#ProjectAssignStaffConfig [name="assigntorole[]"]').removeAttr('required');
        $('#ProjectAssignStaffConfig [name="assigntodesignation[]"]').removeAttr('required');
        $('#ProjectAssignStaffConfig  .dynamic-form-group').hide();

        workflowl.resetForm('ProjectAssignStaffConfig');
        $('#ProjectAssignStaffConfig [name="type"]').val(type_val);
        if(type_val =='direct_assign'){
            $('[name="assignto"]').attr('required',true);
            $('#ProjectAssignStaffConfig #assigntoFormGroup').show();
            $('#ProjectAssignStaffConfig [name="assignto"]').attr('required','true');
        }else if(type_val =='round_robin_method'){
            $('[name="stafftype"]').attr('required',true);
            $('#ProjectAssignStaffConfig #stafftypeFormGroup').show();
            $('#ProjectAssignStaffConfig #assigntogroupFormGroup').show();
        }else if(type_val =='having_less_no_of_projects'){
            $('[name="stafftype"]').attr('required',true);
            $('#ProjectAssignStaffConfig #stafftypeFormGroup').show();
            $('#ProjectAssignStaffConfig #assigntogroupFormGroup').show();
        }else if(type_val =='having_more_wins'){
            $('[name="stafftype"]').attr('required',true);
            $('#ProjectAssignStaffConfig #stafftypeFormGroup').show();
            $('#ProjectAssignStaffConfig #assigntogroupFormGroup').show();
        }else{
            $('#ProjectAssignStaffConfig #assigntoFormGroup').hide();
        }
    }

    function reloadByStaffType(type_val){
        $('#ProjectAssignStaffConfig [name="assigntogroup[]"]').removeAttr('required');
        $('#ProjectAssignStaffConfig [name="assigntorole[]"]').removeAttr('required');
        $('#ProjectAssignStaffConfig [name="assigntodesignation[]"]').removeAttr('required');
        $('#ProjectAssignStaffConfig .dynamic-stafftype-group').hide();
        if(type_val =='staff'){
            $('[name="assigntogroup[]"]').attr('required',true);
            $('#ProjectAssignStaffConfig [name="assigntogroup[]"]').attr('required','true');
            $('#ProjectAssignStaffConfig #assigntogroupFormGroup').show();
        }else if(type_val =='roles'){
            $('[name="assigntorole[]"]').attr('required',true);
            $('#ProjectAssignStaffConfig [name="assigntorole[]"]').attr('required','true');
            $('#ProjectAssignStaffConfig #assigntoroleFormGroup').show();
        }else if(type_val =='designation'){
            $('[name="assigntodesignation[]"]').attr('required',true);
            $('#ProjectAssignStaffConfig [name="assigntodesignation[]"]').attr('required','true');
            $('#ProjectAssignStaffConfig #assigntodesignationFormGroup').show();
        }
        $('#ProjectAssignStaffConfig #assigntoFormGroup').hide();
    }

    document.addEventListener("DOMContentLoaded", function(event) {
        $('#ProjectAssignStaffConfig .dynamic-form-group').hide();
        $('#ProjectAssignStaffConfig #assigntoFormGroup').show();
        $('#ProjectAssignStaffConfig [name="type"]').change(function(){
            var type_val =$(this).val();
            reloadByType(type_val);
            if(type_val !='direct_assign')
                $('#ProjectAssignStaffConfig [name="stafftype"]').trigger('change');
        });

        $('#ProjectAssignStaffConfig [name="stafftype"]').change(function(){
            var type_val =$(this).val();
            reloadByStaffType(type_val);
        })
        appValidateForm($('#ProjectAssignStaffConfig'),
            {
                
            },
            function(form) {
                $.ajax({
                    url: admin_url+'workflow/saveconfig/'+$('.tree .block.selected').attr('data-id'),
                    type: form.method,
                    data: $(form).serialize(),
                    success: function(response) {
                        description =`Assign owner to project. <b>`+$('#ProjectAssignStaffConfig [name="type"] option[value="'+$('#ProjectAssignStaffConfig [name="type"]').val()+'"]').html()+`</b>`;
                        workflowl.updateBlockContent($('.tree .block.selected').attr('data-id'),'',description);
                        alert_float('success', 'Setup saved successfully.');
                    }            
                });
            }
        );
    })
    
</script>