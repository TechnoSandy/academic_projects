//NAME: SANDEEP SATONE
//UTA ID:1001556868


//Global Score Variables
var numberOfHitsOnpaddle = 0;
var strikes = 0;
var maxscore = 0;


//randomly choosen left and top values of the ball
var ballRandomTopValue;
var ballRandomLeftValue;

//Game loop
var gameLoop;
var gameSpeed = 10;

//COURT Co-ordinates
var courtRtEnd;
var courtTopEnd;
var courtLeftEnd;
var courtBottomEnd;

//objects
var ball;
var paddle;


//Initialize variables and display start message
function initialize() {
	displayMsg("Click Start Button to begin Game");
	ball = document.getElementById("ball");
	paddle = document.getElementById("paddle");
	//	console.log(paddle.getBoundingClientRect());
	courtRtEnd = 752;
	courtTopEnd = -80;
	courtLeftEnd = 0;
	courtBottomEnd = 400;
	ballRandomTopValue = getRandomInt();
	ballRandomLeftValue = 0;
	ball.style.top = ballRandomTopValue + 'px';
	ball.style.left = ballRandomLeftValue + 'px';
}


function startGame() {
	//Disable the start button to avoid multiple instances of the ball
	document.getElementById("Button").disabled = true;
	var top = ballRandomTopValue;
	var left = ballRandomLeftValue;
	var bounceAngle = generateRandomBounceAngle(-45, 45)
	var topangle = Math.sin(bounceAngle / 180 * Math.PI); //Upper angle
	var leftangle = Math.cos(bounceAngle / 180 * Math.PI); //Lower angle 

	document.getElementById("messages").innerHTML = "Bounce Angle: " + bounceAngle + " Speed = " + gameSpeed + " i.e Frames/sec = " + 1000 / gameSpeed + " ";

	gameLoop = setInterval(temp, gameSpeed);

	function temp() {
		//Ball is below the bottom of court
		if (top >= courtBottomEnd) {
			clearInterval(gameLoop);
			moveUp();
			//Ball is above the top of court
		} else if (top <= courtTopEnd) {
			clearInterval(gameLoop);
			moveDown();
			//Ball is to the right of court
		} else if (left >= courtRtEnd) {
			clearInterval(gameLoop);
			detectCollision();
		} else {
			//Start the game with random angle between -45 to 45 degree (-pi/4 to pi/4)
			top = top + topangle;
			left = left + leftangle;
			ball.style.top = top + 'px';
			ball.style.left = left + 'px';
		}
	}

	function moveUp() {
		//Kill the old gameLoop and start new loop 
		gameLoop = setInterval(temp, gameSpeed);

		function temp() {
			if (left >= courtRtEnd) {
				clearInterval();
				detectCollision();

			} else if (top <= courtTopEnd) {
				clearInterval(gameLoop);
				moveDown();
			} else {
				left++;
				top--;
				ball.style.top = top + 'px';
				ball.style.left = left + 'px';
			}
		}
	}

	function moveLeftUpwards() {
		gameLoop = setInterval(temp, gameSpeed);

		function temp() {
			if (top <= courtTopEnd) {
				clearInterval(gameLoop);
				moveLeftDownwards();

			} else if (left <= courtLeftEnd) {
				clearInterval(gameLoop);
				moveUp();

			} else {
				left--;
				top--;
				ball.style.top = top + 'px';
				ball.style.left = left + 'px';
			}

		}

	}


	function moveLeftDownwards() {
		gameLoop = setInterval(temp, gameSpeed);

		function temp() {
			if (top >= courtBottomEnd) {
				clearInterval(gameLoop);
				moveLeftUpwards();

			} else if (left <= courtLeftEnd) {
				clearInterval(gameLoop);
				moveDown();
			} else {
				left--;
				top++;
				ball.style.top = top + 'px';
				ball.style.left = left + 'px';
			}

		}
	}

	function moveDown() {
		gameLoop = setInterval(temp, gameSpeed);

		function temp() {
			if (left >= courtRtEnd) {
				clearInterval(gameLoop);
				detectCollision();
			} else if (top >= courtBottomEnd) {
				clearInterval(gameLoop);
				moveUp();

			} else {
				left++;
				top++;
				ball.style.top = top + 'px';
				ball.style.left = left + 'px';
			}
		}
	}


	function detectCollision() {

		var paddleTop = parseInt(paddle.style.top, 10);
		//Every time we move the paddle its Height changes
		// we Make sure that the height for the paddle remain same while we scroll  that is 102px
		var paddleBottom = paddleTop - paddle.getBoundingClientRect().height;
		if ((top <= paddleTop) && (top >= paddleBottom)) {
			clearInterval(gameLoop);
			numberOfHitsOnpaddle++;
			strikes = document.getElementById("strikes").innerHTML = numberOfHitsOnpaddle;
			moveLeftDownwards();

		} else {
			clearInterval(gameLoop);
			ballRandomTopValue = getRandomInt();
			ballRandomLeftValue = 0;
			ball.style.top = ballRandomTopValue + 'px';
			ball.style.left = ballRandomLeftValue + 'px';
			displayMsg("GAME OVER");
			updateScore(strikes, maxscore);

		}
	}
}

//Paddle will Move when onmousemove event is triggered in Page
function movePaddle(onmousemove) {
	var x = onmousemove.clientX;
	var y = onmousemove.clientY;
	if (y > courtBottomEnd) {
		y = courtBottomEnd;
		paddle.style.top = y + 'px';
	} else {
		paddle.style.top = y + 'px';
	}
}

function resetGame() {
	document.getElementById("Button").disabled = false;
	displayMsg("Click Start Button to begin Game");
	ballRandomTopValue = getRandomInt();
	ballRandomLeftValue = 0;
	maxscore = 0;
	strikes = 0;
	document.getElementById("score").innerHTML = maxscore;
	document.getElementById("strikes").innerHTML = strikes;
	clearInterval(gameLoop);
	ball.style.top = ballRandomTopValue + 'px';
	ball.style.left = ballRandomLeftValue + 'px';
}

function setSpeed(speed) {
	var speed = document.getElementsByName('speed');
	var speedValue = 0;
	for (var i = 0, length = speed.length; i < length; i++) {
		if (speed[i].checked) {
			speedValue = speed[i].value;
			break;
		}
	}
	if (speedValue == 0) {
		gameSpeed = 100; //Frames/sec = 100
	}
	if (speedValue == 1) {
		gameSpeed = 20; //Frames/sec = 50
	}
	if (speedValue == 2) {
		gameSpeed = 0.01; //Frames/sec = 100000
	}
}

function scoreCounter() {
	document.getElementById("score").innerHTML = maxscore;
	document.getElementById("strikes").innerHTML = strikes;
}

function getRandomInt() {
	return Math.floor((Math.random() * courtBottomEnd))
}

function displayMsg(msg) {
	document.getElementById("messages").innerHTML = msg;
}

function updateScore(strikes, maxscore) {
	if (strikes > maxscore) {
		maxscore = strikes;
		document.getElementById("score").innerHTML = maxscore;
	}
}

function generateRandomBounceAngle(min, max) {
	//https://stackoverflow.com/questions/4959975/generate-random-number-between-two-numbers-in-javascript?rq=1
	//pi/4 radian is same as 45 degree
	return Math.floor(Math.floor((Math.random()) * (max - (min))) + (min));
}
