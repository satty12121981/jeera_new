jQuery(document).ready(function(){
	$(document).on("change","#galaxy",function(event){ 		  
		 var group_seo_title = $("#galaxy").val();
		 var url = base_url+'/groups/ajaxGetPlanetFromGalaxySeoTitle'; 
		 $.ajax({
			type: "POST",
			url:url,			 
			data:{'group_seo':group_seo_title},			 					 
			success:function(result) {					
				 if(result){
					 $("#search_planet_container").html(result);  						  
				 }				 		
			},
			error: function(msg)
			{
				alert('error');
			}
		}); 
	});
	 
	$(document).on("click","#btn_search",function(){	 
		var galaxy = $("#galaxy").val();
		var planet = $("#planet").val();
		var search = $("#activity_search").val();
		var url = base_url+'/jadmin/activity/'+galaxy+'/'+planet+'/id/asc/'+search;
		window.location.href = url;		
	});
	$(document).on("click",".block_activity",function(event){ 
		event.preventDefault();
		var url = $(this).attr("href"); 
		var id  = $(this).attr("id"); 
		var activity_id = id.replace('block_activity_','');
		 $.ajax({
			type: "POST",
			url:url,			 
			data:'',
			dataType:"json",	
			success:function(result) {					
				 if(result.success){
					if(result.status == 2){
						$("#"+id).html('<img src="'+base_url+'/public/images/red-alert.png" title="Unblock">');
						$("#activity_status_"+activity_id).html('<span class="status_Blocked">Blocked</span>');
					}else{
						$("#"+id).html('<img src="'+base_url+'/public/images/tick.png" title="Unblock">');
						$("#activity_status_"+activity_id).html('<span class="status_Active">Active</span>');
					}					
				 }else{
					alert(result.msg);
				}				 
			},
			error: function(msg)
			{
				alert('error');
			}
		}); 
	}); 
});