<script>
    function add_content(uid,folder) {
        document.getElementById('overlay').style.display = '';
        $.post(admin_url + 'company_mail/content', {
                uid: uid,
				folder: folder
            },
            function(data, status) {
                var json = $.parseJSON(data);
                $('.ch_files_f').html('');
                $('#f_files').html('');
                $('#forward_toemail').val('');
                $('#forward_ccemail').val('');
                $('#forward_bccemail').val('');
                $('#ftotcnt').val(1);
                $('#ffilecnt').val(1);
                $('#fallcnt').val(0);
                $('#f_file').val('');
                // check_email('', 'forward_toemail');
                $('#f_getFile').val('');
                $('#forward_subject').val('Fwd: ' + json.subject);
				if(json.message){
					tinyMCE.get('forward_description').setContent(json.message);
				}else{
					tinyMCE.get('forward_description').setContent('');
				}
				tinymce.get('reply_description').theme.resizeTo("100%", "300px");
                $('#forward-modal').modal('show');
                document.getElementById('overlay').style.display = 'none';

            });
    }

    function add_to(uid,folder) {
        document.getElementById('overlay').style.display = '';
        $.post(admin_url + 'company_mail/to_mail', {
                uid: uid,
				folder: folder
            },
            function(data, status) {
                var json = $.parseJSON(data);
                $('#reply_toemail').val(json.from_address);
                $('#ch_uid').val(json.message_id);
                $('#reply_subject').val('Re: ' + json.subject);
                $('.ch_files_r').html('');
                $('#r_files').html('');
                $('#reply_ccemail').val('');
                $('#reply_bccemail').val('');
                $('#ftotcnt').val(1);
                $('#rfilecnt').val(1);
                $('#rallcnt').val(0);
                $('#r_file').val('');
                $('#reply-modal [name="rel_type"]').val(json.rel_data.rel_type);
                $('#reply-modal [name="rel_id"]').val(json.rel_data.rel_id);
                $('#reply-modal [name="parent_id"]').val(json.rel_data.parent_id);
                tinyMCE.get('reply_description').setContent('<blockquote style="border-left: 2px solid #ccc; padding-left: 10px;">'+json.message+'</blockquote><br><br>');
				tinymce.get('reply_description').theme.resizeTo("100%", "300px");
                $('#r_getFile').val('');
                $('#reply-modal').modal('show');
                document.getElementById('overlay').style.display = 'none';

            });
    }

    function add_reply_all(uid,folder) {
        document.getElementById('overlay').style.display = '';
        $.post(admin_url + 'company_mail/add_reply_all', {
                uid: uid,
				folder: folder
            },
            function(data, status) {
                var json = $.parseJSON(data);
                $('#reply_toemail').val(json.to_address);
                $('#ch_uid').val(json.message_id);
                $('#reply_subject').val('Re: ' + json.subject);

                $('.ch_files_r').html('');
                $('#r_files').html('');
                $('#reply_ccemail').val(json.cc);
                $('#reply_bccemail').val('');
                $('#ftotcnt').val(1);
                $('#rfilecnt').val(1);
                $('#rallcnt').val(0);
                $('#r_file').val('');
				tinyMCE.get('reply_description').setContent('<blockquote style="border-left: 2px solid #ccc; padding-left: 10px;">'+json.message+'</blockquote><br><br>');
				tinymce.get('reply_description').theme.resizeTo("100%", "300px");
                $('#r_getFile').val('');
                $('#reply-modal').modal('show');
                document.getElementById('overlay').style.display = 'none';

            });
    }
</script>