 
$(document).ready(function(){
	$(document).on("click",".add_friend",function(){	
		var id = $(this).attr("id");
		var user = id.replace('add_friend_','');
		url = baseurl +'/user/friendRequest';
		$.ajax({   
			type: "POST",
			data : {"user":user},			 
			url: url,   
			dataType:"json",
			success: function(result){
				  $("#connection-status_"+user).html(result.msg);
			                     
			} 
	});
	});
	$(document).on("click",".accept_friend",function(){	
		var id = $(this).attr("id");
		var user = id.replace('accept_friend_','');
		url = baseurl +'/user/acceptFriendRequest';
		$.ajax({   
			type: "POST",
			data : {"user":user},			 
			url: url,   
			dataType:"json",
			success: function(result){
				  $("#connection-status_"+user).html(result.msg);
			                     
			} 
	});
	});
	var  members_page = 1;
	var lastScroll = 0;
	
	var flag=1;
	var result_scrolled = 'Scroll'; 
	 $(window).scroll(function() {
		var st = $(this).scrollTop();
		if(result_scrolled == 'Scroll'){
			if(st > lastScroll){
				if ($('body').height()-100 <= ($(window).height() + $(window).scrollTop())) {
					if(flag){ flag = 0; 
						var url = baseurl+'/groups/'+galexy+'/'+planet+'/ajaxLoadMoreMembers';
						 $(".ajax_loader").html('<img src="'+baseurl+'/public/images/ajax_loader.gif" />');		
						 var search_string = $("#member-search").val();
						$.ajax({
						type: "POST",
						url: url,
						data: {'page': members_page,'search_string':search_string},
						success: function(data) {
						$(".ajax_loader").html("");
							$("#members_list").append(data);
							members_page= members_page+1;
							flag = 1;
						}
						});
					}
				}
			}
		}
		lastScroll = st;
	});
	$(document).on("click",".remove_user",function(){	
		var id = $(this).attr("id");
		var user = id.replace('remove_','');
		url = baseurl+'/groups/'+galexy+'/'+planet+'/ajaxRemoveUser';
		$.ajax({   
			type: "POST",
			data : {"user":user},			 
			url: url,   
			dataType:"json",
			success: function(result){
				  if(!result.success){
					alert(result.msg);
				  }else{
					$("#connection-list-outer"+user).remove();
				  }
			                     
			} 
	});
	});
	$(document).on("click",".suspend_user",function(){	
		var id = $(this).attr("id");
		var user = id.replace('suspend_','');
		url = baseurl+'/groups/'+galexy+'/'+planet+'/ajaxSuspendUser';
		$.ajax({   
			type: "POST",
			data : {"user":user},			 
			url: url,   
			dataType:"json",
			success: function(result){
				  if(!result.success){
					alert(result.msg);
				  }else{
					 $("#suspend_outer_"+user).html('<a href="javascript:void(0)" id="remove_suspensssion_'+user+'" class="remove_suspensssion blue-butn">Remove Suspenssion</a>')
				  }
			                     
			} 
	});
	});
	$(document).on("click",".remove_suspensssion",function(){	
		var id = $(this).attr("id");
		var user = id.replace('remove_suspensssion_','');
		url = baseurl+'/groups/'+galexy+'/'+planet+'/ajaxRemoveSuspension';
		$.ajax({   
			type: "POST",
			data : {"user":user},			 
			url: url,   
			dataType:"json",
			success: function(result){
				  if(!result.success){
					alert(result.msg);
				  }else{
					 $("#suspend_outer_"+user).html('<a href="javascript:void(0)" id="suspend_'+user+'" class="suspend_user blue-butn">Suspend</a>')
				  }
			                     
			} 
	});
	});
	$(document).on("click","#btn_search_members",function(){	
		var id = $(this).attr("id");
		var user = id.replace('remove_suspensssion_','');
		url = baseurl+'/groups/'+galexy+'/'+planet+'/ajaxmemberSearch';
		var search_string  = $("#member-search").val();
		$.ajax({   
			type: "POST",
			data : {'search_string':search_string},			 
			url: url, 		 
			success: function(result){
				 $("#members_list").html(result);		                     
			} 
	});
	});
	$(document).on("click",".view_questionnaire_member",function(event){	
		var id = $(this).attr("id");
		arr_id = id.split("_");
		var user_id = arr_id[2];
		$("#questionnaire_answer_container_"+user_id).animate({  height:'toggle' });
		$("#questionnaire_answer_container_"+user_id).html('<img src="'+baseurl+'/public/images/ajax_loader.gif" />');
		var url = baseurl+'/groups/'+galexy+'/'+planet+'/getUserQuestionnaire';		
		$.ajax({
			type: "POST",
			url: url,
			data:{'user_id':user_id},		 	
			success: function(data) {		   
					$("#questionnaire_answer_container_"+user_id).html(data);			 
			},
			error:function(){
				alert(error);
			}
		});
	});
});