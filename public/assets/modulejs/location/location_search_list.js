let autocomplete;
let latitudeData;
let longitudeData;

// function initAutocomplete() {
//     // const loader = document.getElementById("loaderDashboardGraph");
//         var input = document.getElementById('ship-address');
// 		var autocomplete = new google.maps.places.Autocomplete(input);
//         console.log(autocomplete);
//         console.log('Hello')
//         latitudeData ="";
//         longitudeData ="";
// 		google.maps.event.addListener(autocomplete, 'place_changed', function() {
            
//             // loader.style.display = "block";
// 			var place = autocomplete.getPlace();
//             console.log(place);
//             // if (place.length === 0) {
//             //     loader.style.display = "none"; // Hide loader if no places found
//             //     return;
//             //   }
//             var latitude = place.geometry.location.lat();
//             var longitude = place.geometry.location.lng();
//             latitudeData = latitude
//             longitudeData = longitude
//             // setTimeout(() => {
//             //     loader.style.display = "none"; // Hide loader after processing
//             //   }, 1000);
//                 console.log(latitudeData);
//                 console.log(longitudeData);
//                 searchData();
//               if(latitudeData != '' &&  longitudeData != ''){
//                   searchData();
//                   console.log('Hello');
//               }
// 		})

       
// }

function initAutocomplete() {
    // Ensure the input element exists before proceeding
    var input = document.getElementById('ship-address');
    if (!input) {
        console.error('Input element #ship-address not found!');
        return;
    }

    // Initialize the autocomplete object
    var autocomplete = new google.maps.places.Autocomplete(input);

    // Bind the place_changed event listener
    google.maps.event.addListener(autocomplete, 'place_changed', function() {
        var place = autocomplete.getPlace();
        console.log(place);
        if (!place.geometry) {
            console.log('No details available for input: ' + place.name);
            return;
        }

        var latitude = place.geometry.location.lat();
        var longitude = place.geometry.location.lng();
        var latitudeData = latitude;
        var longitudeData = longitude;

        console.log('Latitude: ' + latitudeData);
        console.log('Longitude: ' + longitudeData);

        // Call your searchData function if lat/lng are valid
        if (latitudeData && longitudeData) {
            searchData(latitudeData, longitudeData);
        }
    });
}

// Make sure the Google Maps script loads the callback correctly


var response = [];
function searchData(){    
    $('.list-wrapper').html("");
    $('.order-listing-loader1new').atrr('style','')
   $.ajax({
        async:false,
        global:false,
        type:"GET",
        url:_SEARCH_LOCATION_AJAX,
        data:{
            'latitude':latitudeData,
            'longitude':longitudeData,
            'appointment_type':$('#appointment_type').val()
        },
        success:function(res){
            setTimeout(function(e){
                $('.order-listing-loader1new').atrr('style','display:none')
            },500);
            
            $('.list-wrapper').html("")
            $('.list-wrapper').html(res)
        }
   })

  
}