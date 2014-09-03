//Useful links:
// http://code.google.com/apis/maps/documentation/javascript/reference.html#Marker
// http://code.google.com/apis/maps/documentation/javascript/services.html#Geocoding
// http://jqueryui.com/demos/autocomplete/#remote-with-cache

var geocoder;
var map;
var marker;

function initialize(){
//MAP
  var latlng = new google.maps.LatLng(25.2644444, 55.31166669999993);
  var options = {
    zoom: 16,
    center: latlng,
    mapTypeId: google.maps.MapTypeId.ROADMAP
  };

  map = new google.maps.Map(document.getElementById("activity_map"), options);

  //GEOCODER
  geocoder = new google.maps.Geocoder();

  marker = new google.maps.Marker({
    map: map,
    draggable: true
  });
marker.setPosition(latlng);
}

$(document).ready(function() {

  initialize();

  $(function() {
    $("#address").autocomplete({
      //This bit uses the geocoder to fetch address values
      source: function(request, response) {
        geocoder.geocode( {'address': request.term }, function(results, status) {
          response($.map(results, function(item) {
		 // var location = new google.maps.LatLng( item.geometry.location.lat(), item.geometry.location.lng()); 
		//  $("#geo_position").val(location);
            return {
              label:  item.formatted_address,
              value: item.formatted_address,
              latitude: item.geometry.location.lat(),
              longitude: item.geometry.location.lng()
            }
			 
          }));
        })
      },
      //This bit is executed upon selection of an address
      select: function(event, ui) { 
        var location = new google.maps.LatLng(ui.item.latitude, ui.item.longitude);
        
        $("#geo_position").val(location);
       
        marker.setPosition(location);
        map.setCenter(location);
      }
    });
  });

  //Add listener to marker for reverse geocoding
  google.maps.event.addListener(marker, 'drag', function() { 
    geocoder.geocode({'latLng': marker.getPosition()}, function(results, status) {
      if (status == google.maps.GeocoderStatus.OK) {
        if (results[0]) {
          $('#address').val(results[0].formatted_address);
         // $('#latitude').val(marker.getPosition().lat());
        //  $('#longitude').val(marker.getPosition().lng());
		  var posit  = "("+marker.getPosition().lat()+","+marker.getPosition().lng()+")"
		//  $("#geo_position").val(posit);
        }
      }
    });
  });

});
