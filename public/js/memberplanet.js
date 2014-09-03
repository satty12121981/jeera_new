 var planet_page = 1;
 var geo_latitude = 0;
var geo_longitude = 0;
var uploadfiles = new Array();
$(document).ready(function(){
	$(document).on("click","#loadMore_planet",function(){	
		 $(".load-more-butn").remove();
		var url = baseurl+'/profile/ajaxLoadMorePlanets/'+profile_name;
		$.ajax({   
			type: "POST",
			data : {"page":planet_page},			 
			url: url,   
			 
			success: function(result){
				if(result){
				  $("#user_planet_list").append(result);
				  planet_page++;
				  }
			                     
			} 
	});
	});
	$(document).on("click","#creat_planet",function(){	
		$("#create_planet_container").animate({  height:'toggle' });	 
	});
	$(document).on("blur","#planet_name",function(){
		var url = baseurl+'/groups/checkPlanetExist';
		var planet_name = $("#planet_name").val();
		$.ajax({   
			type: "POST",
			data : {"planet_name":planet_name},			 
			url: url,   
			dataType:"json", 
			success: function(result){
				if(result.error){
				  $("#availablity").html('Not available');
				  $("#availablity").addClass("not_available");
				}
				else{
					 $("#availablity").html('Available');
					 $("#availablity").removeClass("not_available");
				}
								 
			} 
		});
	});	
	$(document).on("change","#country",function(){
		var url = baseurl+'/city/ajaxCities';
		var country = $(this).val();
		$.ajax({   
			type: "POST",
			data : {"country_id":country},			 
			url: url,			 
			success: function(result){
				if(result){
					$("#panet_city_container").html(result);
					$("#panet_place_container").show();
					$("#map-canvas").show();
					mapselected = 'map1';
					gmaps_init();
					autocomplete_init();
				}else{
					alert("some error occured");
				}
			} 
		});
	});
$(document).on("change","#panet_city_container #user_profile_city",function(){
		$("#panet_place_container").show();
		$("#map-canvas").show();
		mapselected = 'map1';
		gmaps_init();
		autocomplete_init();
	}); 
	$(document).on("click","#planet_tags .add-option",function(){
		var selected_tag_id = $(this).attr('id');
		var selected_tag	= $(this).text();
		var search_string = $("#tag_search_planet").val();		
		$(this).remove();
		$("#group_tags #"+selected_tag_id).remove();
		$("#group_tags").append('<a class="add-option" href="javascript:void(0)" id="'+selected_tag_id+'">'+selected_tag+'</a>');
		var url = baseurl+'/user/ajaxgettag';
		$.ajax({
		type: "POST",
		url:url,
		data:{page:tag_page,search_string:search_string},		
		success:function(result) {
			$("#planet_tags").append(result);
			tag_page=tag_page+1;
		}});
	});
	$(document).on("click","#group_tags .add-option",function(){
		$(this).remove();
	});
	$(document).on("click","#clear_all_tags",function(){
		$("#group_tags").html('');
	});
	$(document).on("click","#btn_search_tag_planet",function(){
		var url = baseurl+'/user/tagsearch';
		var search_string = $("#tag_search_planet").val();
		$.ajax({
		type: "POST",
		url:url,
		data:{search_string:search_string},		
		success:function(result) {
			$("#planet_tags .add-option").remove();
			$("#planet_tags").append(result);
			tag_page=1;
		}});
	});
	$(document).on("change","#planetpic",function(event){
		uploadfiles = event.target.files;	 
	});
	$(document).on("click","#submit_create_planet",function(event){
		event.stopPropagation();
	    event.preventDefault();
		var formvalues = new Array();
		var formData = new FormData();
		if($("#planet_name").length){			 
			formData.append("title",$("#planet_name").val());
		}
		if($("#galaxy").length){			 
			formData.append("galaxy",$("#galaxy").val());
		}
		if($("#country").length){	 
			formData.append("country",$("#country").val());
		}
		if($("#user_profile_city").length){	 
			formData.append("city",$("#user_profile_city").val());
		}
		if($("#location").length){	 
			formData.append("location",$("#location").val());
		}
		if($("#web_address").length){		 
			formData.append("webaddress",$("#web_address").val());
		}
		if($("#planet_description").length){		 
			formData.append("description",$("#planet_description").val());
		}
		if($("#welcome_message").length){ 	 	 
			formData.append("welcome_message",$("#welcome_message").val());
		}	
		if($('input:checkbox[name=chckQuestionnaire]:checked').length){ 
			formData.append("chckQuestionnaire",$("#chckQuestionnaire").val());
			if($("#question1").length){
				formData.append("question1",$("#question1").val());
				if($('input:radio[name=q1_answer_type]:checked').length){
					formData.append("q1_answer_type",$('input:radio[name=q1_answer_type]:checked').val());
					if($('input:radio[name=q1_answer_type]:checked').val()=='radio'||$('input:radio[name=q1_answer_type]:checked').val()=='checkbox'){
						formData.append("q1_option_1",$("#q1_option_1").val());
						formData.append("q1_option_2",$("#q1_option_2").val());
						formData.append("q1_option_3",$("#q1_option_3").val());
					}
				}
			}
			if($("#question2").length){
				formData.append("question2",$("#question2").val());
				if($('input:radio[name=q2_answer_type]:checked').length){
					formData.append("q2_answer_type",$('input:radio[name=q2_answer_type]:checked').val());
					if($('input:radio[name=q2_answer_type]:checked').val()=='radio'||$('input:radio[name=q2_answer_type]:checked').val()=='checkbox'){
						formData.append("q2_option_1",$("#q2_option_1").val());
						formData.append("q2_option_2",$("#q2_option_2").val());
						formData.append("q2_option_3",$("#q2_option_3").val());
					}
				}
			}
			if($("#question3").length){
				formData.append("question3",$("#question3").val());
				if($('input:radio[name=q3_answer_type]:checked').length){
					formData.append("q3_answer_type",$('input:radio[name=q3_answer_type]:checked').val());
					if($('input:radio[name=q3_answer_type]:checked').val()=='radio'||$('input:radio[name=q3_answer_type]:checked').val()=='checkbox'){
						formData.append("q3_option_1",$("#q3_option_1").val());
						formData.append("q3_option_2",$("#q3_option_2").val());
						formData.append("q3_option_3",$("#q3_option_3").val());
					}
				}
			}
		} 
		$.each(uploadfiles, function(key, value)
		{
			formData.append(key, value); 
		});
		formData.append("geo_latitude",geo_latitude);
		formData.append("geo_longitude",geo_longitude);
		//formvalues['geo_latitude'] = geo_latitude;
		//formvalues['geo_longitude'] = geo_longitude;
		//formData.append("formvalues",formvalues);
		 
		var tags = new Array();
		var i=0;
		$.each($('#group_tags .add-option'), function() {
			tags[i] = this.id ;i++;
		});
		formData.append("tags",tags);
		var url = baseurl+'/groups/createPlanet';
		 $.ajax({
				type: "POST",
				url: url,
				data: formData,
				dataType:"json",
				processData: false, 
				contentType: false,
				success: function(data) {				
					if(data.success){
						window.location.reload();
					}
					else{
						alert(data.msg);
					}
				}
			});
	});
	$(document).on("click","#chckQuestionnaire",function(event){
		 if ($("#chckQuestionnaire").is(":checked")){
		 $("#questionnaire-container").animate({  height:'toggle' });	
		}else{
		 $("#questionnaire-container").animate({  height:'toggle' });
		 $("#questionnaire-container").hide('slow');
		}
	});
	var q1_optionflag = 0; 
	var q2_optionflag = 0; 
	var q3_optionflag = 0; 
	$(document).on("click","input:radio[name=q1_answer_type]",function(event){
		if($('input:radio[name=q1_answer_type]:checked').val()=='radio'||$('input:radio[name=q1_answer_type]:checked').val()=='checkbox'){
			var content = '<div id="q1_option"><div class="planet-field">Add options</div><div class="planet-field"> 1 <input type="text" id="q1_option_1" name="q1_option_1" value="" /> 2 <input type="text" id="q1_option_2" name="q1_option_2" value="" /> 3 <input type="text" id="q1_option_3" name="q1_option_3" value="" /></div></div>';
			if(q1_optionflag == 0){
				$(this).parent().append(content);
			}
			q1_optionflag  = 1;
		}else{
			$("#q1_option").remove();
			q1_optionflag  = 0;
		}
	});
	$(document).on("click","input:radio[name=q2_answer_type]",function(event){
		if($('input:radio[name=q2_answer_type]:checked').val()=='radio'||$('input:radio[name=q2_answer_type]:checked').val()=='checkbox'){
			var content = '<div id="q2_option"><div class="planet-field">Add options</div><div class="planet-field"> 1 <input type="text" id="q2_option_1" name="q2_option_1" value="" /> 2 <input type="text" id="q2_option_2" name="q2_option_2" value="" /> 3 <input type="text" id="q2_option_3" name="q2_option_3" value="" /></div></div>';
			if(q2_optionflag == 0){
				$(this).parent().append(content);
			}
			q2_optionflag  = 1;
		}else{
			$("#q2_option").remove();
			q2_optionflag  = 0;
		}
	});
	$(document).on("click","input:radio[name=q3_answer_type]",function(event){
		if($('input:radio[name=q3_answer_type]:checked').val()=='radio'||$('input:radio[name=q3_answer_type]:checked').val()=='checkbox'){
			var content = '<div id="q3_option"><div class="planet-field">Add options</div><div class="planet-field"> 1 <input type="text" id="q3_option_1" name="q3_option_1" value="" /> 2 <input type="text" id="q3_option_2" name="q3_option_2" value="" /> 3 <input type="text" id="q3_option_3" name="q3_option_3" value="" /></div></div>';
			if(q3_optionflag == 0){
				$(this).parent().append(content);
			}
			q3_optionflag  = 1;
		}else{
			$("#q3_option").remove();
			q3_optionflag  = 0;
		}
	});
});