<?php defined('BASEPATH') or exit('No direct script access allowed');?>
<style>
  #note-add-input .dx-htmleditor-content{
    height: 100px;
  }
  .dx-mention{
    cursor: pointer;
  }
</style>
<?php echo form_open(admin_url('projects/save_note/'.$project->id)); ?>
<div class="dx-viewport mbot15">
    <div id="note-add-input"></div>
</div>
<input type="hidden" name="content" id="content">
<input type="hidden" name="mentions" id="mentions">
<button type="submit" id="addprojnote" class="btn btn-info"><?php echo _l('project_save_note'); ?></button>
<?php echo form_close(); ?>
<div class="clearfix"></div>
<div class="mtop25"></div>
<div class="modal fade edit_note" id="edit_note" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title"><?php echo _l('edit_note'); ?></h4>
        </div>
        <form name="editnote" id="editnote" method="post" action="<?php echo admin_url('projects/edit_note/'.$project->id)?>">
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <p class="bold"><?php echo _l('project_notes'); ?></p>
                        <div class="form-group"><textarea id="editcontent" name="content" class="form-control" rows="4" placeholder="Add Description"></textarea></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <a href="#" class="btn btn-info" onclick="project_submit_note(this); return false;"><?php echo _l('save_note'); ?></a>
            </div>
            <input type="hidden" name="id" id="noteid" value="">
            <input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
        </form>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<div class="clearfix"></div> 
<table class="table dt-table scroll-responsive table-project-files" data-order-col="3" data-order-type="desc">
  <thead>
    <tr>
      <th><?php echo _l('project_notes_created_by'); ?></th>
      <th><?php echo _l('project_notes'); ?></th>
      <th><?php echo _l('project_notes_date'); ?></th>
      <th><?php echo _l('options'); ?></th>
    </tr>
  </thead>
  <tbody>
    <?php
    //pre($notes);
    foreach($notes as $note){
      ?>
      <tr>
       
        <td data-order="<?php echo $note['fullname']; ?>">
                <?php echo $note['fullname']; ?>
        </td>
          <td ><?php echo $note['content']; ?></td>
          <td data-order="<?php echo $note['dateadded']; ?>"><?php echo _dt($note['dateadded']); ?></td>
         
         <td>
            <a href="#" onclick="edit_notes(<?php echo $note['id']; ?>); return false;" class="btn btn-info btn-icon"><i class="fa fa-edit"></i></a>
           <?php if($file['staffid'] == get_staff_user_id() || has_permission('projects','','delete')){ ?>
           <a href="<?php echo admin_url('projects/remove_notes/'.$project->id.'/'.$note['id']); ?>" class="btn btn-danger btn-icon _delete"><i class="fa fa-remove"></i></a>
           <?php } ?>
         </td>
       </tr>
       <?php } ?>
     </tbody>
   </table>

   <script>
    document.addEventListener("DOMContentLoaded", function(event) {
      var editor = $("#note-add-input").dxHtmlEditor({
          mentions: [{
              dataSource: employees,
              searchExpr: "text",
              displayExpr: "text",
              valueExpr : "id",
          }],
          
      }).dxHtmlEditor("instance");
      
      editor.insertEmbed(0, "")
      $('#addprojnote').on('click',function(){
        var mentions =[]
        $('#note-add-input .dx-htmleditor-content .dx-mention').each(function(i, obj) {
          mentions.push($(this).data('id'));
        });

        var uniqueMentions = mentions.filter(function(value, index, self) {
        return self.indexOf(value) === index;
      });

        $('#mentions').val(uniqueMentions);
        var htmlEditor = $('#note-add-input').dxHtmlEditor('instance');
        $('#content').val(htmlEditor.option('value'));
      });


      $('.dx-mention').on("click",function(e){
        window.open("<?php echo admin_url('staff/member/') ?>"+$(this).data('id'), '_blank');
      });
  });


  var employees = [
    <?php if($toplevelstaffs): ?>
      <?php foreach ($toplevelstaffs as $pikay => $pival): ?>
      <?php if($pival['staffid'] != get_staff_user_id()): ?>
        { 
          id: <?php echo $pival['staffid'] ?>,
          text: "<?php echo $pival['firstname'] . ' ' . $pival['lastname'] ?>",
        },
      <?php endif; ?>
      <?php endforeach; ?>
    <?php endif; ?>
    <?php if($project_members): ?>
      <?php foreach ($project_members as $pikay => $pival):?>
        <?php if($pival['staff_id'] != get_staff_user_id()): ?>
        { 
          id: <?php echo $pival['staff_id'] ?>,
          text: "<?php echo $pival['firstname'] . ' ' . $pival['lastname'] ?>",
        },
      <?php endif; ?>
      <?php endforeach; ?>
    <?php endif; ?>
  ];
   </script>