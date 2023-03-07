<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php $templates =$this->sms_model->getTemplates(); ?>
<br>
<?php echo form_open(admin_url('workflow/saveconfig/'), array('id' => 'SMSConfig')); ?>
<?php if ($moduleDetails['name'] == 'lead') : ?>
    <div class="form-group">
        <label for="sendto" class="control-label">Send to</label>
        <select name="sendto" id="sendto" class="form-control" data-live-search="true" required>
            <option value="customer">Customer</option>
            <option value="staff">Assigned Staff</option>
            <option value="other_staffs">Other Staffs</option>
        </select>
    </div>
<?php elseif ($moduleDetails['name'] == 'project') : ?>
    <div class="form-group">
        <label for="sendto" class="control-label">Send to</label>
        <select name="sendto" id="sendto" class="form-control" data-live-search="true" required>
            <option value="staff">Owner</option>
            <option value="followers">Followers</option>
            <option value="manager">Manager</option>
            <option value="other_staffs">Other Staffs</option>
        </select>
    </div>
<?php endif; ?>

<div class="form-group dynamic-form-group" id="otherStaffGroup" style="display:none">
    <label for="other_staffs_group" class="control-label">Select Staffs</label>
    <select name="other_staffs_group[]" id="other_staffs_group" class="form-control selectpicker" data-live-search="true" multiple>
    <?php foreach($staffs as $staffid => $staffname): ?>
        <option value="<?php echo $staffid ?>"><?php echo $staffname ?></option>
    <?php endforeach; ?>
    </select>
</div>
<div class="form-group">
    <label for="template" class="control-label">Template</label>
    <select name="template" id="template" class="form-control selectpicker" data-live-search="true" required>
        <option value="">Select Template</option>
        <?php if($templates): ?>
            <?php foreach($templates as $template): ?>
                <option value="<?php echo $template->template_id ?>"><?php echo $template->name ?></option>
            <?php endforeach; ?>
        <?php endif; ?>
    </select>
</div>
<div class="form-group">
    <label for="message" class="control-label"><?php echo _l('message') ?></label>
    <textarea id="message" name="message" class="form-control" rows="8" disabled></textarea>
</div>

<div id="bodyVariables">
</div>

<br>
<button type="submit" class="btn btn-primary" id="saveSMSConfig">Save Configuration</button>
<?php echo form_close(); ?>
<script>

function updateSMSTemplateDetails() {
        savedVariables =workflowl.getSmsVariables();
        var templateId = $('#SMSConfig #template').val();
        if (templateId) {
            $.ajax({
                url: '<?php echo admin_url('integration/sms/getTemplate') ?>/' + templateId,
                type: "get",
                dataType: "json",
                success: function(response) {
                    if (response.success == true) {
                        $('#SMSConfig #bodyVariables').html('');
                        for (let index = 1; index <= response.data.variables; index++) {
                            let variableValue = '';
                            if (savedVariables && typeof savedVariables[index - 1] !='undefined') {
                                variableValue = savedVariables[index - 1];
                            }

                            var placeholderpicker = `<div class="btn-group placeholder-picker" data-targer-input="#variable_` + index + `" style="width:100%">` + workflowl.getPlaceHolderPicker() + `<div>`;

                            $('#SMSConfig #bodyVariables').append(`<div class="form-group">
                            <label for="variable_` + index + `" class="control-label">Variable {{` + index + `}}</label>
                            <input type="text" id="variable_` + index + `" value="` + variableValue + `" name="variable_` + index + `" class="form-control variablesfield">
                        ` + placeholderpicker + `</div>`);
                            $('#SMSConfig #variable_' + index).attr('required', "");
                        }
                        $('#SMSConfig #message').val(response.data.content);
                    } else {
                        alert_float('warning', response.msg);
                    }
                },
            });
        }
    }

    document.addEventListener("DOMContentLoaded", function(event) {
        $('#SMSConfig [name="sendto"]').change(function(){
            var type_val =$(this).val();
            if(type_val =='other_staffs'){
                $('#SMSConfig #otherStaffGroup').show();
                $('#SMSConfig #otherStaffGroup').attr('required','true');
            }else{
                $('#SMSConfig #otherStaffGroup').hide();
                $('#SMSConfig #otherStaffGroup').attr('required','false');
            }
                
        });
        appValidateForm(
            $('#SMSConfig'),
            {},
            function(form) {
                var sendto = $('#SMSConfig #sendto').val();
                var template = $('#SMSConfig #template').val();
                var other_staffs_group = $('#SMSConfig #other_staffs_group').val();
                var variables = [];
                var variablesfield = $('#SMSConfig .variablesfield');
                $.each(variablesfield, function() {
                    variables.push($(this).val());
                });
                $('#SMSConfig .sms_form_errors').remove();
                $.ajax({
                    url: admin_url+'workflow/saveconfig/'+$('.tree .block.selected').attr('data-id'),
                    type: "post",
                    data: {
                        template: template,
                        variables: variables,
                        sendto: sendto,
                        other_staffs_group:other_staffs_group,
                    },
                    dataType: "json",
                    success: function(response) {
                        if (response.success == true) {
                            var title ='';
                            if($('#SMSConfig [name="sendto"]').val() =='customer'){
                                title ='Send to customer';
                            }else if($('#SMSConfig [name="sendto"]').val() =='staff'){
                                title ='Send to staff';
                            }else if ($('#SMSConfig [name="sendto"]').val() == 'followers') {
                                title = 'Send to followers';
                            }else if ($('#SMSConfig [name="sendto"]').val() == 'manager') {
                                title = 'Send to manager';
                            }

                            var description =$('#SMSConfig #template option[value="'+template+'"]').html();
                            workflowl.updateBlockContent($('.tree .block.selected').attr('data-id'),title,'Template : <b>'+description+'<b>');

                            alert_float('success', 'Setup saved successfully.');
                        } else {
                            alert_float('warning', response.msg);
                            $.each(response.errors, function(k, v) {
                                $('#SMSConfig [name="' + k + '"]').parent().append(`<p class="text-danger sms_form_errors">` + v + `</p>`)
                            });
                        }
                    },
                });
            }
        );

       $('#SMSConfig [name="template"]').change(function(){
            updateSMSTemplateDetails();
       }); 
    });
</script>
