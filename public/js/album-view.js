var comments_page = new Array();
var comment_content = new Array();
$(document).ready(function(){
	$(document).on("click",".album-file-likes",function(){	
			var url = baseurl+'/like';
			var divLoad = '#likes' + '_' + this.id;
			 
			var callback = $.ajax({
				type: "POST",
				url: url,
				dataType:'json',
				data: {
					'group_id': galexy,
					'planet': planet,
					'content_id': this.id,
					'type':'Media',					 
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
	$(document).on("click",".album-file-unlikes",function(){		
		var url = baseurl + "/like/unlike";
		var divLoad = '#likes' + '_' + this.id;
		 
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
$(document).on("click",".comments-likes",function(){		
		var url = baseurl+'/like';
		var divLoad = '#comments_likes' + this.id;
		var callback = $.ajax({
			type: "POST",
			url: url,
			dataType:'json',
			data: {				
				'content_id': this.id,
				'type':'Comment',				 
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
$(document).on("keypress",".album-file_comments",function(e){
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
					'page': current_page,
					'type':'Media',
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
			'type':'Media',
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
			'type':'Media',			 
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
			'type':'Media',
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