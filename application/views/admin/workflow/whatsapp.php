<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<br>
<?php echo form_open(admin_url('workflow/saveconfig/'), array('id' => 'WhatsappConfig')); ?>
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
    </select>
</div>

<div class="form-whatsapp-header-group">
    <input type="hidden" name="header-format">
    <div class="form-whatsapp-header form-whatsapp-header-text">
        <div class="form-group">
            <label for="header_text" class="control-label"><?php echo _l('header') ?></label>
            <input id="header_text" name="header_text" class="form-control" disabled></input>
        </div>
        <div class="form-group whatsapp-header-variable-holder">
            <label for="header_variable" class="control-label"><?php echo _l('header_variable') ?></label>
            <input id="header_variable" name="header_variable" class="form-control"></input>
        </div>
    </div>
    <div class="form-whatsapp-header form-whatsapp-header-media">
        <div class="form-group">
            <label for="header_media_link" class="control-label"><?php echo _l('media_link') ?></label>
            <input id="header_media_link" name="header_media_link" class="form-control"></input>
        </div>
    </div>
    <div class="form-whatsapp-header form-whatsapp-header-media">
        <div class="form-group">
            <label for="header_media_caption" class="control-label"><?php echo _l('media_caption') ?></label>
            <input id="header_media_caption" name="header_media_caption" class="form-control"></input>
        </div>
    </div>
</div>
<div class="form-group">
    <label for="message" class="control-label"><?php echo _l('message') ?></label>
    <textarea id="message" name="message" class="form-control" rows="8" disabled></textarea>
</div>

<div id="bodyVariables">
</div>

<br>
<button type="submit" class="btn btn-primary" id="saveWhatsappConfig">Save Configuration</button>
<?php echo form_close(); ?>


<script>
    var savedTemplate = '<?php echo isset($configure['template']) ? $configure['template'] : false; ?>';
    var savedVariables = <?php echo isset($configure['variables']) ? json_encode($configure['variables']) : []; ?>;

    function updatewhatsapptemplates() {
        $.ajax({
            url: '<?php echo admin_url('integration/whatsapp/gettemplates') ?>',
            type: "get",
            dataType: "json",
            success: function(response) {
                $("#WhatsappConfig #template option").remove();
                $('#WhatsappConfig #template')
                    .append($("<option></option>")
                        .attr("value", '')
                        .text('Select Template'));
                $.each(response.data, function(key, value) {
                    $('#WhatsappConfig #template')
                        .append($("<option></option>")
                            .attr("value", value.name)
                            .text(value.name));
                });
                $("#WhatsappConfig #template").selectpicker('refresh');
                updateWhatsappTemplateDetails();
            },
        });
    }

    function updateWhatsappTemplateDetails() {
        $('.form-whatsapp-header-group .form-whatsapp-header').hide();
        $('[name="header-format"]').val('');
        $('.whatsapp-header-variable-holder').hide();
        $('[name="header_variable"]').removeAttr('required');
        $('[name="header_media_link"]').removeAttr('required');
        $('[name="header_media_caption"]').removeAttr('required');
        savedVariables =workflowl.getWhatsappVariables();
        var templateName = $('#WhatsappConfig #template').val();
        if (templateName) {
            $.ajax({
                url: '<?php echo admin_url('integration/whatsapp/gettemplate') ?>/' + templateName,
                type: "get",
                dataType: "json",
                success: function(response) {
                    if (response.success == true) {
                        $.each(response.data.components, function(key, value) {
                            if (value.type == 'BODY') {
                                $('#WhatsappConfig #bodyVariables').html('');
                                for (let index = 1; index <= value.variables; index++) {
                                    let variableValue = '';
                                    if (savedVariables && typeof savedVariables[index - 1] !='undefined') {
                                        variableValue = savedVariables[index - 1];
                                    }

                                    var placeholderpicker = `<div class="btn-group placeholder-picker" data-targer-input="#variable_` + index + `" style="width:100%">` + workflowl.getPlaceHolderPicker() + `<div>`;

                                    $('#WhatsappConfig #bodyVariables').append(`<div class="form-group">
                                    <label for="variable_` + index + `" class="control-label">Variable {{` + index + `}}</label>
                                    <input type="text" id="variable_` + index + `" value="` + variableValue + `" name="variable_` + index + `" class="form-control variablesfield">
                                ` + placeholderpicker + `</div>`);
                                    $('#WhatsappConfig #variable_' + index).attr('required', "");
                                }
                                $('#WhatsappConfig #message').val(value.text);
                            }else if(value.type =='HEADER'){
                                $('[name="header-format"]').val(value.format);
                                if(value.format =='TEXT'){
                                    var text =value.text;
                                    if(text.indexOf("{{1}}") !=-1){
                                        $('.whatsapp-header-variable-holder').show();
                                        $('[name="header_variable"]').attr('required',true);
                                    }
                                    $('[name="header_text"]').val(value.text);
                                    $('.form-whatsapp-header-group .form-whatsapp-header.form-whatsapp-header-text').show();
                                }else{
                                    $('[name="header_media_link"]').attr('required',true);
                                    $('[name="header_media_caption"]').attr('required',true);
                                    $('.form-whatsapp-header-group .form-whatsapp-header.form-whatsapp-header-media').show();
                                }
                            }
                        })

                    } else {
                        alert_float('warning', response.msg);
                    }
                },
            });
        }
    }

    document.addEventListener("DOMContentLoaded", function(event) {
        var placeholdershtml = workflowl.getPlaceHolderPicker();
        $('#WhatsappConfig [name="header_media_link"]').parent().append(`<div class="btn-group placeholder-picker" data-targer-input="#header_media_link" style="width:100%">` + workflowl.getPlaceHolderPicker() + `<div>`);
        $('#WhatsappConfig [name="header_media_caption"]').parent().append(`<div class="btn-group placeholder-picker" data-targer-input="#header_media_caption" style="width:100%">` + workflowl.getPlaceHolderPicker() + `<div>`);
        $('#WhatsappConfig [name="header_variable"]').parent().append(`<div class="btn-group placeholder-picker" data-targer-input="#header_variable" style="width:100%">` + workflowl.getPlaceHolderPicker() + `<div>`);
        
        updatewhatsapptemplates();

        $('#WhatsappConfig [name="sendto"]').change(function(){
            var type_val =$(this).val();
            if(type_val =='other_staffs'){
                $('#WhatsappConfig #otherStaffGroup').show();
                $('#WhatsappConfig #otherStaffGroup').attr('required','true');
            }else{
                $('#WhatsappConfig #otherStaffGroup').hide();
                $('#WhatsappConfig #otherStaffGroup').attr('required','false');
            }
                
        });

        appValidateForm(
            $('#WhatsappConfig'),
            {},
            function(form) {
                var sendto = $('#WhatsappConfig #sendto').val();
                var template = $('#WhatsappConfig #template').val();
                var variables = [];
                var contacts = [];
                var header_format =$('[name="header-format"]').val();
                var header_variable = header_media_link = header_media_caption ='';
                if(header_format !=''){
                    // header['format'] =header_format;
                    if(header_format =='TEXT'){
                        header_variable =$('[name="header_variable"]').val();
                    }else{
                        header_media_link =$('[name="header_media_link"]').val();
                        header_media_caption =$('[name="header_media_caption"]').val();
                    }
                }
                var variablesfield = $('#WhatsappConfig .variablesfield');
                $.each(variablesfield, function() {
                    variables.push($(this).val());
                });
                $('#WhatsappConfig .whatsapp_form_errors').remove();
                $.ajax({
                    url: admin_url+'workflow/saveconfig/'+$('.tree .block.selected').attr('data-id'),
                    type: "post",
                    data: {
                        template: template,
                        variables: variables,
                        sendto: sendto,
                        header_format: header_format,
                        header_variable: header_variable,
                        header_media_link: header_media_link,
                        header_media_caption: header_media_caption,
                    },
                    dataType: "json",
                    success: function(response) {
                        if (response.success == true) {
                            var title ='';
                            if($('#WhatsappConfig [name="sendto"]').val() =='customer'){
                                title ='Send to customer';
                            }else if($('#WhatsappConfig [name="sendto"]').val() =='staff'){
                                title ='Send to staff';
                            }else if ($('#WhatsappConfig [name="sendto"]').val() == 'followers') {
                                title = 'Send to followers';
                            }else if ($('#WhatsappConfig [name="sendto"]').val() == 'manager') {
                                title = 'Send to manager';
                            }

                            var description =template;
                            workflowl.updateBlockContent($('.tree .block.selected').attr('data-id'),title,'Template : <b>'+description+'<b>');

                            alert_float('success', 'Setup saved successfully.');
                        } else {
                            alert_float('warning', response.msg);
                            $.each(response.errors, function(k, v) {
                                $('#WhatsappConfig [name="' + k + '"]').parent().append(`<p class="text-danger whatsapp_form_errors">` + v + `</p>`)
                            });
                        }
                    },
                });
            }
        );
        $('#WhatsappConfig #template').change(function() {
            updateWhatsappTemplateDetails();
        });
    });
</script>