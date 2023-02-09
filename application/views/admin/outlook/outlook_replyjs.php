<script>
    function add_content(msg_id){
	document.getElementById('overlay').style.display = '';
	$.post(admin_url + 'outlook_mail/get_outlook_message',
	{
		msg_id:msg_id
	},
	function(data,status){
		var json = $.parseJSON(data);
		$('.ch_files_f').html('');
		$('#f_files').html('');
		$('#forward_msg_id').val(msg_id);
		$('#forward_toemail').val('');
		$('#forward_ccemail').val('');
		$('#forward_bccemail').val('');
		$('#ftotcnt').val(1);
		$('#ffilecnt').val(1);
		$('#fallcnt').val(0);
		$('#f_file').val('');
		$('#f_getFile').val('');
		$('#forward_subject').val('Fwd: ' + json.subject);
		tinyMCE.get('forward_description').setContent(json.message);
		$('#forward-modal').modal('show');
		document.getElementById('overlay').style.display = 'none'; 
		
	});
}
function add_to(msg_id){
	document.getElementById('overlay').style.display = '';
	$.post(admin_url + 'outlook_mail/get_outlook_message',
	{
		msg_id:msg_id
	},
	function(data,status){
		var json = $.parseJSON(data);
		$('#reply_toemail').val(json.from_address);
		$('#reply_msg_id').val(msg_id);
		$('#ch_uid').val(msg_id);
		$('#reply_subject').val('Re: '+json.subject); 
		$('.ch_files_r').html('');
		$('#r_files').html('');
		$('#reply_ccemail').val('');
		$('#reply_bccemail').val('');
		$('#ftotcnt').val(1);
		$('#rfilecnt').val(1);
		$('#rallcnt').val(0);
		$('#r_file').val('');
		tinyMCE.get('reply_description').setContent('<blockquote style="border-left: 2px solid #ccc; padding-left: 10px;">'+json.message+'</blockquote><br><br>');
		$('#r_getFile').val('');
		$('#reply-modal').modal('show');
		document.getElementById('overlay').style.display = 'none'; 
		
	});
}
function add_reply_all(msg_id){
	document.getElementById('overlay').style.display = '';
	$.post(admin_url + 'outlook_mail/get_outlook_message',
	{
		msg_id:msg_id,
		reply:'all',
	},
	function(data,status){
		var json = $.parseJSON(data);
		$('#reply_msg_id').val(msg_id);
		$('#reply_toemail').val(json.from_address);
		$('#ch_uid').val(msg_id);
		$('#reply_subject').val('Re: '+json.subject); 
		
		$('.ch_files_r').html('');
		$('#r_files').html('');
		$('#reply_ccemail').val(json.cc);
		$('#reply_bccemail').val('');
		$('#ftotcnt').val(1);
		$('#rfilecnt').val(1);
		$('#rallcnt').val(0);
		$('#r_file').val('');
		tinyMCE.get('reply_description').setContent('<blockquote style="border-left: 2px solid #ccc; padding-left: 10px;">'+json.message+'</blockquote><br><br>');
		$('#r_getFile').val('');
		$('#reply-modal').modal('show');
		document.getElementById('overlay').style.display = 'none'; 
		
	});
}
</script>