   <!-- Include Mapbox Geocoding SDK -->

   <link href='https://api.mapbox.com/mapbox-gl-js/v2.9.1/mapbox-gl.css' rel='stylesheet' />
    <link rel="stylesheet" href="https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-geocoder/v4.7.0/mapbox-gl-geocoder.css" type="text/css" />
    <script src='https://api.mapbox.com/mapbox-gl-js/v2.9.1/mapbox-gl.js'></script>
    <script src="https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-geocoder/v4.7.0/mapbox-gl-geocoder.min.js"></script>
   <script>
       // Replace 'YOUR_MAPBOX_ACCESS_TOKEN' with your actual Mapbox access token
       mapboxgl.accessToken = '<?php echo MAPBOX_ACCESS_TOKEN; ?>';

       // Initialize the Mapbox map
       var map = new mapboxgl.Map({
           container: 'map',
           style: 'mapbox://styles/mapbox/streets-v11', // You can use your desired map style
           center: [0, 0], // Initial center coordinates
           zoom: 1, // Initial zoom level
       });

       // Initialize the Mapbox Geocoder control
       var geocoder = new MapboxGeocoder({
           accessToken: mapboxgl.accessToken,
           mapboxgl: mapboxgl,
       });

       // Add the geocoder control to the form
       document.getElementById('location-form').appendChild(geocoder.onAdd(map));
       document.getElementsByClassName('mapboxgl-ctrl-geocoder--input')[0].setAttribute("placeholder", "Search location");
       // Listen for the result event
       geocoder.on('result', function(e) {
           var coordinates = e.result.geometry.coordinates;
           var locationName = e.result.text;
           var place_name = e.result.place_name;
           document.getElementById('set-location').value = place_name;
           document.getElementById('lat_save').value = coordinates[1];
           document.getElementById('lon_save').value = coordinates[0];
           // console.log(e);
           // Display the selected location's coordinates
           // var coordinatesDiv = document.getElementById('coordinates');
           // coordinatesDiv.innerHTML = '<p>Location: ' + locationName + '</p>' +
           //     '<p>Latitude: ' + coordinates[1] + '</p>' +
           //     '<p>Longitude: ' + coordinates[0] + '</p>';
       });

       // Handle form submission with AJAX when the button is clicked
       document.getElementById('search-button').addEventListener('click', function() {
           var locationInput = document.getElementById('location-input').value;


           // Use the geocoder to search for the location
           geocoder.query(locationInput);
       });
   </script>