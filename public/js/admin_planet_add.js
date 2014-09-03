 var owner_page = 1;
jQuery(document).ready(function(){
	$(document).on("blur","#group_title",function(event){ 
		 $("#group_seo_title").attr("disabled", "disabled"); 
		 var group_title = $("#group_title").val();
		 var url = base_url+'/jadmin/galaxy/getSeoTitle'; 
		 $.ajax({
			type: "POST",
			url:url,
			dataType:"json",	
			data:{'group_title':group_title},			 					 
			success:function(result) {					
				 if(result.seotitle){
					 $("#group_seo_title").val(result.seotitle);  						  
				 }
				 $("#group_seo_title").removeAttr("disabled");
				 		
			},
			error: function(msg)
			{
				alert('error');
			}
		}); 
	});
	$(document).on("keypress","#group_owner",function(){ 
		$("#owner_lists").html("<img src='"+base_url+"/public/images/ajax_loader.gif' />");
		$("#owner_lists").show();
		var url = base_url+'/jadmin/planet/ajaxOwnersList';
		var user_name = $("#group_owner").val();
		$.ajax({   
			type: "POST",
			data : {"user_name":user_name,'page':0},			 
			url: url,			 
			success: function(result){
				if(result){					 
					$("#owner_lists").html(result);					 
				}else{
					alert("some error occured");
				}
			} 
		});
	});
	$(document).on("click","#owners_loadmore",function(){ 
		//$("#owner_lists").html("<img src='"+base_url+"/public/images/ajax_loader.gif' />");
		$(this).hide();
		$("#owner_lists").show();
		var url = base_url+'/jadmin/planet/ajaxOwnersList';
		var user_name = $("#group_owner").val();
		$.ajax({   
			type: "POST",
			data : {"user_name":user_name,'page':owner_page},			 
			url: url,			 
			success: function(result){
				if(result){					 
					$("#owner_lists").append(result);	
					owner_page++;			
				}else{
					alert("some error occured");
				}
			} 
		});
	});
	$(document).on("click",".user_list",function(){
		var id = $(this).attr("id");
		var user = id.replace('user_list_','');
		$("#group_owner_id") .val(user);
		var user_name = $(this).text();
		$("#group_owner").val(user_name);
		$("#owner_lists").hide();
	});
	$(document).on("change","#group_country_id",function(){	 
		var country_id = $(this).val();
		var url = base_url+'/city/ajaxCitiesForAdminPlanet';
		 $("#panet_city_container").html("<img src='"+base_url+"/public/images/ajax_loader.gif' />");
		$.ajax({
		type: "POST",
		url:url,
		data:{country_id:country_id},		
		success:function(result) {
			 $("#panet_city_container").html(result);
			 
		}});		
	});
});