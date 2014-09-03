
var geo_latitude = 0;
var geo_longitude = 0;
var tag_page = 1;
var group_tage_page = 1;
var comments_page = new Array();
var activity_page=1;
var past_activity_page= 1;
var question = 1;
var comment_content = new Array();
$(document).ready(function(){
	
	
	$(document).on("click","#group_tags_for_activity .add-option",function(){
		var selected_tag_id = $(this).attr('id');
		var selected_tag	= $(this).text();
		var i=0;
		var tags = new Array();
		$.each($('#activty_tags .add-option'), function() {
			tags[i] = this.id ;i++;
		});
		if(i>=3){
			alert("You can add only three tags for an activty.If you want to add different one please remove one of tha tag from esisting");
		}else{
			var search_string = $("#tag_search_for_activity").val();		
			$(this).remove();
			$("#activty_tags #"+selected_tag_id).remove();
			$("#activty_tags").append('<a class="add-option" href="javascript:void(0)" id="'+selected_tag_id+'">'+selected_tag+'</a>');
			var url = baseurl+'/groups/'+galexy+'/'+planet+'/ajaxGroupTags';
			$.ajax({
			type: "POST",
			url:url,
			data:{page:group_tage_page,search_string:search_string},		
			success:function(result) {
				$("#group_tags_for_activity").append(result);
				group_tage_page=group_tage_page+1;
			}});
		}
	});
	$(document).on("click","#activty_tags .add-option",function(){
		$(this).remove();
	});
	$(document).on("click","#clear_all_tags_addactivity",function(){
		$("#activty_tags").html('');
	});
	$(document).on("click","#btn_search_tag_for_activity",function(){
		var url = baseurl+'/groups/'+galexy+'/'+planet+'/ajaxGroupTagSearch';	
		var search_string = $("#tag_search_for_activity").val();
		$.ajax({
		type: "POST",
		url:url,
		data:{search_string:search_string},		
		success:function(result) {
			$("#group_tags_for_activity .add-option").remove();
			$("#group_tags_for_activity").append(result);
			group_tage_page=1;
		}});
	});	
	$(document).on("click","input:radio[name=user_invite]",function(){
		 var selected =  $( "input:radio[name=user_invite]:checked" ).val();
		 if(selected=='invited'){
			var url = baseurl+'/groups/'+galexy+'/'+planet+'/ajaxAllMembersExepctLoggedOne';				 
			$.ajax({
			type: "POST",
			url:url,
			data:'',		
			success:function(result) {
				 $(".friend-list").show();
				 $("#membersZone").html(result);
				 $("#group_members").dialog();
			}});
			
		 }else{
			$(".friend-list").hide();
			$( "#group_members" ).dialog( "close" );
			 
		 }
	});
	$(document).on("click",".invite_to_activity",function(){
		var user_id = $(this).attr("id"); 
		var content = $(this).html();
		var content='<a href="javascript:void(0)" id="'+user_id+'" class="invited_member"><div class="activity-friends">'+content+'</div></a>';
		$(".friend-list").append(content);
		$(this).remove();
	});
	$(document).on("click",".invited_member",function(){
		 
		$(this).remove();
	});
	$(document).on("click","#cancel_activity",function(){
		 $("#activity_title").val('');
		 $("#activity_description").val('');
		 $("#address").val('');
		 $("#group_activity_start_timestamp").val('');
		 $("#activty_tags").html('');
		 $(".friend-list").html('');
		 $(".new-activity-container").animate({  height:'toggle' });
	});
	$(document).on("click","#save_activity",function(){
		var formData = new FormData();
		if($("#activity_title").val()!=''){			 
			formData.append("activity_title",$("#activity_title").val());
		}
		else{
			alert("Activity name required");
			return false;
		}
		formData.append("activity_description",$("#activity_description").val());
		var i = 0;
		var tags = new Array();
		$( "#activty_tags .add-option" ).each(function() {
			tags[i] = $( this ).attr( "id" );
			i++;
		});
		if(tags.length>3){
			alert("Atleast three tags are allowed for all activities");
			return false;
		}
		if(tags.length<=0){
			alert("Select atleast one tag");
			return false;
		}
		formData.append("tags",tags);
		if($("#address").val()!=''){
			formData.append("address",$("#address").val());
		}else{
			alert("Specify the place that the activity will happen");
			return false;
		}
		if($("#group_activity_start_timestamp").val()!=''){
			formData.append("group_activity_start_timestamp",$("#group_activity_start_timestamp").val());
		}else{
			alert("When this will happend?");
			return false;
		}
		formData.append("members_type", $( "input:radio[name=user_invite]:checked" ).val());
		if($( "input:radio[name=user_invite]:checked" ).val()=='invited'){
			var i = 0;
			var users = new Array();
			$( ".friend-list .invited_member" ).each(function() {
				users[i] = $( this ).attr( "id" );
				i++;
			});
			formData.append("users",users);
		}
		formData.append("geo_latitude",geo_latitude);
		formData.append("geo_longitude",geo_longitude);
		$(".add-activity-actions").html('<img src="'+baseurl+'/public/images/ajax_loader.gif" />');		
		var url = baseurl+'/activity/'+galexy+'/'+planet+'/ajaxCreateActivity';	 
		$.ajax({
		type: "POST",
		url:url,
		data: formData,
		dataType:"json",
		processData: false, 
		contentType: false,	
		success:function(result) {
			 if(result.success){
			 window.location.reload();
			 }else{
				alert(result.msg);
				$(".add-activity-actions").html('<a href="javascript:void(0)" id="save_activity" class="blue-butn">Add new</a>                                <a href="javascript:void(0);" class="grey-butn" id="cancel_activity" style="color:#888888;">Cancel</a>');		
			 }
		}});
	});
	 
$(document).on("click",".activity-likes",function(){	
		var url = baseurl+'/like';
		var divLoad = '#likes' + '_' + this.id;
		var divLoadcal = '#cal_likes' + '_' + this.id; 
		var callback = $.ajax({
			type: "POST",
			url: url,
			dataType:'json',
			data: {
				'group_id': galexy,
				'planet': planet,
				'content_id': this.id,
				'type':'Activity'
			}, // serializes the form's elements.
			success: function(data)
			{	 
				if(data.error){ 
					alert(data.msg);
				}else{ 
				$(divLoad).html('');
				$(divLoad).html(data.view); 
				$(divLoadcal).html(''); 
				$(divLoadcal).html(data.view); 
				
				}
			},
			error: function(msg)
			{
				alert('error');
			}
		});
		
	});
 
	$(document).on("click",".activity-unlikes",function(){		
		var url = baseurl + "/like/unlike";
		var divLoad = '#likes' + '_' + this.id;
		var divLoadcal = '#cal_likes' + '_' + this.id; 
		var callback = $.ajax({
			type: "POST",
			url: url,
			dataType:'json',
			data: {
				'group_id': galexy,
				'planet': planet,
				'content_id': this.id,
				'type':'Activity'
			},
			success: function(data)
			{	
				if(data.error){
					alert(data.msg);
				}else{
				$(divLoad).html('');
				$(divLoad).html(data.view);
				$(divLoadcal).html(''); 
				$(divLoadcal).html(data.view); 
				}
			},
			error: function(msg)
			{
				alert('error');
			}
		});
		
	});	
	$(document).on("keypress",".activity_comments",function(e){
		if(e.which == 13) {
			var id = $(this).attr("id"); 
			var arr_id = id.split("_"); 
			var current_id = arr_id[1];		
			if(current_id){
				var comment = $(this).val();
				 $(this).val('');
				var url			 = baseurl + "/comment/comments";
				var row_id = '#result_' +id;
				var callback = $.ajax({
				type: "post",
				url: url,
				data:{
					'group_id': galexy,
					'planet': planet,
					'content_id': current_id,
					'type':'Activity',
					'comment_content':comment,
					'action':'save',
				}, // serializes the form's elements.
				success: function(data)
				{
				$("#comment_area"+current_id).append(data);
				}
				});		 
			}
		}
	});
	$(document).on("click",".loadmore_comments",function(){	
		var id = $(this).attr("id");
		var url			 = baseurl + "/comment/loadmore";
		//var row_id = '#result_' +id;
		if(comments_page[id]){	
			page = comments_page[id]+1;
		}else{
			page  = 1;
			comments_page[id] = 1;
		}
		var callback = $.ajax({
		type: "post",
		url: url,
		data:{
			'group_id': galexy,
			'planet': planet,
			'content_id': id,
			'type':'Activity',
			'page':page
			 
		}, // serializes the form's elements.
		success: function(data)
		{
			$("#comment_area"+id).prepend(data);
			comments_page[id] = comments_page[id]+1;
			var comment_count = $("#comment_count_"+id).text();
			comment_count = comment_count*1;
			total_count = (comments_page[id]-1)*10;
			if(comment_count<=total_count){
				$("#loadmore_"+id).html('');
			}
			else{	
				$("#comment_count_"+id).html(comment_count-total_count);
			}
		}
		});	
	});
	$(document).on("click",".comments-likes",function(){		
		var url = baseurl+'/like';
		var divLoad = '#comments_likes' + this.id;
		var callback = $.ajax({
			type: "POST",
			url: url,
			dataType:'json',
			data: {				
				'content_id': this.id,
				'type':'Comment'
			}, // serializes the form's elements.
			success: function(data)
			{	
				if(data.error){
					alert(data.msg);
				}else{
				$(divLoad).html('');
				$(divLoad).html(data.view);
				}
			},
			error: function(msg)
			{
				alert('error');
			}
		});
		
	});
	//uncomments likes
	$(document).on("click",".comments-unlikes",function(){		
		var url = baseurl + "/like/unlike";
		var divLoad = '#comments_likes' + this.id;
		var callback = $.ajax({
			type: "POST",
			url: url,
			dataType:'json',
			data: {				 
				'content_id': this.id,
				'type':'Comment'
			},
			success: function(data)
			{	
				if(data.error){
					alert(data.msg);
				}else{
				$(divLoad).html('');
				$(divLoad).html(data.view);
				}
			},
			error: function(msg)
			{
				alert('error');
			}
		});
		
	});
	$(document).on("click",".joinactivity",function(){	
		var id = $(this).attr("id"); 
		var arr_id = id.split("_"); 
		var current_id = arr_id[1];
		if(current_id){
			var url = baseurl + "/activity/rsvp";
			$(this).parent().html('<img src="'+baseurl+'/public/images/ajax_loader.gif" />');			
			var callback = $.ajax({
				type: "POST",
				url: url,
				dataType:'json',
				data: {				 
					'activity_id': current_id,
					'type':'Comment'
				},
				success: function(data)
				{	
					if(data.error){
						alert(data.msg);
					}else{
					window.location.reload();
					$("#joinactivity_rocket_"+current_id).removeClass("activity-rocket");
					$("#joinactivity_rocket_"+current_id).addClass("activity-rocket-member");
					$("#joinactivity_outer_"+current_id).remove();
					}
				},
				error: function(msg)
				{
					alert('error');
				}
			});
		}
	});
	var endloadmore = 0;
	$(document).on("click","#activity_loadmore",function(){	
		var url = baseurl + "/activity/loadmoreActivity";
		var callback = $.ajax({
			type: "post",
			url: url,
			data:{
				'group': galexy,
				'planet': planet,
				 'page':activity_page,
				 'type':'upcoming'
			}, // serializes the form's elements.
			success: function(data)
			{
			$("#activity_list").append(data);
			activity_page++; 
			 
			}
		});	
	});
	$(document).on("click","#past_activity_loadmore",function(){	
		var url = baseurl + "/activity/loadmoreActivity";
		var callback = $.ajax({
			type: "post",
			url: url,
			data:{
				'group': galexy,
				'planet': planet,
				'page':past_activity_page,
				'type':'past'
			}, // serializes the form's elements.
			success: function(data)
			{
			$("#past_activity_list").append(data);
			past_activity_page++; 			 
			}
		});	
	});
	
	$(document).on("click",".membersonboard",function(){
		$("#all-members-dialog").html('');	
		var url = baseurl + "/activity/memberslist";
		var activity_id = $(this).attr("id");
		var callback = $.ajax({
			type: "post",
			url: url,
			data:{			 
				'activity_id': activity_id,				 
			}, // serializes the form's elements.
			success: function(data)
			{
			$("#all-members-dialog").append(data);			 
			 $("#all-members-dialog").dialog();
			}
		});	
	});
	 $(document).on("click","#join_group",function(){	 
        var url = baseurl+'/groups/'+galexy+'/'+planet+'/questionnaire';	 
		$.ajax({
		type: "POST",
		url:url,
		data:{'question':question},
		dataType:'json',			
		success:function(result) {
			 if(result.success){
				 if(result.question){
					$('body').append('<div id="popup_box">Please answer the following questions before joining this group <a id="popupBoxClose">Close</a>    <div id="pop_questionnaire"></div></div>');
					$('#popup_box').fadeIn("slow");
					$('#pop_questionnaire').html(result.msg);
					question++;
				 }
				 else{
					window.location.reload();
				 }
			 }
			 else{
				alert(result.msg);
			 }
		},
		error:function(){
			alert("error");
		}
		
		});	
	});	
	$(document).on("click","#popupBoxClose",function(){
		$('#popup_box').fadeOut("slow");
		question=1;
	});
	$(document).on("click","#pop_questionnaire .blue-butn",function(){
			var id = $(this).attr("id");
			var arr_id = id.split("_"); 
			var current_id = arr_id[2]; 
			var answer_type = $("#answer_type").val();
			var answer = '';
			if(answer_type == 'radio'){
				answer =  $('input:radio[name=answer_'+current_id+']:checked').val();
				if (answer==''){
					alert("Select your answer");
					return false;
				}	
			}
			if(answer_type == 'checkbox'){
				if($('input:checkbox[id=answer_1_'+current_id+']:checked').length)
				answer+= $('input:checkbox[id=answer_1_'+current_id+']:checked').val();
				if($('input:checkbox[id=answer_2_'+current_id+']:checked').length)
				answer+= ','+$('input:checkbox[id=answer_2_'+current_id+']:checked').val();
				if($('input:checkbox[id=answer_3_'+current_id+']:checked').length)
				answer+= ','+$('input:checkbox[id=answer_3_'+current_id+']:checked').val();
				if (answer==''){
					alert("Select your answer");
					return false;
				}	
			}
			if(answer_type == 'Textarea'){
				answer =  $('#answer_'+current_id).val(); 
				if (answer==''){
					alert("enter your answer");
					return false;
				}	
			}
			
			if(answer!=''){
				var url = baseurl+'/groups/'+galexy+'/'+planet+'/questionnaire';	 
				$.ajax({
				type: "POST",
				url:url,
				data:{'question':question,'question_id':current_id,'answer':answer,'answer_type':answer_type},
				dataType:'json',			
				success:function(result) {
					 if(result.success){
						 if(result.question){
							$('#pop_questionnaire').html(result.msg);
							question++;						
						 }
						 else{
							window.location.reload();
						 }
					 }
					 else{
						alert(result.msg);
					 }
				},
				error:function(){
					alert("error");
				}
				
				});
			}else{
				alert("Answer the question please..");
			}
		// });
		/*var url = baseurl+'/groups/'+galexy+'/'+planet+'/join';	
		var search_string = $("#tag_search_for_activity").val();
		$.ajax({
		type: "POST",
		url:url,
		data:'',
		dataType:'json',			
		success:function(result) {
			 if(result.error){
				alert(result.msg);
				if(result.button_hide){
					$("#join_group").remove();
				}
			 }
			 else{
				window.location.reload();
			 }
		}});*/
	 });
	 $(document).on("click",".quitactivity",function(){	
		var id = $(this).attr("id"); 
		var arr_id = id.split("_"); 
		var current_id = arr_id[1];
		if(current_id){
			var url = baseurl + "/activity/quitrsvp";	
			$(this).parent().html('<img src="'+baseurl+'/public/images/ajax_loader.gif" />');				
			var callback = $.ajax({
				type: "POST",
				url: url,
				dataType:'json',
				data: {				 
					'activity_id': current_id,
					'type':'Comment'
				},
				success: function(data)
				{	
					if(data.error){
						alert(data.msg);
					}else{
					window.location.reload();
					$("#joinactivity_rocket_"+current_id).removeClass("activity-rocket");
					$("#joinactivity_rocket_"+current_id).addClass("activity-rocket-member");
					$("#joinactivity_outer_"+current_id).remove();
					}
				},
				error: function(msg)
				{
					alert('error');
				}
			});
		}
	});
	$(document).on("click",".edit_comment",function(){
		var id  = $(this).attr("id");
		//$(this).hide();
		var arr_id = id.split("_"); 
		var current_id = arr_id[2]; 
		comment_content[current_id] = $("#comment_text_"+current_id).text();
		var content = '<div class="comment-edit-container"><textarea id="comment_textarea_'+current_id+'">'+$("#comment_text_"+current_id).text()+'</textarea><input type="button" value="Save" onclick="SaveThisComment('+current_id+')" class="blue-butn" /><input type="button" class="grey-butn" value="Cancel" onclick="CancelThisCommentAction('+current_id+')" /> </div>';
		$("#comment_text_"+current_id).html(content);
	});
	$(document).on("click",".delete_comment",function(){
		var id  = $(this).attr("id");
		//$(this).hide();
		var arr_id = id.split("_"); 
		var current_id = arr_id[2]; 
		var url		  = baseurl + "/comment/delete";
		var callback = $.ajax({
		type: "post",
		url: url,
		data:{
			'group_id': galexy,
			'planet': planet,
			'content_id': current_id,
			'type':'Activity',			 
			'action':'save',
		}, // serializes the form's elements.
		success: function(data)
		{
				if(data.error){
					alert(data.msg);
				}else{			
					$("#comments-outer-"+current_id).remove();
				}
		}
		});	 
	});
	
});
function CancelThisCommentAction(id) {
		$("#comment_text_"+id).html(comment_content[id]);
		$("#edit_comment"+id).show();
	}
function SaveThisComment(id){
	
		var comment = $("#comment_textarea_"+id).val();		  
		var url			 = baseurl + "/comment/edit";
		var row_id = '#result_' +id;
		var callback = $.ajax({
		type: "post",
		url: url,
		data:{
			'group_id': galexy,
			'planet': planet,
			'content_id': id,
			'type':'Activity',
			'comment_content':comment,
			'action':'save',
		}, // serializes the form's elements.
		success: function(data)
		{
			$("#comment_text_"+id).html(comment);
			$("#edit_comment"+id).show();
		}
		});		 
	
}

