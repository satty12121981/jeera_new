var uploaded_files  = new Array();
var file_count = 0;
var scrollend =0;
jQuery(document).ready(function()
{
$("#add_albums").click(function(){
		$("#add_albums_container").animate({  height:'toggle' });
	});
	$("#album_add_more").click(function(){
		$("#add_albums_container").animate({  height:'toggle' });
		$("#album_edit_view").hide();
	});
	
$(document).on("click","#submit_update",function(event){ 
 event.preventDefault(); 
	var url = $("#edit_album").attr("action");	 
	 $.ajax({   
			type: "POST",
			data : $("#edit_album").serialize(),
			cache: false,  
			url: url,   
			dataType:"json",
			success: function(result){
				 if(result.error){
				 alert(result.msg);
				
				 }else{
					if(result.msg!=''){
					alert(result.msg);
					}
					 window.location.reload();
				 }
			                     
			} 
	});
});
$(document).on("click","#delete_albums",function(event){ 
 event.preventDefault(); 
	var url = $("#delete_albums").attr("href");	 
	 $.ajax({   
			type: "POST",
			data :'',
			cache: false,  
			url: url,   
			dataType:"json",
			success: function(result){
				 if(result.error){
				 alert(result.msg);
				
				 }else{
					if(result.msg!=''){
						alert(result.msg);
					}
					 window.location.replace( baseurl+'/groups/'+galexy+'/'+planet+'/media');
				 }
			                     
			} 
	});
});

$(document).on("click","#cancel_edit",function(){ 
 
$('#album_edit_view').hide('slow');

});
$(document).on("click","#cancel_more",function(){ 
 
$('#album-add').hide('slow');
$("#album")[0].reset();

});
 
$('#album_edit').click(function(){
 
$("#album_edit_view").animate({  height:'toggle' });
$("#add_albums_container").hide();
});
 

 
$("#add_more_video").on('click',function(){

 $("#video_urlbox_more").css("display","block");

});
$(document).on("click",".user-action",function(event){ 
 
 var element = $(this).attr('id');
 var id = element.replace('toggle_section_','');
 $('#toggle_actions_'+id).toggle('slow');
});
$(document).on("click",".album_pic_del",function(event){ 
	event.preventDefault();
	var url = $(this).attr("href"); 
	var element = $(this).attr('id');
	var data_id = element.replace('album_pic_del_','');
 
	 $.ajax({
		 type: "GET",
		 url: url,
		 dataType:"json",
		 success:function(result){ 
			if(result.error){
				 alert(result.msg);
				
				 }else{
					if(result.msg!=''){
						alert(result.msg);
					}
					$("#list-albums_"+data_id).remove();
				 }
			},
		 error:function(){}
	 });	
});
$(document).on("click",".album_cvr",function(event){ 
	 event.preventDefault();
	 var url = $(this).attr("href"); 
	 $.ajax({
		 type: "GET",
		 url:url,
		 dataType:"json",
		 success:function(result){
				if(result.error){
				 alert(result.msg);
				
				 }else{
					if(result.msg!=''){
						alert(result.msg);
					}
					 
				 }
			        
		 },
		 error:function(){}
	 }); 
});



//==================================video start=====================
 
$(document).on("click","#add_planet_video",function(){
 $("#video_urlbox").css("display","block");

});

var counter = 2;
 $(document).on("click","#addButton",function(){
   
	if(counter>5){
            alert("Only 5 at a time");
            return false;
	}   

	var newTextBoxDiv = $(document.createElement('div')).attr("id", 'TextBoxDiv'+counter);
 
	newTextBoxDiv.html('<div class="album-field"><label><span>URL #'+ counter + ' : </span></label>' +'<input type="text" name="planet_video[]" class="video_textbox" id="textbox' + counter + '" value="" ></div>');
 
	newTextBoxDiv.appendTo("#TextBoxesGroup");
 
 
	counter++;
     });
 
     $("#removeButton").click(function () {
	if(counter==1){
          alert("No more textbox to remove");
          return false;
       }   
 
	counter--;
 
        $("#TextBoxDiv" + counter).remove();
 
     });
 
     $("#getButtonValue").click(function () {
 
	var msg = '';
	for(i=1; i<counter; i++){
   	  msg += "\n Textbox #" + i + " : " + $('#textbox' + i).val();
	}
    	  alert(msg);
     });

//=================================end video =========================
var obj = $("#dragandrophandler");
obj.on('dragenter', function (e)
{
    e.stopPropagation();
    e.preventDefault();
    $(this).css('border', '2px solid #0B85A1');
});
obj.on('dragover', function (e)
{
     e.stopPropagation();
     e.preventDefault();
});
obj.on('drop', function (e)
{ 
     $(this).css('border', '2px dotted #0B85A1');
     e.preventDefault();
     var files = e.originalEvent.dataTransfer.files;
 
     //We need to send dropped files to Server 
     handleFileUpload(files,obj);
});
$(document).on('dragenter', function (e)
{
    e.stopPropagation();
    e.preventDefault();
});
$(document).on('dragover', function (e)
{
  e.stopPropagation();
  e.preventDefault();
  obj.css('border', '2px dotted #0B85A1');
});
$(document).on('drop', function (e)
{
    e.stopPropagation();
    e.preventDefault();
});
 //=======================google location===================
var input = document.getElementById('album_location');
    var options = {
        componentRestrictions: {country: 'ae'}
    };
    var autocomplete = new google.maps.places.Autocomplete(input, options);
	

	
	
	$(document).on("click","#album_save",function(event){
		event.preventDefault(); 
		var album_title = $("#album_title").val();
		if(album_title==''){	
			$("#album-error").html("Album title is required");
			return false;
		}else{
			$("#album-error").html("");			 
		}
		var fd = new FormData();
		fd.append('album_title', album_title);
		var album_location = $("#album_location").val();
		fd.append('album_location', album_location);
	    var k=0;		
		for(var j = 0; j < uploaded_files.length; j++){	
			for (var i = 0; i < uploaded_files[j].length; i++)
				{  
					 
					fd.append(k, uploaded_files[j][i]);k++;
				}
		}
		var videos = new Array();
		 $('.video_textbox').each(function() {
		   videos.push($(this).val());
		 });
		 fd.append('videos',videos);
		$("#album_butn_contr").html('<img src="'+baseurl+'/public/images/ajax_loader.gif" />');
		var url = baseurl+'/album/'+galexy+'/'+planet+'/ajaxAddAlbum';	 
		$.ajax({
		type: "POST",
		url:url,
		data: fd,
		dataType:"json",
		processData: false, 
		contentType: false,	
		success:function(result) {
			 if(result.error){
			 alert(result.msg);
			 $("#album_butn_contr").html('<input type="submit" value="Go" class="blue-butn" id="album_save" name="submit">');
			 }else{
				if(result.msg!=''){
				alert(result.msg);
				}
				 window.location.reload();
			 }
		}}); 
	});
		$(document).on("click","#submit_more",function(event){ 
		event.preventDefault(); 		 
		var fd = new FormData();
		var k=0;		
		for(var j = 0; j < uploaded_files.length; j++){	
			for (var i = 0; i < uploaded_files[j].length; i++)
				{  
					 
					fd.append(k, uploaded_files[j][i]);k++;
				}
		}
		
		var videos = new Array();
		 $('.video_textbox').each(function() {
		   videos.push($(this).val());
		 });
		 fd.append('videos',videos);
		 $("#album_more_btn_cntr").html('<img src="'+baseurl+'/public/images/ajax_loader.gif" />');
		var url = $("#frmalbum_add_more").attr("action");	 
		$.ajax({
		type: "POST",
		url:url,
		data: fd,
		dataType:"json",
		processData: false, 
		contentType: false,	
		success:function(result) {
			 if(result.error){
			 alert(result.msg);
			 $("#album_more_btn_cntr").html('<input type="button" class="blue-butn" value="Submit" id="submit_more" />');
			 }else{
				if(result.msg!=''){
				alert(result.msg);
				
				}
				window.location.reload();
			 }
		}}); 
	});
	
});
function sendFileToServer(formData,status)
{
	var planet_id = $("#album_group_id").val();
    var uploadURL = baseurl+"/album/upload/"+39; //Upload URL 
    var extraData ={}; //Extra Data. 
    var jqXHR=$.ajax({
            xhr: function() {
            var xhrobj = $.ajaxSettings.xhr();
            if (xhrobj.upload) {
                    xhrobj.upload.addEventListener('progress', function(event) {
                        var percent = 0;
                        var position = event.loaded || event.position;
                        var total = event.total;
                        if (event.lengthComputable) {
                            percent = Math.ceil(position / total * 100);
                        }
                        //Set progress
                        status.setProgress(percent);
                    }, false);
                }
            return xhrobj;
        },
    url: uploadURL,
    type: "POST",
    contentType:false,
    processData: false,
        cache: false,
        data: formData,
        success: function(data){
		//alert(data); return false;
            status.setProgress(100);
				 if(data != "error"){
				 $('form#album').append('<input type="hidden" name="planet_image[]" id="planet_image" value="'+data+'" />');
							$("#status1").append("<span  style='color:green;'><b>Uploaded File : "+data+"</b></span><br>"); 
				}else{
							$("#status1").append("<span  style='color:red;'><b>Error: Not Image</b></span><br>"); 
				}			
        }
    });
 
    status.setAbort(jqXHR);
}
 
var rowCount=0;
function createStatusbar(obj)
{
     rowCount++;
     var row="odd";
     if(rowCount %2 ==0) row ="even";
     this.statusbar = $("<div class='statusbar"+rowCount+" "+row+"'></div>");
     this.filename = $("<div class='filename'></div>").appendTo(this.statusbar);
     this.size = $("<div class='filesize'></div>").appendTo(this.statusbar);
     this.progressBar = $("<div class='progressBar'><div></div></div>").appendTo(this.statusbar);
     this.abort = $("<div class='abort'>Abort</div>").appendTo(this.statusbar);
	 this.image = $("<div class='image'></div>").appendTo(this.statusbar);
     obj.after(this.statusbar);
 
    this.setFileNameSize = function(name,size)
    {
        var sizeStr="";
        var sizeKB = size/1024;
        if(parseInt(sizeKB) > 1024)
        {
            var sizeMB = sizeKB/1024;
            sizeStr = sizeMB.toFixed(2)+" MB";
        }
        else
        {
            sizeStr = sizeKB.toFixed(2)+" KB";
        }
 
        this.filename.html(name);
        this.size.html(sizeStr);
    }
    this.setProgress = function(progress){      
        var progressBarWidth =progress*this.progressBar.width()/ 100; 
        this.progressBar.find('div').animate({ width: progressBarWidth }, 10).html(progress + "% ");
        if(parseInt(progress) >= 100)
        {
            this.abort.hide();
        }
    }
    this.setAbort = function(jqxhr)
    {
        var sb = this.statusbar;
        this.abort.click(function()
        {
            jqxhr.abort();
            sb.hide();
        });
    }	
	this.setFile  = function(content){
		this.image.append(content)
	}
}
 
function handleFileUpload(files,obj)
{
	
	uploaded_files[file_count]  = files;file_count++;
   for (var i = 0; i < files.length; i++)
   {   
      ///  var fd = new FormData();
		var content = '';
       // fd.append('file', files[i]);
		 
        var status = new createStatusbar(obj); //Using this we can set progress.
        status.setFileNameSize(files[i].name,files[i].size);
		 status.setProgress(100);
		
		var reader = new FileReader();
		  reader.onload = function(e) {  
			 content = '<div id="image'+i+'"><img src="'+ e.target.result+'" width="150px" /></div>';
 
		status.setFile(content);
		  }
		 reader.readAsDataURL(files[i]);
		
        //sendFileToServer(fd,status);
 
   }
}

   