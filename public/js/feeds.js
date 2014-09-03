var comments_page = new Array();
var comment_content = new Array();
var feed_page = 1;
var flag=1; 
$(document).ready(function(){
	$(document).on("click",".activity-likes",function(){
		var url = baseurl+'/like';
		var divLoad = '.act_likes' + '_' + this.id;
		var galexy = $("#galexy_"+this.id).val();
		var planet = $("#planet_"+this.id).val();
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
		var divLoad = '.act_likes' + '_' + this.id;		 
		var galexy = $("#galexy_"+this.id).val();
		var planet = $("#planet_"+this.id).val();
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
				}
			},
			error: function(msg)
			{
				alert('error');
			}
		});		
	});
	$(document).on("click",".discussion-likes",function(){
		var url = baseurl+'/like';
		var divLoad = '.discussion_likes' + '_' + this.id;
		var galexy = $("#discussion_galexy_"+this.id).val();
		var planet = $("#discussion_planet_"+this.id).val();
		var callback = $.ajax({
			type: "POST",
			url: url,
			dataType:'json',
			data: {
				'group_id': galexy,
				'planet': planet,
				'content_id': this.id,
				'type':'Discussion'
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
	$(document).on("click",".discussion-unlikes",function(){		
		var url = baseurl + "/like/unlike";
		var divLoad = '.discussion_likes' + '_' + this.id;		 
		var galexy = $("#discussion_galexy_"+this.id).val();
		var planet = $("#discussion_planet_"+this.id).val();
		var callback = $.ajax({
			type: "POST",
			url: url,
			dataType:'json',
			data: {
				'group_id': galexy,
				'planet': planet,
				'content_id': this.id,
				'type':'Discussion'
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
	$(document).on("click",".group_picture_likes .album-file-likes",function(){
		var url = baseurl+'/like';
		var divLoad = '.group_picture_likes' + '_' + this.id;
		var galexy = $("#groupalbum_galexy_"+this.id).val();
		var planet = $("#groupalbum_planet_"+this.id).val();
		var callback = $.ajax({
			type: "POST",
			url: url,
			dataType:'json',
			data: {
				'group_id': galexy,
				'planet': planet,
				'content_id': this.id,
				'type':'Media'
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
	$(document).on("click",".group_picture_likes .album-file-unlikes",function(){		
		var url = baseurl + "/like/unlike";
		var divLoad = '.group_picture_likes' + '_' + this.id;		 
		var galexy = $("#groupalbum_galexy_"+this.id).val();
		var planet = $("#groupalbum_planet_"+this.id).val();
		var callback = $.ajax({
			type: "POST",
			url: url,
			dataType:'json',
			data: {
				'group_id': galexy,
				'planet': planet,
				'content_id': this.id,
				'type':'Media'
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
	$(document).on("click",".user_picture_likes .album-file-likes",function(){
		var url = baseurl+'/like';
		var divLoad = '.user_picture_likes' + '_' + this.id;
		 
		var callback = $.ajax({
			type: "POST",
			url: url,
			dataType:'json',
			data: {
				 
				'content_id': this.id,
				'type':'Userfiles'
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
	$(document).on("click",".user_picture_likes .album-file-unlikes",function(){		
		var url = baseurl + "/like/unlike";
		var divLoad = '.user_picture_likes' + '_' + this.id;		 
		 
		var callback = $.ajax({
			type: "POST",
			url: url,
			dataType:'json',
			data: {
				 
				'content_id': this.id,
				'type':'Userfiles'
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
	$(document).on("keypress",".activity_comments",function(e){
		if(e.which == 13) {
			var id = $(this).attr("id"); 
			var arr_id = id.split("_"); 
			var current_id = arr_id[1];	
			var galexy = $("#galexy_"+id).val();
			var planet = $("#planet_"+id).val();
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
				$(".activity_comment_area_"+current_id).append(data);
				}
				});		 
			}
		}
	});
	$(document).on("keypress",".discussion_comments",function(e){
		if(e.which == 13) {
			var id = $(this).attr("id"); 
			var arr_id = id.split("_"); 
			var current_id = arr_id[1];	
			var galexy = $("#discussion_galexy_"+this.id).val();
			var planet = $("#discussion_planet_"+this.id).val();
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
					'type':'Discussion',
					'comment_content':comment,
					'action':'save',
				}, // serializes the form's elements.
				success: function(data)
				{
				$(".discussion_comment_area_"+current_id).append(data);
				}
				});		 
			}
		}
	});
	$(document).on("keypress",".group_picture_comments",function(e){
		if(e.which == 13) {
			var id = $(this).attr("id"); 
			var arr_id = id.split("_"); 
			var current_id = arr_id[1];	
			var galexy = $("#groupalbum_galexy_"+this.id).val();
			var planet = $("#groupalbum_planet_"+this.id).val();
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
					'type':'Media',
					'comment_content':comment,
					'action':'save',
				}, // serializes the form's elements.
				success: function(data)
				{
				$(".group_picture_comment_area_"+current_id).append(data);
				}
				});		 
			}
		}
	});
	$(document).on("keypress",".user_picture_comments",function(e){
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
					'content_id': current_id,
					'type':'Userfiles',
					'comment_content':comment,
					'action':'save',
				}, // serializes the form's elements.
				success: function(data)
				{
				$(".user_picture_comment_area_"+current_id).append(data);
				}
				});		 
			}
		}
	});	
	$(document).on("click",".loadmore_activity_comments",function(){ 
		var id = $(this).attr("id");
		var url			 = baseurl + "/comment/loadmore";	
		var content_id	= 	$("#comment_refer_"+id).val();
		if(comments_page[id]){	
			page = comments_page[id]+1;
		}else{
			page  = 1;
			comments_page[id] = 1;
		}
		var galexy = $("#galexy_"+content_id).val();
		var planet = $("#planet_"+content_id).val();
		var callback = $.ajax({
		type: "post",
		url: url,
		data:{
			'group_id': galexy,
			'planet': planet,
			'content_id': content_id,
			'type':'Activity',
			'page':page
			 
		}, // serializes the form's elements.
		success: function(data)
		{
			$("#comment_area_"+id).prepend(data);
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
	$(document).on("click",".loadmore_discussion_comments",function(){ 
		var id = $(this).attr("id");
		var url			 = baseurl + "/comment/loadmore";	
		var content_id	= 	$("#comment_refer_"+id).val();
		if(comments_page[id]){	
			page = comments_page[id]+1;
		}else{
			page  = 1;
			comments_page[id] = 1;
		}
		var galexy = $("#discussion_galexy_"+content_id).val();
		var planet = $("#discussion_planet_"+content_id).val();
		var callback = $.ajax({
		type: "post",
		url: url,
		data:{
			'group_id': galexy,
			'planet': planet,
			'content_id': content_id,
			'type':'Discussion',
			'page':page
			 
		}, // serializes the form's elements.
		success: function(data)
		{
			$("#comment_area_"+id).prepend(data);
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
	$(document).on("click",".loadmore_groupalbum_comments",function(){ 
		var id = $(this).attr("id");
		var url			 = baseurl + "/comment/loadmore";	
		var content_id	= 	$("#comment_refer_"+id).val();
		if(comments_page[id]){	
			page = comments_page[id]+1;
		}else{
			page  = 1;
			comments_page[id] = 1;
		}
		var galexy = $("#groupalbum_galexy_"+content_id).val();
		var planet = $("#groupalbum_planet_"+content_id).val();
		var callback = $.ajax({
		type: "post",
		url: url,
		data:{
			'group_id': galexy,
			'planet': planet,
			'content_id': content_id,
			'type':'Media',
			'page':page
			 
		}, // serializes the form's elements.
		success: function(data)
		{
			$("#comment_area_"+id).prepend(data);
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
	$(document).on("click",".loadmore_userfile_comments",function(){ 
		var id = $(this).attr("id");
		var url			 = baseurl + "/comment/loadmore";	
		var content_id	= 	$("#comment_refer_"+id).val();
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
			 
			'content_id': content_id,
			'type':'Userfiles',
			'page':page
			 
		}, // serializes the form's elements.
		success: function(data)
		{
			$("#comment_area_"+id).prepend(data);
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
		var divLoad = '.comments_likes' + this.id;
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
	$(document).on("click",".comments-unlikes",function(){		
		var url = baseurl + "/like/unlike";
		var divLoad = '.comments_likes' + this.id;
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
	$(document).on("click",".activity-comments .edit_comment",function(){ 
		var parent_id = $(this).parent().parent().parent().parent().attr("id"); 
		var id  = $(this).attr("id");
		var arr_id = id.split("_"); 
		var current_id = arr_id[2];
		comment_content[current_id] = $("#comment_text_"+current_id).text();
		var content = '<div class="comment-edit-container"><textarea id="comment_textarea_'+current_id+'">'+$("#comment_text_"+current_id).text()+'</textarea><input type="button" value="Save" onclick="SaveThisComment('+current_id+',\'Activity\',\''+parent_id+'\')" class="blue-butn" /><input type="button" class="grey-butn" value="Cancel" onclick="CancelThisCommentAction('+current_id+')" /> </div>';
		$('#'+parent_id+' #comment_text_'+current_id).html(content); 
	});
	$(document).on("click",".discussion-comments .edit_comment",function(){ 
		var parent_id = $(this).parent().parent().parent().parent().attr("id"); 
		var id  = $(this).attr("id");
		var arr_id = id.split("_"); 
		var current_id = arr_id[2];
		comment_content[current_id] = $("#comment_text_"+current_id).text();
		var content = '<div class="comment-edit-container"><textarea id="comment_textarea_'+current_id+'">'+$("#comment_text_"+current_id).text()+'</textarea><input type="button" value="Save" onclick="SaveThisComment('+current_id+',\'Discussion\',\''+parent_id+'\')" class="blue-butn" /><input type="button" class="grey-butn" value="Cancel" onclick="CancelThisCommentAction('+current_id+')" /> </div>';
		$('#'+parent_id+' #comment_text_'+current_id).html(content); 
	});
	$(document).on("click",".groupalbum-comments .edit_comment",function(){ 
		var parent_id = $(this).parent().parent().parent().parent().attr("id"); 
		var id  = $(this).attr("id");
		var arr_id = id.split("_"); 
		var current_id = arr_id[2];
		comment_content[current_id] = $("#comment_text_"+current_id).text();
		var content = '<div class="comment-edit-container"><textarea id="comment_textarea_'+current_id+'">'+$("#comment_text_"+current_id).text()+'</textarea><input type="button" value="Save" onclick="SaveThisComment('+current_id+',\'Media\',\''+parent_id+'\')" class="blue-butn" /><input type="button" class="grey-butn" value="Cancel" onclick="CancelThisCommentAction('+current_id+')" /> </div>';
		$('#'+parent_id+' #comment_text_'+current_id).html(content); 
	});
	$(document).on("click",".userfile-comments .edit_comment",function(){ 
		var parent_id = $(this).parent().parent().parent().parent().attr("id"); 
		var id  = $(this).attr("id");
		var arr_id = id.split("_"); 
		var current_id = arr_id[2];
		comment_content[current_id] = $("#comment_text_"+current_id).text();
		var content = '<div class="comment-edit-container"><textarea id="comment_textarea_'+current_id+'">'+$("#comment_text_"+current_id).text()+'</textarea><input type="button" value="Save" onclick="SaveThisComment('+current_id+',\'Userfiles\',\''+parent_id+'\')" class="blue-butn" /><input type="button" class="grey-butn" value="Cancel" onclick="CancelThisCommentAction('+current_id+')" /> </div>';
		$('#'+parent_id+' #comment_text_'+current_id).html(content); 
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
			'content_id': current_id,
			'type':'Activity',			 
			'action':'save',
		}, // serializes the form's elements.
		success: function(data)
		{
				if(data.error){
					alert(data.msg);
				}else{			
					$(".comments-outer-"+current_id).remove();
				}
		}
		});	 
	});
	var lastScroll = 0;
	 var url = baseurl+'/memberprofile/morefeeds';
	var scrollposition=0;
	 $(window).scroll(function() {
		var st = $(this).scrollTop();		 
		if(st > lastScroll && st >scrollposition){ 
			if ($('body').height()-100 <= ($(window).height() + $(window).scrollTop())) {
				if(flag){ flag = 0;  
					 $(".ajax_loader").html('<img src="'+baseurl+'/public/images/ajax_loader.gif" />');					 
					$.ajax({
					type: "POST",
					url: url,
					data: {'page': feed_page},
					success: function(data) {
					$(".ajax_loader").html("");
						$("#news-feed-container").append(data);
						feed_page= feed_page+1;
						flag = 1;
						scrollposition = st+1000;
					},
					error:function(msg)
					{
						flag = 1;
					}
					});
				}
			}
		}
		lastScroll = st;
	});
});
	function CancelThisCommentAction(id) {
		$(".comment_text_"+id).html(comment_content[id]);		
	}
	function SaveThisComment(comment_id,type,parent_id){
		var comment = $("#"+parent_id+" #comment_textarea_"+comment_id).val();		  
		var url			 = baseurl + "/comment/edit";		
		var callback = $.ajax({
		type: "post",
		url: url,
		data:{			 
			'content_id': comment_id,
			'type':type,
			'comment_content':comment,
			'action':'save',
		}, // serializes the form's elements.
		success: function(data)
		{
			$(".comment_text_"+comment_id).html(comment);
			$(".edit_comment").show();
		}
		});	
	}