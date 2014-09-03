var settings_group_page = 1;
var bio_content = '';
var uploadfiles = new Array();;
var uploadcover = new Array();;
var leftvalue = 0;
var topvalue = 0;
$(function() {
	var profile = new Array();
	$("#show_hide_settings").click(function(){		
		$("#profile-settings-container").animate({  height:'toggle' });	 
	});
	$("#profile-general-setttings").click(function(){		
		$("#general-settings-container").animate({  height:'toggle' });
		if($("#profile-general-setttings").hasClass("head_active")){	
			$("#profile-general-setttings").removeClass("head_active");
		}else{
			$("#profile-general-setttings").addClass("head_active");
		}		
	});
	$("#profile-email-settings").click(function(){		
		$("#email-settings-container").animate({  height:'toggle' });
		if($("#profile-email-settings").hasClass("head_active")){	
			$("#profile-email-settings").removeClass("head_active");
		}else{
			$("#profile-email-settings").addClass("head_active");
		}		
	});
	$("#profile-other-settings").click(function(){		
		$("#other-settings-container").animate({  height:'toggle' });
		if($("#profile-other-settings").hasClass("head_active")){	
			$("#profile-other-settings").removeClass("head_active");
		}else{
			$("#profile-other-settings").addClass("head_active");
		}		
	});	
	$(document).on("click",".edit_profile",function(){
		var id = $(this).attr("id");
		$("#"+id).hide();
		var arr_id = id.split("_"); 
		profile[arr_id[1]]  = $("#text_"+arr_id[1]).text();
		$("#profile-action").show();
		switch(arr_id[1]){
			case "gender":
				if(profile[arr_id[1]] =='male'){
				var content = '<input type="radio" name="field_'+arr_id[1]+'" id="field_'+arr_id[1]+'_m" value="male" checked="checked" /> Male <input type="radio" name="field_'+arr_id[1]+'" id="field_'+arr_id[1]+'_f" value="Female" /> Female';
				}else{
					var content = '<input type="radio" name="field_'+arr_id[1]+'" id="field_'+arr_id[1]+'_m" value="male" /> Male <input type="radio" name="field_'+arr_id[1]+'" id="field_'+arr_id[1]+'_f" value="Female" checked="checked" /> Female';
				}
				$("#text_"+arr_id[1]).html(content);
			break;
			case "location":
				var content = '<input type="text" name="field_'+arr_id[1]+'" id="field_'+arr_id[1]+'" value="'+profile[arr_id[1]]+'" />';
				$("#text_"+arr_id[1]).html(content);
				var input = document.getElementById('field_'+arr_id[1]);
				var options = {
					componentRestrictions: {country: 'au'}
				};
				var autocomplete = new google.maps.places.Autocomplete(input, options);
			break;
			case "dob":
				var content = '<input type="text" name="field_'+arr_id[1]+'" id="field_'+arr_id[1]+'" value="'+profile[arr_id[1]]+'" />';
				$("#text_"+arr_id[1]).html(content);
				$('#field_'+arr_id[1]).datepicker({ dateFormat: "yy-mm-dd",changeYear: true });
			break;
			case "country":
				var url = baseurl+'/country/ajaxCountrySelect';		 
				$.ajax({
				type: "POST",
				url:url,
				data:{'country':profile[arr_id[1]]},				
				success:function(result) {
					if(result){
						$("#text_"+arr_id[1]).html(result);						 
					}else{
						$("#text_"+arr_id[1]).html('Some error occured. Please try again');
					}			
				}});
			break;
			case "city":
				var url = baseurl+'/city/ajaxCitySelect';		 
				$.ajax({
				type: "POST",
				url:url,
				data:{'country':profile['country']},				
				success:function(result) {
					if(result){
						$("#text_"+arr_id[1]).html(result);						 
					}else{
						$("#text_"+arr_id[1]).html('Some error occured. Please try again');
					}			
				}});
			break;
			default:
				var content = '<input type="text" name="field_'+arr_id[1]+'" id="field_'+arr_id[1]+'" value="'+profile[arr_id[1]]+'" />';
				$("#text_"+arr_id[1]).html(content);
		}
	});
	$(document).on("change","#country_list",function(){
		var country_id = $(this).val();
		var url = baseurl+'/city/ajaxCities';
		profile['city']  = $("#text_city").text();
		  $("#text_city").html("<img src='"+baseurl+"/public/images/ajax_loader.gif' />");
			$.ajax({
			type: "POST",
			url:url,
			data:{country_id:country_id},		
			success:function(result) {
				  $("#text_city").html(result);	
				}
			});
	});
	$(document).on("click","#cancel_profile",function(){
		if(profile['firstname']!=undefined){
			$("#text_firstname").html(profile['firstname'])
			$("#edit_firstname").show();
		}
		if(profile['middlename']!=undefined){
			$("#text_middlename").html(profile['middlename'])
			$("#edit_middlename").show();
		}
		if(profile['lastname']!=undefined){
			$("#text_lastname").html(profile['lastname'])
			$("#edit_lastname").show();
		}
		if(profile['givenname']!=undefined){
			$("#text_givenname").html(profile['givenname'])
			$("#edit_givenname").show();
		}
		if(profile['email']!=undefined){
			$("#text_email").html(profile['email'])
			$("#edit_email").show();
		}
		if(profile['mobile']!=undefined){
			$("#text_mobile").html(profile['mobile'])
			$("#edit_mobile").show();
		}
		if(profile['location']!=undefined){
			$("#text_location").html(profile['location'])
			$("#edit_location").show();
		}
		if(profile['dob']!=undefined){
			$("#text_dob").html(profile['dob'])
			$("#edit_dob").show();
		}
		if(profile['professionat']!=undefined){
			$("#text_professionat").html(profile['professionat'])
			$("#edit_professionat").show();
		}
		if(profile['profession']!=undefined){
			$("#text_profession").html(profile['profession'])
			$("#edit_profession").show();
		}
		if(profile['gender']!=undefined){
			$("#text_gender").html(profile['gender'])
			$("#edit_gender").show();
		}
		if(profile['country']!=undefined){
			$("#text_country").html(profile['country'])
			$("#edit_country").show();
		}
		if(profile['city']!=undefined){
			$("#text_city").html(profile['city'])
			$("#edit_city").show();
		}
		$("#profile-action").hide();
	});
	$(document).on("click","#update_profile",function(event){		
		event.stopPropagation();
	    event.preventDefault();
		var formData = new FormData();
		if($("#field_firstname").length){			 
			formData.append("field_firstname",$("#field_firstname").val());
		}
		if($("#field_middlename").length){			 
			formData.append("field_middlename",$("#field_middlename").val());
		}
		if($("#field_lastname").length){			 
			formData.append("field_lastname",$("#field_lastname").val());
		}
		if($("#field_givenname").length){			 
			formData.append("field_givenname",$("#field_givenname").val());
		}
		if($("#field_email").length){			 
			formData.append("field_email",$("#field_email").val());
		}
		if($("#field_mobile").length){			 
			formData.append("field_mobile",$("#field_mobile").val());
		}
		if($("#field_location").length){			 
			formData.append("field_location",$("#field_location").val());
		}
		if($("#field_dob").length){			 
			formData.append("field_dob",$("#field_dob").val());
		}
		if($("#field_professionat").length){			 
			formData.append("field_professionat",$("#field_professionat").val());
		}
		if($("#field_profession").length){			 
			formData.append("field_profession",$("#field_profession").val());
		}
		if($('input:radio[name=field_gender]:checked').length){			 
			formData.append("field_gender",$('input:radio[name=field_gender]:checked').val());
		}
		if($("#country_list").length){			 
			formData.append("country_list",$("#country_list").val());
		}
		if($("#user_profile_city").length){			 
			formData.append("user_profile_city",$("#user_profile_city").val());
		}
		if($("#email_varification").length){			 
			formData.append("email_validation_code",$("#email_varification").val());
		}
		formData.append("type","profile");
		var url			 = baseurl + "/profile/settings";
		$.ajax({
			type: "POST",
			url:url,
			dataType:"json",
			processData: false, 
			contentType: false,			
			data:formData,		
			success:function(result) {
				  if(result.success){
					window.location.reload();
				  }else{
					alert(result.msg);
					if(result.email_varification){
						$("#email_varification_code").html('<div class="settings-label">Varification Code:</div><div class="settings-field" id="text_givenname"><input type="text" id="email_varification" name="email_varification" /></div><div class="clear"></div>');
					}
				  }
			}
		});
	});	
	$(document).on("click",".group_settings",function(event){  
		var group_id = $(this).attr("id");
		$("#group-settings-container-"+group_id).animate({  height:'toggle' });
		if($("#"+group_id).hasClass("head_active")){	
			$("#"+group_id).removeClass("head_active");
		}else{
			$("#"+group_id).addClass("head_active");
		}	
	});
	$(document).on("click",".grp-save",function(event){  	 
		var id			= this.id.replace('grp-save-','');
		var group_id 	= $('#setting-grpid-'+id).val();	 	 
		var url			= baseurl + "/profile/settings";
		var activity	= $('input[name="activity_alert_'+id+'"]:checked').val();	
		var media		= $('input[name="media_alert_'+id+'"]:checked').val();	
		var discussion	= $('input[name="discussion_alert_'+id+'"]:checked').val(); 
		var member		= $('input[name="member_alert_'+id+'"]:checked').val();
		var announcement= $('input[name="announcement_alert_'+id+'"]:checked').val();
		$.ajax({
			type: "POST",
			url: url,
			dataType:"json",
			data:{				 
				'group_id':group_id,
				'activity':activity,
				'media':media,
				'discussion':discussion,
				'member':member,
				'announcement':announcement,
				'type':'group',
			}, 
			success:function(result) {
				if(result.success){
					window.location.reload();
				}else{
					alert(result.msg);
				}
			}
		});
	});
$(document).on("click","#group_settings_loadmore",function(event){
	$(this).remove();
	var url			 = baseurl + "/profile/settingsGroupLoadmore";	
	$.ajax({
		type: "POST",
		url: url,			 
		data:{				 
			'page':settings_group_page,				 
		}, 
		success:function(result) {
			  if(result){
					$("#email-settings-container").append(result);
				   settings_group_page++;
			  } 
			
			}
		});
});
$(document).on("click","#other-save",function(event){ 
	var alerts = new Array();
	var i = 0;
	var url			 = baseurl + "/profile/settings";
	
	$('input[name="email_alert"]').each(function() {
            if (this.checked) { 
                alerts[i] =$(this).val() ;
				i++;
            }
    });
	var connection = $('input[name="contact_req"]:checked').val();	
	var message = $('input[name="message_setting"]:checked').val();	
		
	$.ajax({
		type: "POST",
		url: url,
		dataType:"json",
		data:{				     
			'alerts':alerts,
			'connection':connection,
			'message':message,
			'type':'general',
		}, 
		success: function(result)
		{
		
		 if(result.success){
					window.location.reload();
				  }else{
					alert(result.msg);
				  }
		}
	});
});
	$(document).on("click","#edit-bio",function(event){
		var content = '';
		bio_content = $("#bio-text-profile").text();
		content  = '<textarea id="bio-text" name="bio-text">'+bio_content+'</textarea><div class="bio-edit-butns"><input type="button" id="save_bio" class="blue-butn" value="Save"/><input type="button" id="cancel_bio" class="grey-butn" value="Cancel" /></div>';
		$("#bio-text-profile").html(content);
		$("#edit-bio").hide();
	});
	$(document).on("click","#cancel_bio",function(event){
		$("#bio-text-profile").html(bio_content);
		$("#edit-bio").show();
	});
	$(document).on("click","#save_bio",function(event){
		 var bio = $("#bio-text").val();
		 var url			 = baseurl + "/profile/updatebio";
		 $.ajax({
			type: "POST",
			url: url,
			dataType:"json",
			data:{				     
				'bio':bio,				 
			}, 
			success: function(result)
			{
			
			 if(result.success){
				$("#bio-text-profile").html(bio);	
				bio_content = bio;
				$("#edit-bio").show();
				}else{
				alert(result.msg);
			  }
			}
			});
		});
	$("#interest_close").click(function(){
     $("#interest_popout").hide();
	 $("#show_interests").show();
	});
	
	$("#show_interests").click(function(){
		var url			 = baseurl + "/tag/listExceptSelected";
		$("#interest_popout").show();
		 $(".planet-tags").html('<img src="'+baseurl+'/public/images/ajax_loader.gif" />');
		$.ajax({
			type: "POST",
			url: url,			 
			data: '', 
			success: function(result)
			{
			
			 if(result){
				 $(".planet-tags").html(result);
			  }
			}
			});
		$("#show_interests").hide();
	});
	$(document).on("click","#btn_tag_search",function(event){
		 var search_str = $("#tag_search_text").val();
		 $(".planet-tags").html('<img src="'+baseurl+'/public/images/ajax_loader.gif" />');
		 var url			 = baseurl + "/tag/searchTags";
		 $.ajax({
			type: "POST",
			url: url,			 
			data: {'search_str':search_str}, 
			success: function(result)
			{
			
			 if(result){
				 $(".planet-tags").html(result);
			  }
			}
			});
		 
	});
	$(document).on("click",".add-tag",function(event){
		 var id = $(this).attr("id"); 
		
		 var arr_id = id.split("_");	
		 $("#tag_cnt_"+arr_id[1]).remove();
		 var url	= baseurl + "/profile/addTag";
		 $.ajax({
			type: "POST",
			url: url,			 
			data: {'tag_id':arr_id[1]}, 
			dataType:"json",
			success: function(result)
			{
			
			 if(result.success){
				 $("#user_tags").append(result.msg);
			  }else{
				alert(result.msg);
			  }	
			}
			});
		 
	});
	$(document).on("click",".remove_tag",function(event){
		 var id = $(this).attr("id"); 
		
		 var arr_id = id.split("_");	
		 $("#tag_cnt_"+arr_id[2]).remove();
		 var url	= baseurl + "/profile/removeTag";
		 $.ajax({
			type: "POST",
			url: url,			 
			data: {'tag_id':arr_id[2]}, 
			dataType:"json",
			success: function(result)
			{
			
			 if(result.success){
				 $("#user_tag_added_"+arr_id[2]).remove();
			  }else{
				alert(result.msg);
			  }	
			}
			});
		 
	});
	
	$(document).on("click","#edit_password",function(event){
		var content = '';
		content+= '<div class="settings-field-outer"> <div class="settings-label">Current :</div><div id="current_pass_field" class="settings-field"><input type="password" name="current_pass" id="current_pass" ></div><div class="clear"></div></div>';
		content+= '<div class="settings-field-outer"><div class="settings-label">New :</div><div id="new_pass_field" class="settings-field"><input type="password" name="new_pass" id="new_pass" ></div><div class="clear"></div></div>';
		content+= '<div class="settings-field-outer"><div class="settings-label">Re-Type New :</div><div id="re_pass_field" class="settings-field"><input type="password" name="re_pass" id="re_pass"></div><div class="clear"></div></div>';
		content+= ' <div class="change-password-butns"><input type="button" id="pass_update" class="pass_update blue-butn" value="Update">&nbsp;<input type="button" id="pass_cancel" class="pass_cancel grey-butn" value="Cancel"><div class="clear"></div></div>';
		$("#password_field").html(content);
	});
	$(document).on("click","#pass_cancel",function(event){
		$("#password_field").html('');
	});
	$(document).on("click","#pass_update",function(event){
		var current_pass =  $("#current_pass").val();
		var new_pass =  $("#new_pass").val();
		var re_pass =  $("#re_pass").val();
		var flag = 0;
		if(current_pass == ''){
			alert('Enter current password');
			flag++;
		}
		if(new_pass == ""){
			alert('Enter New password');
			flag++;
		}
		if(re_pass == ""){
			alert('Re type password');
			flag++;
		}else {   
		if(new_pass != re_pass){
			alert('Re type password not correct');
			flag++;
		}
		}
		if(flag == 0){
			var url = baseurl+'/user/changepassword';
			$.ajax({
				type: "POST",
				url: url,
				dataType:"json",
				data:{
					'new_pass': new_pass,
					're_pass':re_pass,
					'current_pass':current_pass,					
				}, 
				success: function(result)
				{  
					if(result.success){
					 $("#password_field").html('');
				  }else{
					alert(result.msg);
				  }	
				}
				 
			});
		}else{
              return false;
       }
	});
	$(document).on("mouseenter","#myprofile_pic",function(event){
		 $("#myprofile_upload_pic").fadeIn("slow");
	});
	$(document).on("click","#file_profile_pic",function(){
		$(this).parent().find('input').click();
	});
	$(document).on("mouseout","#myprofile_pic",function(event){
		 $("#myprofile_upload_pic").fadeOut("slow");
	});
	$(document).on("change","#profile_pic",function(event){
		uploadfiles = event.target.files;
		input = document.getElementById("profile_pic");
		     if (input.files && input.files[0]) {
				var formData = new FormData();		 
				 $.each(uploadfiles, function(key, value)
				{
					formData.append(key, value); 
				});
				$("#profile_popup_crop").html('<img src="'+baseurl+'/public/images/ajax_loader.gif" />');
				var url	= baseurl + "/profile/addProfilePicToTemp";
				 $.ajax({
						type: "POST",
						url: url,
						data: formData,
						processData: false, 
						contentType: false,
						success: function(result) {				
						 if(result){
							 		
								$("#profile_popup_crop").html(result);
									$('#cropbox').Jcrop({
									aspectRatio: 1,
										onSelect: updateCoords
									});
							 }else{
								alert("Some error occured");
								// window.location.reload();
							 }
						}
					});
              /*  var reader = new FileReader();

                reader.onload = function(e) {
					var content = '<img src="'+ e.target.result+'" width="400px" id="cropbox" />'			 
					$("#profile_popup_crop").html(content);
					$('#cropbox').Jcrop({
					aspectRatio: 1,
						onSelect: updateCoords
					});
                }

                reader.readAsDataURL(input.files[0]); */
				loadPopupBox();
				
			}
		 
	});
	$(document).on("click","#profile_popup_close",function(event){   
        unloadPopupBox();	 

    });
	function unloadPopupBox() {    // TO Unload the Popupbox
            $('#profile_popup').fadeOut("slow");
            $(".view-outer-container").css({ // this is just for style        
                "opacity": "1"  
            });  
        }    
 function loadPopupBox() {    // To Load the Popupbox
            $('#profile_popup').fadeIn("slow");
             $(".view-outer-container").css({ // this is just for style
                "opacity": "1"  
            });           
        } 
		var Xvalue = 0;
var Yvalue = 0;
var Wvalue = 0;
var Hvalue = 0;
function updateCoords(c)
  {
	Xvalue    = c.x;
	Yvalue    = c.y;
	Wvalue    = c.w;
	Hvalue    = c.h; 
   //$('#x').val(c.x);
    //$('#y').val(c.y);
    //$('#w').val(c.w);
    //$('#h').val(c.h);
  };

  function checkCoords()
  {
    if (parseInt($('#w').val())) return true;
    alert('Please select a crop region then press submit.');
    return false;
  };
  $(document).on("click","#creat_profile_pic",function(event){
	//	var formData = $("#messagefrm").serialize();
	event.stopPropagation();
	   event.preventDefault();
		var formData = new FormData();		 
		 $.each(uploadfiles, function(key, value)
		{
			formData.append(key, value); 
		});
		formData.append("Xvalue",Xvalue);
		formData.append("Yvalue",Yvalue);
		formData.append("Wvalue",Wvalue);
		formData.append("Hvalue",Hvalue);
		formData.append("filename",$("#filename").val());
		 
		 //formData.append( "message_file", $("#message_file")[0].files[0]);
		var url	= baseurl + "/profile/addProfilePic";
		$("#profile_pic_butn").html('<img src="'+baseurl+'/public/images/ajax_loader.gif" />');
		 $.ajax({
				type: "POST",
				url: url,
				data: formData,
				processData: false, 
				contentType: false,
				success: function(result) {				
				 if(result.error){
					$("#profile_pic_butn").html('<input type="button" id="creat_profile_pic" class="blue-butn" value="Save" />');
					 alert(result.msg);			
					 }else{
						 
						 window.location.reload();
					 }
				}
			});
	});
	$(document).on("mouseenter","#myprofile_cover",function(event){
		 $("#myprofile_upload_cover").fadeIn("slow");
	});
	$(document).on("click","#file_cover_pic",function(event){
		  event.preventDefault();
		$(this).parent().find('#cover_pic').click();
	});
  $(document).on("mouseout","#myprofile_cover",function(event){
		 $("#myprofile_upload_cover").fadeOut("slow");
	});
	$(document).on("change","#cover_pic",function(event){
		uploadcover = event.target.files;
		input = document.getElementById("cover_pic");
		     if (input.files && input.files[0]) {
				
                var reader = new FileReader();

                reader.onload = function(e) {
					var content = '<img src="'+ e.target.result+'" />'			 
					$("#myprofile_cover").html(content);
					$( "#myprofile_cover" ).draggable({
					  stop: function( event, ui ) {
						var helper = ui.helper
						pos = ui.position;
						leftvalue    = pos.left; 
						topvalue    = pos.top;
						$("#btn_cover_pic").show();
					  }
					}); 
                }

                reader.readAsDataURL(input.files[0]);
				
			}
	});
	$(document).on("click","#btn_cover_pic",function(event){
		event.stopPropagation();
	    event.preventDefault();
		var formData = new FormData();		 
		 $.each(uploadcover, function(key, value)
		{
			formData.append(key, value); 
		});
		formData.append("leftvalue",leftvalue);
		formData.append("topvalue",topvalue);
		 
		 
		 //formData.append( "message_file", $("#message_file")[0].files[0]);
		var url	= baseurl + "/profile/addProfileCover";
		 $.ajax({
				type: "POST",
				url: url,
				data: formData,
				processData: false, 
				contentType: false,
				success: function(result) {				
				 if(result.error){
					 alert(result.msg);			
					 }else{
						 
						 window.location.reload();
					 }
				}
			});
	});
	$(document).on("click","#invite_friend",function(event){
		$("#invite_friend_container").toggle("slow");
		 $("#invite-msg").html("");	
	});
	$(document).on("click","#btn_invite",function(event){
		var firstname_invite = $("#firstname_invite").val();
		var email_invite = $("#email_invite").val();
		var url	= baseurl + "/profile/inviteFriends";
		 $.ajax({
				type: "POST",
				url: url,
				data: {'firstname_invite':firstname_invite,'email_invite':email_invite},
				dataType:"json",			
				success: function(result) {
		
				 if(result.error == 1){
				
				  $("#invite-msg").removeClass("msg_succ");
				  $("#invite-msg").addClass("msg_err");
				  $("#invite-msg").html(result.msg);
							
					 }else{	
					 
					         $("#invite-msg").removeClass("msg_err");
							 $("#invite-msg").addClass("msg_succ");
							 $("#invite-msg").html(result.msg);	
							 $("#firstname_invite").val('');
							 $("#email_invite").val('');
							 $("#invite_friend_container").delay(1000).toggle("slow");
							
					 }
				}
			});
	});
	$(document).on("click",".privacy_settings",function(event){
		var id = $(this).attr("id");
		$("#"+id+"_visibility").toggle();
	});
	$(document).on("click",".privacy_select",function(event){
		var id = $(this).attr("name");
		var value = $(this).val();
		$("input[name='"+id+"']").parent().append('<div id="'+id+'_ajax_loader" class="privacy_ajax_loader"><img src="'+baseurl+'/public/images/ajax_loader.gif" /></div>');
		var url	= baseurl + "/profile/savePrivacySettings";
		 $.ajax({
				type: "POST",
				url: url,
				data: {'field':id,'value':value},
				dataType:"json",
				success: function(result) {				
				 if(result.error){
					alert(result.msg);	
					$("#"+id+"_ajax_loader").remove(); 					 
				 }else{
					$("#"+id+"_ajax_loader").remove();
					$("input[name='"+id+"']").parent().hide();
				 }
				}
			});
	});
	
});
