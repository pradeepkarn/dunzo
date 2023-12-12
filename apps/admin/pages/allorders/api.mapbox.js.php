  
  <?php 
  $cust_addrs = $context['cust_address'];
  $pickup_addrs = $context['pickup_address'];
  ?>
  <!-- Include Mapbox Geocoding SDK -->

   
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
           document.getElementById('custLat').value = coordinates[1];
           document.getElementById('custLon').value = coordinates[0];
       });
   </script>