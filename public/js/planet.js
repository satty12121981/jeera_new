var planet_page = 1;
var flag=1;

$(document).ready(function(){
	 $("#btn_planet_search").click(function(){
		$("#planet_list").html("<img src='"+baseurl+"/public/images/ajax_loader.gif' />");
		$(".sort_planet").removeClass('active');
		var search_content = $("#planet_search").val();
		var url = baseurl+'/groups/ajaxPlanetSearch';
		$.ajax({
		type: "POST",
		url:url,
		data:{search_content:search_content,galexy:galexy},		 
		success:function(result) {
			$("#planet_list").html(result);
		}});
	 });
	 $(".sort_planet").click(function(){
		$(".sort_planet").removeClass('active');
		$("#planet_list").html("<img src='"+baseurl+"/public/images/ajax_loader.gif' />");
		var sort_content = $(this).text(); 
		$(this).addClass('active');
		$("#planet_search").val('');
		var url = baseurl+'/groups/ajaxPlanetSort';
		$.ajax({
		type: "POST",
		url:url,
		data:{sort_content:sort_content,galexy:galexy},		 
		success:function(result) {
			$("#planet_list").html(result);
		}});
	 });
	 var lastScroll = 0;
	 var url = baseurl+'/groups/ajaxLoadPlanet';
	
	 $(window).scroll(function() {
		var st = $(this).scrollTop();
		if(st > lastScroll){
			if ($('body').height()-100 <= ($(window).height() + $(window).scrollTop())) {
				if(flag&&endresult){ flag = 0;
					 $(".ajax_loader").html('<img src="'+baseurl+'/public/images/ajax_loader.gif" />');
					var search_content = $("#planet_search").val();
					var sort_content = $(".sort_planet .active").text();
					$.ajax({
					type: "POST",
					url: url,
					data: {'page': planet_page,search_content:search_content,sort_content:sort_content,galexy:galexy},
					success: function(data) {
					$(".ajax_loader").html("");
						$("#planet_list").append(data);
						planet_page= planet_page+1;
						flag = 1;
					}
					});
				}
			}
		}
		lastScroll = st;
	});
});
