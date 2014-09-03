var galexy_page = 1;
var flag=1;
$(document).ready(function(){
	var lastScroll = 0;
	var url = baseurl+'/groups/ajaxLoadGalaxy';
	$(".ajax_loader").html('<img src="'+baseurl+'/public/images/ajax_loader.gif" />');
	$(window).scroll(function() {
		var st = $(this).scrollTop();
		if(st > lastScroll){
			if ($('body').height()-100 <= ($(window).height() + $(window).scrollTop())) {
				if(flag){ flag = 0;
					$.ajax({
					type: "POST",
					url: url,
					data: {'page': galexy_page},
					success: function(data) {
					$(".ajax_loader").html("");
						$(".galaxy-container").append(data);
						galexy_page= galexy_page+1;
						flag = 1;
					}
					});
				}
			}
		}
		lastScroll = st;
	});
});
