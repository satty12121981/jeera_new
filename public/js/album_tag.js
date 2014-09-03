jQuery(document).ready(function()
{
     
	var counter = 0;
    var mouseX  = 0;
    var mouseY  = 0;
	$(document).on("click",".done-tag",function(event){	 	 
	var valu = $(this).attr("id");
	var id = valu.replace('done-tag-','');
	$('#tagit').remove(); 
	$('#album-img-'+id).removeClass('tag-img');
	$('#done-tag-'+id).hide();
	$('#add-tag-'+id).show();
	});
	
	$(document).on("click",".add-tag",function(event){	 
	var valu = $(this).attr("id");
	var id = valu.replace('add-tag-','');
	$('#data_id').val(id);
	$('#album-img-'+id).addClass('tag-img');
	$('#add-tag-'+id).hide();
	$('#done-tag-'+id).show();
	});

    $(document).on("click",".tag-img img",function(event){
    
      var imgtag = $(this).parent().attr("id"); 
	  //alert(imgtag);
	  imgtag = "#"+imgtag;
      mouseX = event.pageX - $(imgtag).offset().left;
      mouseY = event.pageY - $(imgtag).offset().top;
      $('#tagit').remove(); 
      $(imgtag).append('<div id="tagit"><div class="box"></div><div class="name"><input type="text" name="tagname" id="tagname" /></div></div>');
      $('#tagit').css({top:mouseY,left:mouseX});
      
      $('#tagname').focus();
    });
	 $(document).on("enterKey","#tagname",function(event){	 
      name = $('#tagname').val();
	  //alert(name);
      counter++;
	  var data_id = $('#data_id').val();
	  var tag_id  = $('#tag_hid_id').val();
	  var xvalue = 1;
	  if(mouseX&&mouseX!=''){
		xvalue = mouseX;
	  }
	  if(mouseY&&mouseY!=''){
		yvalue = mouseY;
	  }
	  $.ajax({
		 type: "post",		  
		 url:baseurl +'/album/addTag/'+data_id+'/'+tag_id+'/'+xvalue+'/'+yvalue,
		 success:function(result){ 
			if(result){
			//alert (1);
			var display_tag = '<span class="album_tags" id="list_tag_'+result+'_'+tag_id+'"><a class="view_tag" href="#" id="view_tag_'+result+'|'+tag_id+'">'+name+'</a> (<a id="del_tag_'+result+'_'+tag_id+'" class="remove_tag" style="cursor:pointer;">X</a>)</span>';
			$('#list-tag-'+data_id).append(display_tag);
			var display_box = '<div class="tagview" id="user_tag_'+result+'_'+tag_id+'" ></div>';
			$('#album-img-'+data_id).append(display_box);
			$('#user_tag_'+result+'_'+tag_id).css({top:mouseY,left:mouseX});
			$('#tagit').fadeOut();
			}else{
				alert('sorry error occured');
			}
			},
		 error:function(){}
	 });
	  return false;
      

    });
	$(document).on("keyup","#tagname",function(event){		
		if(event.keyCode == 13)
		{
			$(this).trigger("enterKey");
			
		}
		 
		var path = baseurl+"/album/usertagquery/"+planet;
		$(this).autocomplete({
			source: path,
			minLength: 1,
			select: function (event, ui) {
			  $("#tag_hid_value").val(ui.item.value); 
			  $("#tag_hid_id").val(ui.item.id); 
		}
		});
	
	});
    $(document).on("click","#tagit #btncancel",function(event){	       
      $('#tagit').fadeOut();
      
    });
$(".view_tag").hover(function(event){
  
 
	   var valu =  $(this).attr('id');
	   var id = valu.replace('view_tag_','');
	   var res = id.split('|');
	   var tag_id = res[0];
	   var user_id = res[1];
	  
	   $("#user_tag_"+tag_id+"_"+user_id).show();
 
  });
  $(document).on("mouseleave",".view_tag",function(event){ 	
   
	   var valu =  $(this).attr('id');
	   var id = valu.replace('view_tag_','');
	   var res = id.split('|');
	   var tag_id = res[0];
	   var user_id = res[1];
	  
	   $("#user_tag_"+tag_id+"_"+user_id).hide();
 
  });
  $(document).on("click",".remove_tag",function(event){ 	
      
	   var valu =  $(this).attr('id');
	   var id = valu.replace('del_tag_','');
	   var res = id.split('_');
	   var tag_id = res[0];
	   var user_id = res[1];
	   $.ajax({
		 //type: "GET",
		 url:baseurl +'/album/deleteTag/'+tag_id,
		 success:function(result){ 
			if(result == 'success'){
			$("#user_tag_"+id).remove();
			$("#list_tag_"+id).remove();
			
			}else{
				alert('sorry error occured');
			}
			},
		 error:function(){}
	 });
	  
	   
 
  });
  
});
