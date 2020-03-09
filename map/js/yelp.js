//NAME: Sandeep Satone
//UTA ID: 1001556868

//Map object
var map;

//Keep marker info so as to set it to null on map later
var markers = [];

//Default lat/lng for the map
var coords = {
	lat: 32.75,
	lng: -97.13
};

//initialize page with default map with no markers
function initialize() {
	map = new google.maps.Map(document.getElementById('map'), {
		zoom: 16,
		center: coords
	});
	hideLoading();
}

// Shows markers and numeric title
function showMarkers(locationCoords, Title) {
	var bounds = new google.maps.LatLngBounds();
	if (locationCoords) {
		var marker = new google.maps.Marker({
			position: locationCoords,
			map: map,
			label: Title
			//			animation: google.maps.Animation.BOUNCE // Not required
		});

		// ZOOM THE RESTAURANT LOCATION on clicking the marker 
		google.maps.event.addListener(marker, 'click', function () {
			var pos = map.getZoom();
			map.setZoom(16);
			map.setCenter(marker.getPosition());
			// ZOOM BACK TO ORIGINAL SIZE AFTER 5 SECONDS
			window.setTimeout(function () {
				map.setZoom(pos);
			}, 5000);
		});
		markers.push(marker);
	}
	for (var i = 0; i < markers.length; i++) {
		bounds.extend(markers[i].getPosition());
	}
	map.fitBounds(bounds);
	map.setZoom(map.getZoom());
}

// Removes all markers from map
function clearMarkers(map) {
	for (var i = 0; i < markers.length; i++) {
		markers[i].setMap(map);
	}
}

//Request to the YELP Fusion API using php as proxy
function sendRequest() {
	// Show the loader ICON when the AJAX request is taking time to load data
	showLoading();
	//gets the lat / lng of the maps center. 
	//when we move map to new location we get new location and new values for lat/ lng to pass to the Yelp API.
	//	latitude=32.75&longitude=-97.13 are for Arlington which professor has asked us to keep
	var center = map.getCenter();
	var northeast = map.getBounds().getNorthEast();
	var radius = Math.round(google.maps.geometry.spherical.computeDistanceBetween(center, northeast));
	var xhr = new XMLHttpRequest();
	//get Search Parameter and encodeURI encode the query as per the web URL i.e space replaced by %20 etc. 
	var query = encodeURI(document.getElementById("search").value);
	xhr.open("GET", "php/proxy.php?term=" + query + "&latitude=" + center.lat() + "&longitude=" + center.lng() + "&radius=" + radius + "&limit=10");
	xhr.setRequestHeader("Accept", "application/json");
	xhr.onreadystatechange = function () {
		if (this.readyState == 4) {
			hideLoading();
			var jsonResponse = JSON.parse(this.responseText);
			console.log(jsonResponse);
			// This creates the elements for displaying the results on the page. 
			createTitles(jsonResponse, radius);
		}
	};
	xhr.send(null);

}

function createTitles(jsonResponse, radius) {
	//clearMarkers from Map for every new search
	clearMarkers(null);
	//append everything inside an Ordered List
	var ol = document.getElementById("orderedList");
	//Empty the list if no search done or initially
	ol.innerHTML = "";
	//	if (jsonResponse.businesses && radius <= 12539) {
	if (jsonResponse.businesses && jsonResponse.businesses.length != 0) {
		for (var i = 0; i < jsonResponse.businesses.length; i++) {
			//		console.log(i);
			var li = document.createElement('li');
			var Title = document.createElement("p");
			var YelpPageURL = document.createElement("a");
			var image = document.createElement("img");
			var rating = document.createElement("p");
			var ratingImg = document.createElement("img");

			//Rating image not asked by professor but it looked easy to implement so implemented 
			//it improved look and feel for the application
			//All images are taken from the YELP display Requirement page
			//https://www.yelp.com/developers/display_requirements
			ratingImg.setAttribute("src", "images/" + jsonResponse.businesses[i].rating + ".png");

			Title.innerHTML = (i + 1) + " ) Title : " + jsonResponse.businesses[i].name;
			YelpPageURL.setAttribute("href", jsonResponse.businesses[i].url);
			YelpPageURL.setAttribute("target", "_blank");
			YelpPageURL.appendChild(Title);
			image.setAttribute("src", jsonResponse.businesses[i].image_url);
			rating.innerHTML = "Rating : " + jsonResponse.businesses[i].rating + "<br>";
			li.appendChild(YelpPageURL);
			li.appendChild(image);
			li.appendChild(rating);
			rating.appendChild(ratingImg);
			ol.appendChild(li);

			//We pass marker paramter here in the loop 
			//Everything individual marker is created and added to the map
			//Later we fitBounds according to the last marker and set zoom level accordingly 
			showMarkers({
				lat: jsonResponse.businesses[i].coordinates.latitude,
				lng: jsonResponse.businesses[i].coordinates.longitude
			}, i + 1 + "");
		}
	}
	//	else if (radius >= 12539) {
	//		ol.innerHTML = "<h1>Radius is too large </h1>" + radius + "<h1>Zoom map using plus button and search again</h1>";
	//	}
	else {
		ol.innerHTML = "<h1>NO RESTAURANT FOUND</h1>";
	}
	if (radius >= 12539) {
		ol.innerHTML = "<h1>Error Code : </h1><br>" + jsonResponse.error.code + "<h1> Description : </h1><br>" + jsonResponse.error.description + " <br> <h2>Solution :</h2> Radius of search area too large Click zoom and search again";
	}

}


function showLoading() {
	document.getElementById("loadingImage").style.display = 'block';
}

function hideLoading() {
	document.getElementById("loadingImage").style.display = 'none';
}
