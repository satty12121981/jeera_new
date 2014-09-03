 
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
	 
});