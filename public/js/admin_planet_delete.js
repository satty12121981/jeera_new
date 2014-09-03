 
jQuery(document).ready(function(){
	 
	$(document).on("click","#delete_planet",function(event){ 
		event.isDefaultPrevented();
		$("#delete_planet_container").html("Please Wait..<br/><ul><li>Album Details removing............</li></ul>");
		var url = base_url+'/jadmin/planet/removeAlbumDetails';
		var group_id = $("#group_id").val();
		$.ajax({
				type: "POST",
				url: url,
				dataType:'json',
				data: {
					'group_id': group_id,					 		 
				}, 
				success: function(data)
				{	 
					if(data.success){ 
						$("#delete_planet_container").html("Please Wait..<br/><ul><li style='color:#00ff00'>Album Details removed.</li></ul>");
						removeDiscussions(group_id);
					}else{ 
						alert(data.msg);					
					}
				},
				error: function(msg)
				{
					alert('error');
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
	function removeDiscussions(group_id){
		$("#delete_planet_container").html("Please Wait..<br/><ul><li style='color:#00ff00'>Album Details removed.</li><li >Planet discussions removing....</li></ul>");
		var url = base_url+'/jadmin/planet/removeDiscussionDetails';
		 
		$.ajax({
				type: "POST",
				url: url,
				dataType:'json',
				data: {
					'group_id': group_id,					 		 
				}, 
				success: function(data)
				{	 
					if(data.success){ 
						$("#delete_planet_container").html("Please Wait..<br/><ul><li style='color:#00ff00'>Album Details removed.</li><li style='color:#00ff00'>Planet Discussion removed.</li></ul>");
						removeActivities(group_id);
					}else{ 
						alert(data.msg);					
					}
				},
				error: function(msg)
				{
					alert('error');
				}
			});
	}
	function removeActivities(group_id){
		$("#delete_planet_container").html("Please Wait..<br/><ul><li style='color:#00ff00'>Album Details removed.</li><li style='color:#00ff00'>Planet Discussion removed.</li><li >Planet activities removing....</li></ul>");
		var url = base_url+'/jadmin/planet/removeActivities';
		 
		$.ajax({
				type: "POST",
				url: url,
				dataType:'json',
				data: {
					'group_id': group_id,					 		 
				}, 
				success: function(data)
				{	 
					if(data.success){ 
						$("#delete_planet_container").html("Please Wait..<br/><ul><li style='color:#00ff00'>Album Details removed.</li><li style='color:#00ff00'>Planet Discussion removed.</li><li style='color:#00ff00'>Planet Activity removed.</li></ul>");
						removeMembersAndPermissions(group_id);
					}else{ 
						alert(data.msg);					
					}
				},
				error: function(msg)
				{
					alert('error');
				}
			});
	}
	function removeMembersAndPermissions(group_id){
		$("#delete_planet_container").html("Please Wait..<br/><ul><li style='color:#00ff00'>Album Details removed.</li><li style='color:#00ff00'>Planet Discussion removed.</li><li style='color:#00ff00'>Planet Activity removed.</li><li >Planet members removing....</li></ul>");
		var url = base_url+'/jadmin/planet/removeMembers';
		 
		$.ajax({
				type: "POST",
				url: url,
				dataType:'json',
				data: {
					'group_id': group_id,					 		 
				}, 
				success: function(data)
				{	 
					if(data.success){ 
						$("#delete_planet_container").html("Please Wait..<br/><ul><li style='color:#00ff00'>Album Details removed.</li><li style='color:#00ff00'>Planet Discussion removed.</li><li style='color:#00ff00'>Planet Activity removed.</li><li style='color:#00ff00'>Planet members removed.</li></ul>");
						removeQuestionnaire(group_id);
					}else{ 
						alert(data.msg);					
					}
				},
				error: function(msg)
				{
					alert('error');
				}
			});
	}
	function removeQuestionnaire(group_id){
		$("#delete_planet_container").html("Please Wait..<br/><ul><li style='color:#00ff00'>Album Details removed.</li><li style='color:#00ff00'>Planet Discussion removed.</li><li style='color:#00ff00'>Planet Activity removed.</li><li style='color:#00ff00'>Planet members removed.</li><li >Planet questionnaire removing....</li></ul>");
		var url = base_url+'/jadmin/planet/removeQuestionnaire';
		 
		$.ajax({
				type: "POST",
				url: url,
				dataType:'json',
				data: {
					'group_id': group_id,					 		 
				}, 
				success: function(data)
				{	 
					if(data.success){ 
						$("#delete_planet_container").html("Please Wait..<br/><ul><li style='color:#00ff00'>Album Details removed.</li><li style='color:#00ff00'>Planet Discussion removed.</li><li style='color:#00ff00'>Planet Activity removed.</li><li style='color:#00ff00'>Planet members removed.</li><li style='color:#00ff00'>Planet questionnaire removed.</li></ul>");
						removeTags(group_id);
					}else{ 
						alert(data.msg);					
					}
				},
				error: function(msg)
				{
					alert('error');
				}
			});
	}
	function removeTags(group_id){
		$("#delete_planet_container").html("Please Wait..<br/><ul><li style='color:#00ff00'>Album Details removed.</li><li style='color:#00ff00'>Planet Discussion removed.</li><li style='color:#00ff00'>Planet Activity removed.</li><li style='color:#00ff00'>Planet members removed.</li><li style='color:#00ff00'>Planet questionnaire removed.</li><li >Planet Tags removing....</li></ul>");
		var url = base_url+'/jadmin/planet/removeTags';
		 
		$.ajax({
				type: "POST",
				url: url,
				dataType:'json',
				data: {
					'group_id': group_id,					 		 
				}, 
				success: function(data)
				{	 
					if(data.success){ 
						$("#delete_planet_container").html("Please Wait..<br/><ul><li style='color:#00ff00'>Album Details removed.</li><li style='color:#00ff00'>Planet Discussion removed.</li><li style='color:#00ff00'>Planet Activity removed.</li><li style='color:#00ff00'>Planet members removed.</li><li style='color:#00ff00'>Planet questionnaire removed.</li><li style='color:#00ff00'>Planet tags removed.</li></ul>");
						removeSettings(group_id);
					}else{ 
						alert(data.msg);					
					}
				},
				error: function(msg)
				{
					alert('error');
				}
			});
	}
	function removeSettings(group_id){
		$("#delete_planet_container").html("Please Wait..<br/><ul><li style='color:#00ff00'>Album Details removed.</li><li style='color:#00ff00'>Planet Discussion removed.</li><li style='color:#00ff00'>Planet Activity removed.</li><li style='color:#00ff00'>Planet members removed.</li><li style='color:#00ff00'>Planet questionnaire removed.</li><li style='color:#00ff00'>Planet tags removed.</li><li >Planet settings removing....</li></ul>");
		var url = base_url+'/jadmin/planet/removeSettings';
		 
		$.ajax({
				type: "POST",
				url: url,
				dataType:'json',
				data: {
					'group_id': group_id,					 		 
				}, 
				success: function(data)
				{	 
					if(data.success){ 
						$("#delete_planet_container").html("Please Wait..<br/><ul><li style='color:#00ff00'>Album Details removed.</li><li style='color:#00ff00'>Planet Discussion removed.</li><li style='color:#00ff00'>Planet Activity removed.</li><li style='color:#00ff00'>Planet members removed.</li><li style='color:#00ff00'>Planet questionnaire removed.</li><li style='color:#00ff00'>Planet tags removed.</li><li style='color:#00ff00'>Planet settings removed.</li></ul>");
						removeGroup(group_id);
					}else{ 
						alert(data.msg);					
					}
				},
				error: function(msg)
				{
					alert('error');
				}
			});
	}
	function removeGroup(group_id){
		$("#delete_planet_container").html("Please Wait..<br/><ul><li style='color:#00ff00'>Album Details removed.</li><li style='color:#00ff00'>Planet Discussion removed.</li><li style='color:#00ff00'>Planet Activity removed.</li><li style='color:#00ff00'>Planet members removed.</li><li style='color:#00ff00'>Planet questionnaire removed.</li><li style='color:#00ff00'>Planet tags removed.</li><li style='color:#00ff00'>Planet settings removed.</li><li >Planet removing....</li></ul>");
		var url = base_url+'/jadmin/planet/removeGroup';
		 
		$.ajax({
				type: "POST",
				url: url,
				dataType:'json',
				data: {
					'group_id': group_id,					 		 
				}, 
				success: function(data)
				{	 
					if(data.success){ 
						$("#delete_planet_container").html("<span style='color:#00ff00;'>Done.</span><br/><ul><li style='color:#00ff00'>Album Details removed.</li><li style='color:#00ff00'>Planet Discussion removed.</li><li style='color:#00ff00'>Planet Activity removed.</li><li style='color:#00ff00'>Planet members removed.</li><li style='color:#00ff00'>Planet questionnaire removed.</li><li style='color:#00ff00'>Planet tags removed.</li><li style='color:#00ff00'>Planet settings removed.</li><li style='color:#00ff00'>Planet removed.</li></ul>");
						 
					}else{ 
						alert(data.msg);					
					}
				},
				error: function(msg)
				{
					alert('error');
				}
			});
	}
});