<div class="modal fade" id="clientid_add_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
<div class="model-wrapper">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button group="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <span class="edit-title"><?php echo _l('add_new', _l('proposal_for_customer')); ?></span>
                </h4>
            </div>
            <?php echo form_open('admin/clients/ajax_client', array('id' => 'clientid_add_group_modal')); ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <?php $attrs = array('autofocus' => true, 'required' => true); ?>
                        <?php echo render_input('company', 'client_company', '', 'text', $attrs); ?>
                        <div id="companyname_exists_info" class="hide"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button group="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button group="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>

            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>
</div>

<div class="modal fade" id="project_contacts_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <button group="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="myModalLabel">
                <span class="edit-title"><?php echo _l('add_new', _l('contact')); ?></span>
            </h4>
        </div>
        <?php echo form_open('admin/clients/form_contact/undefined', array('id' => 'project_contacts_add')); ?>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <?php echo form_hidden('clientid', ''); ?>
                    <?php $attrs = array('autofocus' => true, 'required' => true); ?>
                    <?php echo render_input('firstname', 'client_firstname', '', '', $attrs); ?>
                    <div id="contact_exists_info" class="hide"></div>
                    <?php echo render_input('title', 'contact_position', ''); ?>

                    <div class="form-group" app-field-wrapper="email">

                        <label for="email" class="control-label">Email </label>
                        <div class="input-group">
                            <input type="email" id="email" name="email" class="form-control" value="">
                            <div class="input-group-addon"><span class="add_field_button_ae pointer "><i class="fa fa fa-plus"></i></span></div>
                        </div>
                    </div>

                    <div class="input_fields_wrap_ae">

                    </div>


                    <div class="form-group" app-field-wrapper="phonenumber">
                        <label for="phonenumber" class="control-label">Phone </label>
                        <div class="input-group">
                            <input type="text" id="phonenumber" name="phonenumber" class="form-control" autocomplete="off" value="">
                            <div class="input-group-addon"><span class="add_field_button_ap pointer "><i class="fa fa fa-plus"></i></span></div>
                        </div>
                    </div>

                    <div class="input_fields_wrap_ap">

                    </div>

                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button group="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
            <button group="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>

        </div>
        <?php echo form_close(); ?>
    </div>