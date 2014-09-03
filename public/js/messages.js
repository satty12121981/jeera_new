var messagefiles= new Array();;
var uploadfiles = new Array();;
var reciever_id=0;
var message_page_count = 1;
scroller = "message";
var delete_conversation = 0;
$(document).ready(function(){
	$(document).on("click",".listMessage",function(){
		message_page_count = 1;
		var user = $(this).attr("id");
		var url = baseurl+'/messages/messageslist';
		$(".msg-list-outer").removeClass("open-message");
		$("#msg-list-outer"+user).addClass("open-message");
		$(".message-right").html('<img src="'+baseurl+'/public/images/ajax_loader.gif" />');
		if(user!=''){
			$.ajax({
				type: "POST",
				url: url,
				data: {'user': user},
				success: function(data) {
					$(".message-right").html("");
					$(".message-right").html(data);
					reciever_id = user;
				}
			});
		}
	});
	$(document).on("click","#add_file a",function(){
		$(this).parent().find('input').click();
	});
	$(document).on("click","#add_photo a",function(){
		$(this).parent().find('input').click();
	});
	$(document).on("change","#message_photo",function(event){
		messagefiles = event.target.files;
		input = document.getElementById("message_photo");
		     if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function(e) {
					var content = '<img src="'+ e.target.result+'" width="150px" />'
					$("#attachments").append(content);
                   // $('#blah').attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]);
			}
		 
	});
	$(document).on("change","#message_file",function(event){
		uploadfiles = event.target.files;
		input = document.getElementById("message_photo");
		     if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function(e) {
					var content ='<div>'+$("#message_file").val()+'</div>';
					$("#attachments").append(content);
                   // $('#blah').attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]);
			}
		 
	});
	$(document).on("click","#send_message",function(event){
	//	var formData = $("#messagefrm").serialize();
	event.stopPropagation();
	   event.preventDefault();
		var formData = new FormData();
		var i = 0; 
		$.each(messagefiles, function(key, value)
		{
			formData.append(i, value);i++;
		});
		 $.each(uploadfiles, function(key, value)
		{
			formData.append(i, value);i++;
		});
		formData.append("messsage",$("#txt_message").val());
		formData.append("reciever_id",reciever_id);
		 //formData.append( "message_file", $("#message_file")[0].files[0]);
		 var url = baseurl+'/messages/messagesend';
		 $.ajax({
				type: "POST",
				url: url,
				data: formData,
				processData: false, 
				contentType: false,
				success: function(data) {				
				$("#message_lists").append(data);
					messagefiles = new Array();
					uploadfiles = new Array();
					$("#txt_message").val('');
					$("#attachments").html('');
					$('#message_lists').scrollTop($('#message_lists')[0].scrollHeight);
				}
			});
	});
	$(document).on("click","#clear_conversation",function(event){ 
		var url = baseurl+'/messages/messagesdelete';
		
		$.ajax({
			type: "POST",
			url: url,
			data: {"user":reciever_id,"type":"All"},	
			dataType:"json",			
			success: function(data) {				
			$("#message_lists").append(data);
				alert(data.msg);			
				 if(!data.error){
					 $('#message_lists').html('');
				 }
				  
			}
		});
	});
	$(document).on("click","#delete_conversation",function(event){ 
		if(delete_conversation==0){
			$(".message_select").show();
			delete_conversation = 1;
		}else{
			var url = baseurl+'/messages/messagesdelete';
			var messages = [];
			 $('#message_lists :checked').each(function() {
				   messages.push($(this).val());
				 });
			$.ajax({
				type: "POST",
				url: url,
				data: {"user":reciever_id,"type":"Selected",'messages':messages},
				dataType:"json",
				success: function(data) {	
					alert(data.msg);			
					if(!data.error){
						  $.each(messages, function(key, value)
						 {
							$("#message-container_"+value).remove();
						 });
						 delete_conversation = 0;
						 $(".message_select").hide();
					 } 
				}
		});
		}
	});
	$(document).on("keyup","#msg_user_search",function(event){
		var url = baseurl+'/messages/usersearch';
		var search_string = $("#msg_user_search").val();
		$("#srch_friends").html('<img src="'+baseurl+'/public/images/ajax_loader.gif" />');
		$("#srch_friends").show();
		$.ajax({
				type: "POST",
				url: url,
				data: {"search_string":search_string},				 
				success: function(result) {	
					$("#srch_friends").html(result);
				}
		});
	});
	$(document).on("click",".friends_list",function(event){ 
		var list = $(this).html();
		$("#srch_friends").html('');
		$("#srch_friends").hide();
		message_page_count = 1;
		var user = $(this).attr("id");
		$(".message-list #msg-list-outer"+user).remove();
		$(".message-list").prepend(list);
		var url = baseurl+'/messages/messageslist';
		$(".msg-list-outer").removeClass("open-message");
		$("#msg-list-outer"+user).addClass("open-message");
		$(".message-right").html('<img src="'+baseurl+'/public/images/ajax_loader.gif" />');
		if(user!=''){
			$.ajax({
				type: "POST",
				url: url,
				data: {'user': user},
				success: function(data) {
					$(".message-right").html("");
					$(".message-right").html(data);
					reciever_id = user;
				}
			});
		}
	});
	$(document).on("mouseenter",".message-list",function(event){ 
		scroller = "user";
	});
	$(document).on("mouseenter","#message_lists",function(event){ 
		scroller = "message";
	});
	 
});
