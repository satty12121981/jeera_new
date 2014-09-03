jQuery(document).ready(function(){
	$(document).on("click","#delete_activity",function(event){ 	
		 event.preventDefault();
		$("#activity_delete_container").html("Please Wait..<br/><ul><li>Activity likes and comments removing............</li></ul>");		 
		 
		 var activity_id = $("#hid_activity_id").val()
		 var url = base_url+'/jadmin/activity/removeActivityLikesAndComments/'+activity_id; 
		 $.ajax({
			type: "POST",
			url:url,			 
			data:'',
			dataType:'json',
			success:function(result) {					
				if(result.success){ 
					$("#activity_delete_container").html("Please Wait..<br/><ul><li style='color:#00ff00'>Activity likes and comments removed.</li></ul>");
					removeActivitytags(activity_id);
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
 function removeActivitytags(activity_id){
	$("#activity_delete_container").html("Please Wait..<br/><ul><li style='color:#00ff00'>Activity likes and comments removed.</li><li>Activity tags removing............</li></ul>");			 
		 var url = base_url+'/jadmin/activity/removeActivityTags/'+activity_id; 
		 $.ajax({
			type: "POST",
			url:url,			 
			data:'',
			dataType:'json',
			success:function(result) {					
				if(result.success){ 
					$("#activity_delete_container").html("Please Wait..<br/><ul><li style='color:#00ff00'>Activity likes and comments removed.</li><li style='color:#00ff00'>Activity tags removed.</li></ul>");
					removeActivityMembers(activity_id);
				}else{ 
					alert(result.msg);					
				}			 		
			},
			error: function(msg)
			{
				alert('error');
			}
		}); 
 }
 function removeActivityMembers(activity_id){
	$("#activity_delete_container").html("Please Wait..<br/><ul><li style='color:#00ff00'>Activity likes and comments removed.</li><li style='color:#00ff00'>Activity tags removed.</li><li>Activity members removing............</li></ul>");			 
		 var url = base_url+'/jadmin/activity/removeActivityMembers/'+activity_id; 
		 $.ajax({
			type: "POST",
			url:url,			 
			data:'',
			dataType:'json',
			success:function(result) {					
				if(result.success){ 
					$("#activity_delete_container").html("Please Wait..<br/><ul><li style='color:#00ff00'>Activity likes and comments removed.</li><li style='color:#00ff00'>Activity tags removed.</li><li style='color:#00ff00'>Activity members removed.</li></ul>");
					removeActivity(activity_id);
				}else{ 
					alert(result.msg);					
				}			 		
			},
			error: function(msg)
			{
				alert('error');
			}
		}); 
 }
 function removeActivity(activity_id){
		$("#activity_delete_container").html("Please Wait..<br/><ul><li style='color:#00ff00'>Activity likes and comments removed.</li><li style='color:#00ff00'>Activity tags removed.</li><li style='color:#00ff00'>Activity members removed.</li><li>Activity removing............</li></ul>");			 
		 var url = base_url+'/jadmin/activity/removeActivity/'+activity_id; 
		 $.ajax({
			type: "POST",
			url:url,			 
			data:'',	
			dataType:'json',
			success:function(result) {					
				if(result.success){ 
					$("#activity_delete_container").html("<span style='color:#00ff00;'>Done.</span><br/><ul><li style='color:#00ff00'>Activity likes and comments removed.</li><li style='color:#00ff00'>Activity tags removed.</li><li style='color:#00ff00'>Activity members removed.</li><li style='color:#00ff00'>Activity removed.</li></ul>");
				 
				}else{ 
					alert(result.msg);					
				}			 		
			},
			error: function(msg)
			{
				alert('error');
			}
		});
 }
});