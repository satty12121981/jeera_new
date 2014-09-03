 
jQuery(document).ready(function(){
	 
	$(document).on("click",".planet_approve",function(event){  
		event.preventDefault();
		var url = $(this).attr("href");
		var id = $(this).attr("id");
		$.ajax({
				type: "POST",
				url: url,
				dataType:'json',				 
				success: function(data)
				{	 
					if(data.success){ 
						if(data.status)
						 $("#"+id).html('<img src="'+base_url+'/public/images/tick.png" title="planet_approve">');
						 else
						 $("#"+id).html('<img src="'+base_url+'/public/images/red-alert.png" title="planet_approve">');
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
});