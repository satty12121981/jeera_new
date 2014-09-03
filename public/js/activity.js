var activity_page = 1;
var flag=1;

$(document).ready(function(){
	 
	 var lastScroll = 0;
	 var url = baseurl+'/activity/ajaxLoadActivity';
	
	 $(window).scroll(function() {
		var st = $(this).scrollTop();
		if(st > lastScroll){
			if ($('body').height()-100 <= ($(window).height() + $(window).scrollTop())) {
				if(flag){ flag = 0;
					 $(".ajax_loader").html('<img src="'+baseurl+'/public/images/ajax_loader.gif" />');					 
					$.ajax({
					type: "POST",
					url: url,
					data: {'page': activity_page},
					success: function(data) {
					$(".ajax_loader").html("");
						$("#activity_list").append(data);
						activity_page= activity_page+1;
						flag = 1;
					}
					});
				}
			}
		}
		lastScroll = st;
	});
});
