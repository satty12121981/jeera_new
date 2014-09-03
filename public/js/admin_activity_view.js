var members_page  = 1 ;
jQuery(document).ready(function(){
	$(document).on("click","#loadmore_members",function(event){ 		  
		 var group_seo_title = $("#galaxy").val();
		 var url = base_url+'/jadmin/activity/getActivityMembers'; 
		 var activity_id = $("#hid_activity_id").val()
		 $.ajax({
			type: "POST",
			url:url,			 
			data:{'page':members_page,'activity_id':activity_id},			 					 
			success:function(result) {					
				 if(result){
					 $("#members_list").append(result);  						  
				 }				 		
			},
			error: function(msg)
			{
				alert('error');
			}
		}); 
	});	 
 
});