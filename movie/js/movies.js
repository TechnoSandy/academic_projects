var resultObject;
var movieID = new Array();
var movieTitle = new Array();
var movieReleaseDate = new Array();
var movieDescription = new Array();
var movieImageURL = new Array();

var title;



function initialize() {

}

function sendRequest() {
	var xhr = new XMLHttpRequest();
	var query = encodeURI(document.getElementById("form-input").value);
	xhr.open("GET", "php/proxy.php?method=/3/search/movie&query=" + query); // Gets the movies having keyword in the query
	//	xhr.open("GET", "php/proxy.php?method=/3/movie/" + {MovieID}); // Gets the Movie Info
	//	xhr.open("GET", "php/proxy.php?method=/3/movie/" + {MovieID} + "/credits"); // Gets the Movie Credits
	xhr.setRequestHeader("Accept", "application/json");
	xhr.onreadystatechange = function () {
		if (this.readyState == 4) {
			var json = JSON.parse(this.responseText);
			var str = JSON.stringify(json, undefined, 2);
			resultObject = json.results.length;
			createList(str, json);
		}
	};
	xhr.send(null);

}

function deleteList() {
	var completeList = document.getElementById("orderedList");
	while (completeList.firstChild) {
		completeList.removeChild(completeList.firstChild);
	}
	while (movieID.length > 0) {
		movieID.pop();
		movieTitle.pop();
		movieReleaseDate.pop();
		movieDescription.pop();
		movieImageURL.pop();
	}

}

function createList(str, json) {
	deleteList();

	var ol = document.getElementById("orderedList");
	for (var i = 0; i < resultObject; i++) {
		var li = document.createElement('li');
		title = document.createElement('p');
		movieID.push(json.results[i].id);
		movieTitle.push(json.results[i].title);
		movieReleaseDate.push(json.results[i].release_date);
		title.innerHTML = " Movie Title: &nbsp; " + movieTitle[i] + " &nbsp; Release: " + movieReleaseDate[i];
		title.setAttribute("onclick", "titleClicked(" + json.results[i].id + ");");
		li.appendChild(title);
		ol.appendChild(li);
	}
}


function titleClicked(titleElement) {
	var outputMSG = document.getElementById("outputMSG");
	outputMSG.innerHTML = '';
	getMovieGenre(titleElement);
	getMovieCast(titleElement);

}

function getMovieGenre(titleElement) {
	var genere = document.createElement('p');
	var xhr = new XMLHttpRequest();
	xhr.open("GET", "php/proxy.php?method=/3/movie/" + titleElement); // Gets the Movie Info
	xhr.setRequestHeader("Accept", "application/json");
	xhr.onreadystatechange = function () {
		if (this.readyState == 4) {
			var json = JSON.parse(this.responseText);
			for (var i = 0; i < json.genres.length; i++) {
				genere.innerHTML = "<b>Movie Genere : </b>" + " " + json.genres[0].name;
			}
			var outputMSG = document.getElementById("outputMSG");
			var title = document.createElement('p');
			title.innerHTML = "<b>Title :</b>" + " " + json.title;
			var releaseDate = document.createElement('p');
			releaseDate.innerHTML = "<b>Release :</b>" + json.release_date;
			var description = document.createElement('p');
			description.innerHTML = "<b>Description :</b>" + "  " + json.overview;
			var img = document.createElement('img');
			if (json.poster_path) {

				img.setAttribute('src', " http://image.tmdb.org/t/p/w185" + json.poster_path);
			} else if (json.backdrop_path) {

				img.setAttribute('src', " http://image.tmdb.org/t/p/w185" + json.backdrop_path);
			} else {

				img.setAttribute('src', "https://via.placeholder.com/350x150");
			}
			outputMSG.appendChild(title);
			outputMSG.appendChild(releaseDate);
			outputMSG.appendChild(img);
			outputMSG.appendChild(description);
			outputMSG.appendChild(genere);

		}
	};
	xhr.send(null);
}

function getMovieCast(titleElement) {
	var cast = document.createElement('p');
	var castLabel = document.createElement('p');
	castLabel.innerHTML = " <b>Cast :</b>";
	var numberOfCastToPrint;
	var xhr = new XMLHttpRequest();
	xhr.open("GET", "php/proxy.php?method=/3/movie/" + titleElement + "/credits"); // Gets the Movie Credits
	xhr.setRequestHeader("Accept", "application/json");
	xhr.onreadystatechange = function () {
		if (this.readyState == 4) {
			var json = JSON.parse(this.responseText);
			//			console.log(json);
			if (json.cast.length > 5) {
				numberOfCastToPrint = 5;
			} else {
				numberOfCastToPrint = json.cast.length;
			}
			for (var i = 0; i < numberOfCastToPrint; i++) {
				cast.innerHTML += json.cast[i].name + ",";
			}
		}
	};
	xhr.send(null);
	var outputMSG = document.getElementById("outputMSG");
	outputMSG.appendChild(castLabel);
	outputMSG.appendChild(cast);

}
