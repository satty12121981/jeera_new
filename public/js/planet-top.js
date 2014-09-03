var planet_title_name = $("#basic_title_field").text();
var planet_image_field =$("#basic_picture_field").html();
var planet_webaddress_field=$("#basic_webaddress_field").text();
var planet_description_field = $("#basic_description_field").html();
var planet_welcome_field =$("#basic_welcome_message_field").html();
var planet_location_field = $("#basic_location_field").html();
var rolesEditContainer = $("#rolesEditContainer").html();
var rollPermissions_fields = $("#rollPermissions_fields").html();
var discussion_settings_field = $("#discussion_settings_field").html();
var visiblity_status = $("#visiblity_status").html();
var uploadfiles = new Array();
var geo_latitude = 0;
var geo_longitude = 0;
var tag_page = 1;
$(function() {   
   
	$("#show_hide_settings").click(function(){
		$(".accordion").animate({  height:'toggle' });
	});
	$("#basic_settings").click(function(){
		if($(this).hasClass( "head_active" )){
			$(this).removeClass("head_active");
		}
		else{
			$(this).addClass("head_active");
		}
		$("#basic_settings_form").animate({  height:'toggle' });
		mapselected = 'map1';
		gmaps_init();
		autocomplete_init();
	});
	$("#other_settings").click(function(){
		if($(this).hasClass( "head_active" )){
			$(this).removeClass("head_active");
		}
		else{
			$(this).addClass("head_active");
		}
		$("#other_settings_form").animate({  height:'toggle' });		 
	});
	$("#members_settings").click(function(){
		if($(this).hasClass( "head_active_sub" )){
			$(this).removeClass("head_active_sub");
		}
		else{
			$(this).addClass("head_active_sub");
		}
		$("#members_settings_form").animate({  height:'toggle' });		 
	});
	$("#tag_settings").click(function(){
		if($(this).hasClass( "head_active_sub" )){
			$(this).removeClass("head_active_sub");
		}
		else{
			$(this).addClass("head_active_sub");
		}
		$("#tag_settings_form").animate({  height:'toggle' });		 
	});
	$("#privacy_settings").click(function(){
		if($(this).hasClass( "head_active_sub" )){
			$(this).removeClass("head_active_sub");
		}
		else{
			$(this).addClass("head_active_sub");
		}
		$("#privacy_settings_form").animate({  height:'toggle' });		 
	});
	$("#visibility_settings").click(function(){
		if($(this).hasClass( "head_active_sub" )){
			$(this).removeClass("head_active_sub");
		}
		else{
			$(this).addClass("head_active_sub");
		}
		$("#visibility_settings_form").animate({  height:'toggle' });		 
	});
	$("#public").click(function(){
		 
		$(".private-sub-fields").hide();		 
	});
	$("#private").click(function(){
		 
		$(".private-sub-fields").show();		 
	});
	$(document).on("click","#title_edit",function(){
		planet_title_name = $("#basic_title_field").text();
		var content = '<input type="text" placeholder="Planet Name" id="title" name="title" value="'+planet_title_name+'" />'
		$("#basic_title_field").html(content);
		$("#basic_button").show();
		$("#title_edit").hide();
	});
	$(document).on("click","#picture_edit",function(){		
		planet_image_field = $("#basic_picture_field").html();
		var content = '<span><input type="file" name="planet_cover" id="planet_cover" /></span>'
		$("#basic_picture_field").html(content);
		$("#basic_button").show();
		$("#picture_edit").hide();
	});
	$(document).on("click","#webaddress_edit",function(){
		planet_webaddress_field = $("#basic_webaddress_field").text();
		var content = '<input type="text" placeholder="Web address" id="webaddress" name="webaddress" value="'+planet_webaddress_field+'" />'
		$("#basic_webaddress_field").html(content);
		$("#basic_button").show();
		$("#webaddress_edit").hide();
	});
	$(document).on("click","#description_edit",function(){
		planet_description_field = $("#basic_description_field").html();
		var content = '<textarea name="description" placeholder="Planet descriptions" id="description">'+planet_description_field+'</textarea>'
		$("#basic_description_field").html(content);
		$("#basic_button").show();
		$("#description_edit").hide();
	});
	$(document).on("click","#welcome_edit",function(){
		planet_welcome_field = $("#basic_welcome_message_field").html();
		var content = '<textarea name="welcome_message" placeholder="Welcome message" id="welcome_message">'+planet_welcome_field+'</textarea>'
		$("#basic_welcome_message_field").html(content);
		$("#basic_button").show();
		$("#welcome_edit").hide();
	});
	$(document).on("click","#location_edit",function(){
		planet_location_field = $("#basic_location_field").html();
		var url = baseurl+'/country/ajaxCountryList';
		$.ajax({
				type: "POST",
				url: url,
				data: '',
				success: function(data) {
					content = data;
					content+='<div id="city_contaier"><select id="city_list"><option value="">Select your city</option></select></div>';
					content+='<input type="text" value="'+planet_location_field+'" id="location" name="location" />';
					$("#basic_location_field").html(content);
					mapselected = 'map1';
					gmaps_init();
					autocomplete_init();
				}
		});
		 
		//$("#basic_location_field").html(content);
		$("#basic_button").show();
		$("#location_edit").hide();
	});
	$(document).on("change","#country_list",function(){
		var country = $(this).val();		
		var url = baseurl+'/city/ajaxCitiesFromGeocode';
		$.ajax({
				type: "POST",
				url: url,
				data: {country_id:country},
				success: function(data) {
					 $("#city_contaier").html(data);
				}
		});
		 
		 
	});
	$(document).on("click","#basic_settings_cancel",function(){
		$("#basic_title_field").html(planet_title_name);
		$("#basic_picture_field").html(planet_image_field);
		$("#basic_webaddress_field").html(planet_webaddress_field);
		$("#basic_description_field").html(planet_description_field);
		$("#basic_welcome_message_field").html(planet_welcome_field);
		$("#basic_location_field").html(planet_location_field);
		
		$("#basic_button").hide();
		$("#title_edit").show();
		$("#picture_edit").show();
		$("#webaddress_edit").show();
		$("#description_edit").show();
		$("#welcome_edit").show();
		$("#location_edit").show();
	});
	$(document).on("change","#planet_cover",function(event){
		uploadfiles = event.target.files;	 
	});
	$(document).on("click","#basic_settings_save",function(event){
		event.stopPropagation();
	    event.preventDefault();
		var formvalues = new Array();
		var formData = new FormData();
		if($("#title").length){			 
			formData.append("title",$("#title").val());
		}
		if($("#country_list").length){	 
			formData.append("country",$("#country_list").val());
		}
		if($("#city_list").length){	 
			formData.append("city",$("#city_list").val());
		}
		if($("#location").length){	 
			formData.append("location",$("#location").val());
		}
		if($("#webaddress").length){		 
			formData.append("webaddress",$("#webaddress").val());
		}
		if($("#description").length){		 
			formData.append("description",$("#description").val());
		}
		if($("#welcome_message").length){ 	 	 
			formData.append("welcome_message",$("#welcome_message").val());
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
		formData.append("type","basic");
		var url = baseurl+'/groups/'+galexy+'/'+planet+'/ajaxSettings';
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
						//$("#basic_errors").html(data.msg);
						alert(data.msg);
					}
				}
			});
	});
	$(document).on("click","#memberroles_edit",function(event){
		 rolesEditContainer = $("#rolesEditContainer").html();
		 var content   = '<img src="'+baseurl+'/public/images/ajax_loader.gif" />';
		 var url = baseurl+'/grouprole/rolelist';
		 $("#rolesEditContainer").html(content);
		 $.ajax({
				type: "POST",
				url: url,
				data: '',
				success: function(data) {
						content = data+'<div id="user_lists"></div>';
					  $("#rolesEditContainer").html(content);
				}
			});
		$("#memberroles_edit").hide();
	});
	$(document).on("change","#rolesEditContainer  #role_list",function(event){
		var url = baseurl+'/groups/'+galexy+'/'+planet+'/ajaxMembersList';
		var role = $(this).val();
		 
		var content   = '<img src="'+baseurl+'/public/images/ajax_loader.gif" />';
		$("#user_lists").html(content);
		$.ajax({
			type: "POST",
			url: url,
			data: {"role":role},
			success: function(data) {					 
				  $("#user_lists").html(data);
			}
		});
	 		
	
	});
	$(document).on("click","#add_members_to_roles",function(event){ 
		$("#whole_members").dialog();
	}); 
	$(document).on("click",".addToRoles",function(event){ 
		 var user_id = $(this).attr("id"); 
		 var content = $(this).html();
		 content = '<div id="selected_member_'+user_id+'"><a href="javascript:void(0)" id="'+user_id+'" class="selected_members_list">'+content+'</a></div> <a href="javascript:void(0)" id="'+user_id+'" class="remove_selected_member">REMOVE</a>';
		 $(this).remove();
		$("#selected_members").append(content);
		
	}); 
	$(document).on("click",".remove_selected_member",function(event){ 
		 var user_id = $(this).attr("id"); 
		 var content = $("#selected_member_"+user_id+" .selected_members_list").html();
		 content = '<a href="javascript:void(0)" id="'+user_id+'" class="addToRoles">'+content+'</a>';
		 $(this).remove();
		 $("#selected_member_"+user_id).remove();
		$("#whole_members").append(content);
		
	}); 
	$(document).on("click","#membership_settings_cancel",function(event){ 
		$("#rolesEditContainer").html(rolesEditContainer);
		$("#memberroles_edit").show();
	});
	$(document).on("click","#membership_settings_save",function(event){
		var i = 0;
		var users = new Array();
		$( "#selected_members .selected_members_list" ).each(function() {
			users[i] = $( this ).attr( "id" );
			i++;
		}); 
		if(i == 0){
			alert("Add members in this role");
		}
		else{	
			var roles = $( "#role_list").val();
			if(roles==''||roles=='undefined'){
				alert("Select roles");
				return false;
			}
			var url = baseurl+'/groups/'+galexy+'/'+planet+'/ajaxAddRoles';
			$.ajax({
				type: "POST",
				url: url,
				data: {"users":users,"roles":roles},
				dataType:"json",				 
				success: function(data) {				
					if(data.success){
						window.location.reload();
					}
					else{
						$("#basic_errors").html(data.msg);
					}
				}
			});
		}
	});
	$(document).on("click","#permissions_edit",function(event){
		 rollPermissions_fields = $("#rollPermissions_fields").html();
		 var content   = '<img src="'+baseurl+'/public/images/ajax_loader.gif" />';
		 var url = baseurl+'/grouprole/rolelist';
		 $("#rollPermissions_fields").html(content);
		 $.ajax({
				type: "POST",
				url: url,
				data: '',
				success: function(data) {
						content = data+'<div id="permissions_lists"></div>';
					  $("#rollPermissions_fields").html(content);
					  $("#permission_button").show();
				}
			});
		$("#permissions_edit").hide();
	});
	$(document).on("change","#rollPermissions_fields  #role_list",function(event){
		var url = baseurl+'/groups/'+galexy+'/'+planet+'/ajaxPermissions';
		var role = $(this).val();
		 
		var content   = '<img src="'+baseurl+'/public/images/ajax_loader.gif" />';
		$("#user_lists").html(content);
		$.ajax({
			type: "POST",
			url: url,
			data: {"role":role},
			
			success: function(data) {					 
				  $("#permissions_lists").html(data);
			}
		});
	 		
	
	});
	$(document).on("click","#permission_save",function(event){
		var permissions_checked = new Array();
		var i=0;
		 $('input[type="checkbox"]').each(function() {
            if (this.checked) { 
                permissions_checked[i] =$(this).val() ;
				i++;
            }
        });
		var url = baseurl+'/groups/'+galexy+'/'+planet+'/ajaxSavePermissions';
		var role = $("#role_list").val();	 
		$.ajax({
			type: "POST",
			url: url,
			data: {"role":role,"functions":permissions_checked},
			dataType:"json",	
			success: function(data) {					 
				  if(data.success){
						window.location.reload();
					}
					else{
						alert(data.msg)
					}
			}
		});
	});
	$(document).on("click","#permission_cancel",function(event){
		$("#rollPermissions_fields").html(rollPermissions_fields);
		 $("#permission_button").hide();
		 $("#permissions_edit").show();
	});
	$(document).on("click","#discussion_edit",function(event){
		$(".discussion_first").hide();
		$(".discussion_hidden").show();
		$("#discussion_edit").hide();
	});
	$(document).on("click","#discussion_cancel",function(event){
		$(".discussion_first").show();
		$(".discussion_hidden").hide();
		$("#discussion_edit").show();
	});
	$(document).on("click","#discussion_save",function(event){
		var settings  = $( "input:radio[name=radio_discussion_settings]:checked" ).val();
		var url = baseurl+'/groups/'+galexy+'/'+planet+'/ajaxDiscussionSettings';		 
		$.ajax({
			type: "POST",
			url: url,
			data: {"settings":settings},
			dataType:"json",	
			success: function(data) {					 
				  if(data.success){
						window.location.reload();
					}
					else{
						alert(data.msg)
					}
			}
		});
	});
	$(document).on("click","#activity_edit",function(event){
		$(".activity_first").hide();
		$(".activity_hidden").show();
		$("#activity_edit").hide();
	});
	$(document).on("click","#activity_cancel",function(event){
		$(".activity_first").show();
		$(".activity_hidden").hide();
		$("#activity_edit").show();
	});
	$(document).on("click","#cancel_discussion",function(event){
		$("#discussion_content").val('');
	});
	$(document).on("click","#activity_save",function(event){
		var settings  = $( "input:radio[name=radio_activity_settings]:checked" ).val();
		var url = baseurl+'/groups/'+galexy+'/'+planet+'/ajaxActivitySettings';		 
		$.ajax({
			type: "POST",
			url: url,
			data: {"settings":settings},
			dataType:"json",	
			success: function(data) {					 
				  if(data.success){
						window.location.reload();
					}
					else{
						alert(data.msg)
					}
			}
		});
	});
	$(document).on("click","#members_join_settings",function(event){
		var settings  = $( "input:radio[name=planet_members]:checked" ).val();
		var url = baseurl+'/groups/'+galexy+'/'+planet+'/ajaxMemberSettings';	
		var email_content = $( "#welcome_email" ).val();
		var questionnaire = 0;
		if($('input:checkbox[name=chk_questionnaire]:checked').length){ 
			var questionnaire = 1;
		}
		$.ajax({
			type: "POST",
			url: url,
			data: {"settings":settings,"email_content":email_content,"questionnaire":questionnaire},
			dataType:"json",	
			success: function(data) {					 
				  if(data.success){
						window.location.reload();
					}
					else{
						alert(data.msg)
					}
			}
		});
	});
	$(document).on("click","#interest-tags .add-option",function(){
		var selected_tag_id = $(this).attr('id');
		var selected_tag	= $(this).text();
		var search_string = $("#tag_search").val();		
		$(this).remove();
		$("#group_tags #"+selected_tag_id).remove();
		$("#group_tags").append('<a class="add-option" href="javascript:void(0)" id="'+selected_tag_id+'">'+selected_tag+'</a>');
		var url = baseurl+'/user/ajaxgettag';
		$.ajax({
		type: "POST",
		url:url,
		data:{page:tag_page,search_string:search_string},		
		success:function(result) {
			$("#interest-tags").append(result);
			tag_page=tag_page+1;
		}});
	});
	$(document).on("click","#group_tags .add-option",function(){
		$(this).remove();
	});
	$(document).on("click","#clear_all_tags",function(){
		$("#group_tags").html('');
	});
	$(document).on("click","#btn_search_tag",function(){
		var url = baseurl+'/user/tagsearch';
		var search_string = $("#tag_search").val();
		$.ajax({
		type: "POST",
		url:url,
		data:{search_string:search_string},		
		success:function(result) {
			$("#interest-tags .add-option").remove();
			$("#interest-tags").append(result);
			tag_page=1;
		}});
	});
	$(document).on("click","#tag_submit",function(){
		var tags = new Array();
		var i=0;
		$.each($('#group_tags .add-option'), function() {
			tags[i] = this.id ;i++;
		});
		var url = baseurl+'/groups/'+galexy+'/'+planet+'/ajaxSaveGroupTags';			
		$.ajax({
			type: "POST",
			url: url,
			data: {"tags":tags},
			dataType:"json",	
			success: function(data) {					 
				  if(data.success){
						alert(data.msg);
					}
					else{
						alert(data.msg);
					}
			}
		});
	});
	$(document).on("click","#privacy_settings_save",function(event){
		var settings  = $( "input:radio[name=privacy]:checked" ).val();
		if(settings == 'Private'){
			settings  = $( "input:radio[name=privacy_sub]:checked" ).val();
		}
		var url = baseurl+'/groups/'+galexy+'/'+planet+'/ajaxPrivacySettings';	
		
		$.ajax({
			type: "POST",
			url: url,
			data: {"settings":settings},
			dataType:"json",	
			success: function(data) {					 
				  if(data.success){
						window.location.reload();
					}
					else{
						alert(data.msg)
					}
			}
		});
	});
	$(document).on("click","#visibility_edit",function(){
		planet_title_name = $("#visiblity_status").text();
		var content = '<select id="privacy_status"><option value="1">Active</option><option value="0">Deactivate</option></select>'
		$("#visiblity_status").html(content);
		$("#visibility_button").show();
		$("#visibility_edit").hide();
	});
	$(document).on("click","#visibility_settings_save",function(event){
		var settings  = $( "#privacy_status" ).val();		 
		var url = baseurl+'/groups/'+galexy+'/'+planet+'/ajaxVisibilitySettings';			
		$.ajax({
			type: "POST",
			url: url,
			data: {"settings":settings},
			dataType:"json",	
			success: function(data) {					 
				  if(data.success){
						window.location.reload();
					}
					else{
						alert(data.msg)
					}
			}
		});
	});
	$(document).on("click","#quit_group",function(event){
		var url = baseurl+'/groups/'+galexy+'/'+planet+'/quitGroup';			
		$.ajax({
			type: "POST",
			url: url,
			data:'',
			dataType:"json",	
			success: function(data) {					 
			  if(data.success){
					window.location.reload();
				}
				else{
					alert(data.msg)
				}
			}
		});
	});
	$(document).on("click",".add_to_members",function(event){
		var url = baseurl+'/groups/'+galexy+'/'+planet+'/approveMembers';
		var id = $(this).attr("id"); 
		var arr_id = id.split("_"); 
		var current_id = arr_id[3]; 
		$.ajax({
			type: "POST",
			url: url,
			data:{'user':current_id},
			dataType:"json",	
			success: function(data) {					 
			  if(data.success){
					 $("#list_member_approve_"+current_id).remove();
				}
				else{
					alert(data.msg)
				}
			}
		});
	});
	$(document).on("click",".ignore_members",function(event){
		var url = baseurl+'/groups/'+galexy+'/'+planet+'/IgnoreMembers';
		var id = $(this).attr("id"); 
		var arr_id = id.split("_"); 
		var current_id = arr_id[2]; 
		$.ajax({
			type: "POST",
			url: url,
			data:{'user':current_id},
			dataType:"json",	
			success: function(data) {					 
			  if(data.success){
					 $("#list_member_approve_"+current_id).remove();
				}
				else{
					alert(data.msg)
				}
			}
		});
	});
	$(document).on("click",".remove_members",function(event){
		var url = baseurl+'/groups/'+galexy+'/'+planet+'/RemoveMemberRequest';
		var id = $(this).attr("id"); 
		var arr_id = id.split("_"); 
		var current_id = arr_id[2]; 
		$.ajax({
			type: "POST",
			url: url,
			data:{'user':current_id},
			dataType:"json",	
			success: function(data) {					 
			  if(data.success){
					 $("#list_member_approve_"+current_id).remove();
				}
				else{
					alert(data.msg)
				}
			}
		});
	});
	$(document).on("click",".add_to_activities",function(event){
		var url = baseurl+'/activity/'+galexy+'/'+planet+'/approveActivity';
		var id = $(this).attr("id"); 
		var arr_id = id.split("_"); 
		var current_id = arr_id[3]; 
		$.ajax({
			type: "POST",
			url: url,
			data:{'activity':current_id},
			dataType:"json",	
			success: function(data) {					 
			  if(data.success){
					 $("#list_activity_approve_"+current_id).remove();
				}
				else{
					alert(data.msg)
				}
			}
		});
	});
	$(document).on("click",".ignore_activity",function(event){
		var url = baseurl+'/activity/'+galexy+'/'+planet+'/IgnoreActivity';
		var id = $(this).attr("id"); 
		var arr_id = id.split("_"); 
		var current_id = arr_id[2]; 
		$.ajax({
			type: "POST",
			url: url,
			data:{'activity':current_id},
			dataType:"json",	
			success: function(data) {					 
			  if(data.success){
					 $("#list_activity_approve_"+current_id).remove();
				}
				else{
					alert(data.msg)
				}
			}
		});
	});
	$(document).on("click",".remove_activity",function(event){
		var url = baseurl+'/activity/'+galexy+'/'+planet+'/removeActivity';
		var id = $(this).attr("id"); 
		var arr_id = id.split("_"); 
		var current_id = arr_id[2]; 
		$.ajax({
			type: "POST",
			url: url,
			data:{'activity':current_id},
			dataType:"json",	
			success: function(data) {					 
			  if(data.success){
					 $("#list_activity_approve_"+current_id).remove();
				}
				else{
					alert(data.msg)
				}
			},
			error:function(){
				alert(error);
			}
		});
	});
	$(document).on("click","input:radio[name=planet_members]",function(event){
		if($("input:radio[name=planet_members]:checked").val() == 'Any'||$("input:radio[name=planet_members]:checked").val() == 'AdminApproval'){
			$("#joining_questionnaire_outer").show();
		}else{
			$("#joining_questionnaire_outer").hide();
		}
	});
	var Questions = new Array();
	$(document).on("click",".edit_question",function(event){
		var id = $(this).attr("id");
		var arr_id = id.split("_");
		var question_id = arr_id[2];
		$(this).hide();
		Questions[question_id] = $("#question_"+question_id).text();
		Questions[question_id] = $("#question_"+question_id).text();
		Questions[question_id] = $("#question_"+question_id).text();
		var url = baseurl+'/groups/'+galexy+'/'+planet+'/editQuestion';		
		$.ajax({
			type: "POST",
			url: url,
			data:{'question_id':question_id},			 
			success: function(data) {							 
				$("#question_"+question_id).html(data);	
				
			},
			error:function(){
				alert(error);
			}
		});	 
	 
	});
	$(document).on("click","#questionnaire_list .grey-butn",function(event){ 
		var id = $(this).attr("id"); 
		var arr_id = id.split("_");
		var question_id = arr_id[1];
		var question_content = Questions[question_id];		
		var question_buttons = '<a href="javascript:void(0)" id="edit_question_'+question_id+'" class="edit_question">Edit</a>';
		$("#question_"+question_id).html(question_content);
		$("#question_button_"+question_id).html(question_buttons);		
	});
	$(document).on("click","#questionnaire_list .blue-butn",function(event){ 
		var id = $(this).attr("id"); 
		var arr_id = id.split("_");
		var question_id = arr_id[1];
		var url = baseurl+'/groups/'+galexy+'/'+planet+'/updateQuestion';		
		var Question = $("#question"+question_id).val();
		var answer_type = '';
		if($('input:radio[name=q'+question_id+'_answer_type]:checked').length){
			answer_type = $('input:radio[name=q'+question_id+'_answer_type]:checked').val();
		}
		var option1 = '';
		var option2 = '';
		var option3 = '';
		if(answer_type == 'radio' || answer_type == 'checkbox'){
			option1 = $("#q"+question_id+"_option_1").val();
			option2 = $("#q"+question_id+"_option_2").val();
			option3 = $("#q"+question_id+"_option_3").val();
		}
		//$("#question_"+question_id).html('<img src="'+baseurl+'/public/images/ajax_loader.gif" />');
		$.ajax({
			type: "POST",
			url: url,
			data:{'question_id':question_id,'question':Question,'answer_type':answer_type,'option1':option1,'option2':option2,'option3':option3},
			dataType:"json",	
			success: function(data) {					 
			  if(data.success){
					var question_content = Question;		
					var question_buttons = '<a href="javascript:void(0)" id="edit_question_'+question_id+'" class="edit_question">Edit</a>';
					$("#question_"+question_id).html(question_content);
					$("#question_button_"+question_id).html(question_buttons);	 
				}
				else{
					alert(data.msg)
				}
			},
			error:function(){
				alert(error);
			}
		});
	});
	$(document).on("click","#chk_questionnaire",function(event){
		if ($("#chk_questionnaire").is(":checked")){
		 $("#questionnaire_list").animate({  height:'toggle' });	
		}else{
		 $("#questionnaire_list").animate({  height:'toggle' });
		 $("#questionnaire_list").hide('slow');
		}
	});
	$(document).on("click","#add_question",function(event){
		event.stopPropagation();
	    event.preventDefault();
		var formvalues = new Array();
		var formData = new FormData();
		if($("#question1").length){			 
			formData.append("question1",$("#question1").val());
		}
		if($("#question2").length){			 
			formData.append("question2",$("#question2").val());
		}
		if($("#question1").length){			 
			formData.append("question3",$("#question3").val());
		}
		if($('input:radio[name=addq1_answer_type]:checked').length){
			formData.append("q1_answer_type",$('input:radio[name=addq1_answer_type]:checked').val());
			if($('input:radio[name=addq1_answer_type]:checked').val()=='radio'||$('input:radio[name=addq1_answer_type]:checked').val()=='checkbox'){
				formData.append("q1_option_1",$("#q1_option_1").val());
				formData.append("q1_option_2",$("#q1_option_2").val());
				formData.append("q1_option_3",$("#q1_option_3").val());
			}
		}
		if($('input:radio[name=addq2_answer_type]:checked').length){
			formData.append("q2_answer_type",$('input:radio[name=addq2_answer_type]:checked').val());
			if($('input:radio[name=addq2_answer_type]:checked').val()=='radio'||$('input:radio[name=addq2_answer_type]:checked').val()=='checkbox'){
				formData.append("q2_option_1",$("#q2_option_1").val());
				formData.append("q2_option_2",$("#q2_option_2").val());
				formData.append("q2_option_3",$("#q2_option_3").val());
			}
		}
		if($('input:radio[name=addq3_answer_type]:checked').length){
			formData.append("q3_answer_type",$('input:radio[name=addq3_answer_type]:checked').val());
			if($('input:radio[name=addq3_answer_type]:checked').val()=='radio'||$('input:radio[name=addq3_answer_type]:checked').val()=='checkbox'){
				formData.append("q3_option_1",$("#q3_option_1").val());
				formData.append("q3_option_2",$("#q3_option_2").val());
				formData.append("q3_option_3",$("#q3_option_3").val());
			}
		}
		var url = baseurl+'/groups/'+galexy+'/'+planet+'/AddQuestion';			 	 
		$.ajax({
			type: "POST",
			url: url,
			data:formData,
			dataType:"json",
			processData: false, 
			contentType: false,
			success: function(data) {					 
			  if(data.success){
					$("#questionnaire_list").html(data.msg);
				}
				else{
					alert(data.msg)
				}
			},
			error:function(){
				alert(error);
			}
		});
	});	
	$(document).on("click",".answer_type",function(event){	
		var id = $(this).attr("id");
		arr_id = id.split("_");
		if( $(this).val()=='radio'||$(this).val()=='checkbox'){
			$("#"+arr_id[0]+"_option").show();
		}else{
			$("#"+arr_id[0]+"_option").hide();
		}
		
		
	});
	$(document).on("click",".view_questionnaire",function(event){	
		var id = $(this).attr("id");
		arr_id = id.split("_");
		var user_id = arr_id[2];
		$(".questionnaire_answer_container").animate({  height:'toggle' });
		$(".questionnaire_answer_container").html('<img src="'+baseurl+'/public/images/ajax_loader.gif" />');
		var url = baseurl+'/groups/'+galexy+'/'+planet+'/getUserQuestionnaire';		
		$.ajax({
			type: "POST",
			url: url,
			data:{'user_id':user_id},		 	
			success: function(data) {		   
					$(".questionnaire_answer_container").html(data);			 
			},
			error:function(){
				alert(error);
			}
		});
	});
	var q1_optionflag = 0; 
	var q2_optionflag = 0; 
	var q3_optionflag = 0; 
	$(document).on("click","input:radio[name=addq1_answer_type]",function(event){
		if($('input:radio[name=addq1_answer_type]:checked').val()=='radio'||$('input:radio[name=addq1_answer_type]:checked').val()=='checkbox'){
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
	$(document).on("click","input:radio[name=addq2_answer_type]",function(event){
		if($('input:radio[name=addq2_answer_type]:checked').val()=='radio'||$('input:radio[name=addq2_answer_type]:checked').val()=='checkbox'){
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
	$(document).on("click","input:radio[name=addq3_answer_type]",function(event){
		if($('input:radio[name=addq3_answer_type]:checked').val()=='radio'||$('input:radio[name=addq3_answer_type]:checked').val()=='checkbox'){
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