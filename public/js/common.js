var tag_page = 1;
var serch_flag = 0;
var message_notification = 0;
$(document).ready(function(){
	 
	$("#user_login").submit(function(e){		 
		e.preventDefault();
		$(".login-butn").html("<img src='"+baseurl+"/public/images/ajax_loader.gif' />");
		$(".login_error").html('');
		var url = $("#user_login").attr("action");
		$.ajax({
		type: "POST",
		url:url,
		data:$("#user_login").serialize(),
		dataType: "json", 
		success:function(result) {
			if(result.error){
				$(".login-butn").html('<input type="image" src="'+baseurl+'/public/images/signin-butn.png" alt="Login" />');
				$(".login_error").html(result.msg);
			}
			else{
				window.location.reload();
			}
		}});
	});
	$("#user_profile_country_id").change(function(){
		var country_id = $(this).val();
		var url = baseurl+'/city/cities';
		 $(".signup-location-right").html("<img src='"+baseurl+"/public/images/ajax_loader.gif' />");
		$.ajax({
		type: "POST",
		url:url,
		data:{country_id:country_id},		
		success:function(result) {
			 $(".signup-location-right").html(result);
			$('#user_profile_city').customSelect();			 
		}});		
	});
	$(document).on("click","#tag_list .add-option",function(){
		var selected_tag_id = $(this).attr('id');
		var selected_tag	= $(this).text();
		var search_string = $("#tag_search").val();
		$(this).remove();
		$("#added_items").append('<a class="add-option" href="javascript:void(0)" id="'+selected_tag_id+'">'+selected_tag+'</a>');
		var url = baseurl+'/user/ajaxgettag';
		$.ajax({
		type: "POST",
		url:url,
		data:{page:tag_page,search_string:search_string},		
		success:function(result) {
			$("#tag_list").append(result);
			tag_page=tag_page+1;
		}});
	});
	
	$(document).on("click","#added_items .add-option",function(){
		$(this).remove();
	});
	$(document).on("click","#btn_search_tag",function(){
		var url = baseurl+'/user/tagsearch';
		var search_string = $("#tag_search").val();
		$.ajax({
		type: "POST",
		url:url,
		data:{search_string:search_string},		
		success:function(result) {
			$("#tag_list .add-option").remove();
			$("#tag_list").append(result);
			tag_page=1;
		}});
	});
	$(document).on("click","#tag_submit",function(){
		var id = $("#added_items .add-option").attr('id').substring(1);
		var tags = new Array();
		var i=0;
		$.each($('#added_items .add-option'), function() {
			tags[i] = this.id ;i++;
		});
		var str_tag = tags.join('~');
		var url = baseurl+'/user/register';
		 var form = document.createElement("form");
			$(form).attr("action", url)
				   .attr("method", "post");
			$(form).html('<input type="hidden" name="tags" value="' + str_tag + '" />');
			document.body.appendChild(form);
			$(form).submit();
			document.body.removeChild(form);
	}); 
	$(".message_list").click(function(e) {  
		e.preventDefault();
		$("#message_menu").toggle();
		$("#connection_menu").hide();
		$("#signin_menu").hide();	
		if(message_notification==0){
		$("#message_menu").html("<img src='"+baseurl+"/public/images/ajax_loader.gif' />");
		var url = baseurl+'/messages/ajaxGetMessageNotification';
		$.ajax({
		type: "POST",
		url:url,
		data:'',		
		success:function(result) {
			if(result){
				$("#message_menu").html(result);
				message_notification = 1;
				$("#msgs_count").html('');
				$("#top_msg_count").hide();
			}
		}});
		}
		$(".signin").toggleClass("menu-open");
		$("#message-noti").addClass("active");
	});			
	$("#message_menu").mouseup(function() {
		return false
	});
	$(document).mouseup(function(e) {
		if($(e.target).parent("a.signin").length==0) {
			$(".message_list").removeClass("menu-open");
			$("#message_menu").hide();
			$("#message-noti").removeClass("active");
		}
	});	
	$(document).on("click",".connection_list",function(){
		var url = baseurl+'/user/ajaxNewConnectionRequests';	
		$("#message_menu").hide();
		$("#signin_menu").hide();	
		$("#connection_menu").toggle();		 
		$("#connection_menu").html("<img src='"+baseurl+"/public/images/ajax_loader.gif' />");		
		$.ajax({
		type: "POST",
		url:url,
		data:'',		
		success:function(result) {
			if(result){
				$("#connection_menu").html(result);
				 
			}else{
				$("#connection_menu").html("No more requests available now");
			}			
		}});
	});
	$(document).on("click",".notification_list",function(){
		var url = baseurl+'/notifications/ajaxGetUserNotificationList';	
		 
		$("#message_menu").hide();
		$("#connection_menu").hide();
		$("#signin_menu").toggle();		 
		$(".notification-list").html("<img src='"+baseurl+"/public/images/ajax_loader.gif' />");		
		$.ajax({
		type: "POST",
		url:url,
		data:'',		
		success:function(result) {
			if(result){
				$(".notification-list").html(result);
				 $(".all-notifc-number").hide();
			}else{
				$(".notification-list").html("No more requests available now");
			}			
		}});
	});
	$(document).on("click",".add_friend",function(){
		var id = $(this).attr("id");
		var arr_id = id.split("_"); 
		var current_id = arr_id[1];
		var url = baseurl+'/user/acceptFriendRequest';		 	
		$.ajax({
		type: "POST",
		url:url,
		data:{'user':current_id},
		dataType:"json",		
		success:function(result) {
			if(result.error){
				$("#connection-action-"+current_id).html(result.msg);
				 
			}else{
				$("#connection-action-"+current_id).html(result.msg);
			}			
		}});		
	});
	$(document).on("click",".decline",function(){
		var id = $(this).attr("id");
		var arr_id = id.split("_"); 
		var current_id = arr_id[1];
		var url = baseurl+'/user/declineFriendRequest';		 	
		$.ajax({
		type: "POST",
		url:url,
		data:{'user':current_id},
		dataType:"json",		
		success:function(result) {
			if(result.error){
				$("#connection-action-"+current_id).html(result.msg);
				 
			}else{
				$("#connection-action-"+current_id).html(result.msg);
			}			
		}});		
	});
	$(document).on("keyup","#common_search",function(event){
		event.stopPropagation();
		$("#quick_search").show();
		var search_str  = $("#common_search").val();
		var url = baseurl+'/application/quicksearch';		 	
		$.ajax({
		type: "POST",
		url:url,
		data:{'search_str':search_str},		 		
		success:function(result) {
			 
				$("#quick_search").html(result);
				 
			  			
		}});
	});
	$(document).on('mouseenter',"#quick_search", function(){
		serch_flag = 1; 
	});
	$(document).on('mouseleave',"#quick_search", function(){ 
		serch_flag = 0; 
	});
	$("html").click(function(){ 
		if (!serch_flag) {
			 $("#quick_search").hide();
		}
	});
	
	 
	 
	 
	$(document).on("click",".quick_add_friend",function(){	
		var id = $(this).attr("id");
		var user = id.replace('quick_add_friend_','');
		$("#quick_search").show();
		url = baseurl +'/user/friendRequest';
		$.ajax({   
			type: "POST",
			data : {"user":user},			 
			url: url,   
			dataType:"json",
			success: function(result){
				  $("#quick_connection-status_"+user).html(result.msg);
			                     
			} 
	});
	});
	$(document).on("click",".quick_accept_friend",function(){	
		var id = $(this).attr("id");
		$("#quick_search").show();
		var user = id.replace('quick_accept_friend_','');
		url = baseurl +'/user/acceptFriendRequest';
		$.ajax({   
			type: "POST",
			data : {"user":user},			 
			url: url,   
			dataType:"json",
			success: function(result){
				  $("#quick_connection-status_"+user).html(result.msg);
			                     
			} 
	});
	});
	$(document).on("mouseenter","#email_alert img",function(){
		$("#email_alert_messsage").show();
	});
	$(document).on("mouseout","#email_alert img",function(){
		$("#email_alert_messsage").hide();
	});
});
function loadNewMessages(){
	var url = baseurl+'/messages/ajaxMessageCount';		 
		$.ajax({
		type: "POST",
		url:url,
		data:'',		
		success:function(result) {
			if(result!=0){
				$("#msgs_count").html(result);
				$("#top_msg_count").html(result);
				 
			}
			else{
			$("#top_msg_count").hide();
			}
		}});
}
function ajaxGetConnectionCount(){
	var url = baseurl+'/user/ajaxGetConnectionCount';		 
		$.ajax({
		type: "POST",
		url:url,
		data:'',		
		success:function(result) {
			if(result!=0){
				$(".notifc-number").html(result);			 
			}
			else{
				$(".notifc-number").hide();
			}
		}});
}
function ajaxGetNotificationCount(){
	var url = baseurl+'/notifications/ajaxGetNotificationCount';		 
	$.ajax({
	type: "POST",
	url:url,
	data:'',		
	success:function(result) {
		if(result!=0){
			$(".all-notifc-number").html(result);			 
		}
		else{
			$(".all-notifc-number").hide();
		}
	}});
}