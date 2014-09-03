var search_page = 1;
var search_str = '';
$(document).ready(function(){
 
	$(document).on("click","#search_loadmore",function(){
		$(this).remove();
		var url = baseurl+'/application/ajaxLoadMore';		 
		$.ajax({
		type: "POST",
		url:url,
		data:{'page':search_page,'search_str':search_str},		
		success:function(result) {
			
			$("#search_lists").append(result);
			search_page++;
		}});
	});
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
});
 