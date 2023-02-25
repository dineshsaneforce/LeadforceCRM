<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<br>
<?php echo form_open(admin_url('workflow/saveconfig/'), array('id' => 'EmailConfig')); ?>

<?php if ($moduleDetails['name'] == 'lead') : ?>
    <div class="form-group">
        <label for="sendto" class="control-label">Send to</label>
        <select name="sendto" id="sendto" class="form-control" data-live-search="true" required>
            <option value="customer">Customer</option>
            <option value="staff">Assigned Staff</option>
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

<?php echo render_input('subject', 'template_subject', '', 'text', ['data-cursor' => 0]); ?>
<?php echo render_input('fromname', 'template_fromname', '', 'text', ['data-cursor' => 0]); ?>
<hr />
<?php echo render_textarea('message', 'email_template_email_message', isset($configure['message']) ? $configure['message'] : '', array('data-url-converter-callback' => 'myCustomURLConverter', 'id' => 'EmailConfigMessage'), array(), '', 'tinymce tinymce-manual'); ?>
<br>
<button type="submit" class="btn btn-primary" id="saveEmailConfig">Save Configuration</button>
<?php echo form_close(); ?>
<script>
    var placeholderSubjectCursorPos = 0;
    document.addEventListener("DOMContentLoaded", function(event) {

        var placeholdershtml = workflowl.getPlaceHolderPicker();

        $('#EmailConfig [name="subject"]').parent().append(`<div class="btn-group placeholder-picker" data-targer-input="#EmailConfig #subject" style="width:100%" id="placeholderSubject">` + placeholdershtml + `<div>`);
        $('#EmailConfig [name="fromname"]').parent().append(`<div class="btn-group placeholder-picker" data-targer-input="#EmailConfig #fromname" style="width:100%" id="placeholderFromname">` + placeholdershtml + `<div>`);
        $('#EmailConfig [name="message"]').parent().append(`<div class="btn-group placeholder-picker" style="width:100%" id="placeholderMessage">` + placeholdershtml + `<div>`);


        $('#EmailConfig [name="subject"]').blur(function() {
            $(this).attr('data-cursor', $(this).prop('selectionStart'));
        });


        $('#EmailConfig [name="fromname"]').blur(function() {
            $(this).attr('data-cursor', $(this).prop('selectionStart'));
        });

        $('#EmailConfig #placeholderMessage .click-to-copy').click(function() {
            tinymce.activeEditor.execCommand('mceInsertContent', false, $(this).attr('data-placeholder'));
        });

        init_editor('#EmailConfig [name="message"]', {
            urlconverter_callback: 'merge_field_format_url',
            setup: function(ed) {
                ed.on("change", function() {
                    tinymce.triggerSave();
                })
            },
        });

        $('#EmailConfig [name="sendto"]').change(function(){
            var type_val =$(this).val();
            if(type_val =='other_staffs'){
                $('#EmailConfig #otherStaffGroup').show();
                $('#EmailConfig #otherStaffGroup').attr('required','true');
            }else{
                $('#EmailConfig #otherStaffGroup').hide();
                $('#EmailConfig #otherStaffGroup').attr('required','false');
            }
                
        });
        
        appValidateForm($('#EmailConfig'), {
                subject: 'required',
                fromname: 'required',
                message: 'required'
            },
            function(form) {
                $.ajax({
                    url: admin_url + 'workflow/saveconfig/' + $('.tree .block.selected').attr('data-id'),
                    type: form.method,
                    data: $(form).serialize(),
                    success: function(response) {
                        var title = '';
                        if ($('#EmailConfig [name="sendto"]').val() == 'customer') {
                            title = 'Send to customer';
                        } else if ($('#EmailConfig [name="sendto"]').val() == 'staff') {
                            title = 'Send to staff';
                        }else if ($('#EmailConfig [name="sendto"]').val() == 'followers') {
                            title = 'Send to followers';
                        }else if ($('#EmailConfig [name="sendto"]').val() == 'manager') {
                            title = 'Send to manager';
                        }
                        var description = $('#EmailConfig [name="subject"]').val();
                        workflowl.updateBlockContent($('.tree .block.selected').attr('data-id'), title, description);
                        alert_float('success', 'Setup saved successfully.');
                    }
                });
            }
        );
    });
</script>