var notification_page = 1;
$(document).ready(function(){
	 
 
	$(document).on("click","#notification_loadmore",function(){
		var url = baseurl+'/notifications/ajaxLoadMore';		 
		$.ajax({
		type: "POST",
		url:url,
		data:{page:notification_page},		
		success:function(result) {
			$("#notification_loadmore").remove();
			$("#notification_list_outer").append(result);
			notification_page++;
		}});
	});
	 
});
 