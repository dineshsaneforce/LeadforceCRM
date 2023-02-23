<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<br>
<?php echo form_open(admin_url('workflow/saveconfig/'), array('id' => 'ProjectChangeEventConfig')); ?>
<div class="form-group">
    <label for="event" class="control-label">Select Event</label>
    <select name="event" id="event" class="form-control selectpicker" required>
        <option value="name" selected>Deal name updated</option>
        <option value="project_cost" >Deal value updated</option>
        <option value="deadline" >Deal expected closing date updated</option>
        <option value="pipeline_id" >Deal pipeline changed</option>
        <option value="status" >Deal stage changed</option>
        <option value="clientid" >Deal organization changed</option>
        <option value="stage_of_0" >Deal Reopened</option>
        <option value="stage_of_1" >Deal marked as own</option>
        <option value="stage_of_2" >Deal marked as lost</option>
    </select>
</div>

<br>
<button type="submit" class="btn btn-primary" id="saveEmailConfig">Save Configuration</button>
<?php echo form_close(); ?>

<script>
    document.addEventListener("DOMContentLoaded", function(event) {
        appValidateForm($('#ProjectChangeEventConfig'), {

            },
            function(form) {
                $.ajax({
                    url: admin_url + 'workflow/saveconfig/' + $('.tree .block.selected').attr('data-id'),
                    type: form.method,
                    data: $(form).serialize(),
                    success: function(response) {
                        description = `When ` + $('#ProjectChangeEventConfig [name="event"] option[value="' + $('#ProjectChangeEventConfig [name="event"]').val() + '"]').html() + `</b>`;
                        workflowl.updateBlockContent($('.tree .block.selected').attr('data-id'), '', description);
                        alert_float('success', 'Setup saved successfully.');
                    }
                });
            }
        );
    })
</script>