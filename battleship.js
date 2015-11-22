"use strict";

//Globals
var yourTurn;
var boardLength;
var yourBoard = [[]];
var oppBoard = [[]];
var yourTiles = [];
var oppTiles = [];
var ships;
var shipButtons;
var shipClicked;
var firstClick;
var boardLength;
var shipSet = 0;
var numShips = 0;
var WEB_APP = "http://wwwuser.csse.rose-hulman.edu/~brownje/webProgramming/GroupProject/getData.php";
var boardStringArray = [];
var player;
var gameID;
var timer;
var yourHitCount = 0;
var oppHitCount = 0;

window.onload = function() {

	//Get list of spans
	var yourSpans = document.getElementsByClassName("yourTiles");
	var oppSpans = document.getElementsByClassName("oppTiles");
	var user = document.getElementById("user").innerText;
	var yourNameDiv = document.getElementById("yourName");
	//Set your name
	yourNameDiv.innerText = user + "'s Board";
	//Get length
	boardLength = Math.sqrt(yourSpans.length);
	//Start game button
	var ready = document.getElementById("ready");
	if (ready != null)
		ready.onclick = startGame;
	//Add tiles to our arrays
	for (var i = 0; i < yourSpans.length; i++) {
		yourTiles.push(yourSpans[i]);
		oppTiles.push(oppSpans[i]);
	}

	//Put tiles in 2D array and charArray
	for (var i = 0; i < boardLength; i++) {
		yourBoard.push([]);
		boardStringArray.push([]);
		for (var j = 0; j < boardLength; j++) {
			boardStringArray[i] += "W";
			yourBoard[i].push(yourTiles[i * boardLength + j]);
		}
	}

	//Put tiles in 2D array
	for (var i = 0; i < boardLength; i++) {
		oppBoard.push([]);
		for (var j = 0; j < boardLength; j++) {

			oppBoard[i].push(oppTiles[i * boardLength + j]);
		}
	}

	shipButtons = document.getElementsByClassName("S");
	numShips = shipButtons.length;
	var button;
	for (var i = 0; i < shipButtons.length; i++) {
		button = shipButtons[i];
		button.onclick = (function(button) {
			return function() {
				clickShip(button);
			};
		})(button);
	}
	var tile;
	var oppTile;
	for ( i = 0; i < yourTiles.length; i++) {
		tile = yourTiles[i];
		oppTile = oppTiles[i];
		tile.onclick = (function(tile) {
			return function() {
				setShip(tile);
			};
		})(tile);
		oppTile.onclick = (function(oppTile) {
			return function() {
				attack(oppTile);
			};
		})(oppTile);

	}
};

function startGame() {
	//Remove shipBoard
	var shipBoard = document.getElementById("shipBoard");
	shipBoard.classList.add("hide");

	//Send info of board
	var info = document.getElementById("player").innerHTML;
	info = info.split(" ");
	player = info[0];
	gameID = info[1];
	//Updates the database with your board and returns turn, other player, and their board
	$.post(WEB_APP, {
		"board" : boardStringArray,
		"player" : player,
		"oppGameID" : gameID
	}).done(callStartTurn).fail(ajaxFail);
	var button = document.getElementById("ready");
	button.parentNode.removeChild(button);
}

function callStartTurn(data) {
	//Turn, oppName, oppBoard
	var info = data.split(" ");
	console.log("data " + info);
	var turn = info[0];
	var oppPlayer = info[1];
	var oppBoard = info[2];
	console.log("opp player is " + oppPlayer + " and opp board is " + oppBoard);
	//If the other player is ready
	if (oppBoard != "") {
		console.log("other player is ready");
		var oppNameDiv = document.getElementById("oppName");
		oppNameDiv.innerText = oppPlayer + "'s Board";
		callTurn(turn);
	}
	//If the other player isn't ready
	else {
		setTimeout(updateFetch, 300);
	}
}

function updateFetch() {
	var turnDiv = document.getElementById("turn");
	turnDiv.innerText = "Waiting on opponent";

	$.post(WEB_APP, {
		"player" : player,
		"oppGameID" : gameID
	}).done(callStartTurn).fail(ajaxFail);
}

function callTurn(data) {
	var turnDiv = document.getElementById("turn");
	//If your turn
	if (player == data) {
		turnDiv.innerText = "Your Turn";
		yourTurn = true;
		var yourStringBoard = boardStringArray.join("");
		//		console.log(yourStringBoard);
		//Returns index of which tile was clicked by opponent and sunk ship
		$.get(WEB_APP, {
			"player" : player,
			"gameID" : gameID,
			"yourBoard" : yourStringBoard
		}).done(upYourBoard).fail(ajaxFail);
	} else {
		turnDiv.innerText = "Opponent's Turn";
		yourTurn = false;
		timer = setInterval(function() {
			wait(timer);
		}, 100);
	}

}

//Update your board
var upYourBoard = function(data) {
	var info = data.split(" ");
	var index = info[0];
	var ship = info[1];
	if (ship != "") {
		swal("You sunk your opponent's " + ship);
		//Update sunk ship
		if (player == 2)
			var tempPlayer = 1;
		else
			var tempPlayer = 2;
		$.post(WEB_APP, {
			"player" : tempPlayer,
			"shipName" : "",
			"gameID" : gameID
		}).fail(ajaxFail);
	}
	if (index != boardLength * boardLength) {
		var charRow = Math.floor(index / boardLength);
		var charCol = index % boardLength;
		var tile = yourTiles[index];
		var hitTiles = [];
		if (tile.classList.contains("P")) {
			hitTiles = document.getElementsByClassName("P");
			shipName = "patrol boat";
		} else if (tile.classList.contains("U")) {
			hitTiles = document.getElementsByClassName("U");
			shipName = "submarine";
		} else if (tile.classList.contains("D")) {
			hitTiles = document.getElementsByClassName("D");
			shipName = "destroyer";
		} else if (tile.classList.contains("B")) {
			hitTiles = document.getElementsByClassName("B");
			shipName = "battleship";
		} else if (tile.classList.contains("A")) {
			hitTiles = document.getElementsByClassName("A");
			shipName = "aircraft carrier";
		} else if (tileList.classList.contains("W")) {
			tile.classList.add("M");
			console.log("got miss");
			boardStringArray[charRow] = boardStringArray[charRow].substring(0, charCol) + "M" + boardStringArray[charRow].substring(charCol + 1);
		}
		if (checkSunkShip(hitTiles, shipName)) {
			swal("They sunk your " + shipName);
			checkEndGame();
		}

	}
};

var wait = function(timer) {
	var info = document.getElementById("player").innerHTML;
	info = info.split(" ");
	gameID = info[1];
	$.get(WEB_APP, {
		"turn" : gameID
	}).done(aF).fail(ajaxFail);

};

var aF = function(data) {
	//	alert(data);
	if (data == player) {
		clearInterval(timer);
		callTurn(data);
	}
};

function checkEndGame() {
	var shipTilesLength = document.getElementsByClassName("S").length;
	console.log(shipTilesLength);
	console.log(yourHitCount);
	console.log(oppHitCount);
	if (shipTilesLength == yourHitCount) {
		swal({
			title : "Too bad!",
			text : "You lost... Better luck next time.",
			imageUrl : "images/sadFace.png",
			showConfirmButton : false
		});
		setTimeout(reset, 4000);
	} else if (shipTilesLength == oppHitCount) {
		swal({
			title : "Congratulations!",
			text : "You won!!!",
			imageUrl : "images/thumbsUp.png",
			showConfirmButton : false
		});
		setTimeout(smallReset, 4000);
	}
}

var reset = function() {
	$.post(WEB_APP, {
		"reset" : gameID
	}).fail(ajaxFail);
	smallReset();
};

var smallReset = function() {
	window.location.href = "battleship.php";
};

var attack = function(tile) {
	if (yourTurn) {
		var index = oppTiles.indexOf(tile);
		//Checks what tile clicked is
		$.get(WEB_APP, {
			"tile" : index,
			"gameID" : gameID,
			"player" : player
		}).done(attack2).fail(ajaxFail);
	}
};

var attack2 = function(data) {
	if (yourTurn) {
		//		alert(data);
		var arry = data.split(" ");
		var index = arry[0];
		var hORm = arry[1];
		// hit or miss
		//console.log(index);
		index = parseInt(index);
		//console.log(index);
		var tile = oppTiles[index];
		//Ship
		if (hORm == "S") {
			console.log("you hit");
			tile.classList.add("H");
			oppHitCount++;
			checkEndGame();
			// var hitTiles;
			// var shipName = "ship!!!!!!";
			// if (tile.classList.contains("P")) {
			// hitTiles = document.getElementsByClassName("P");
			// shipName = "patrol boat";
			// } else if (tile.classList.contains("U")) {
			// hitTiles = document.getElementsByClassName("U");
			// shipName = "submarine";
			// } else if (tile.classList.contains("D")) {
			// hitTiles = document.getElementsByClassName("D");
			// shipName = "destroyer";
			// } else if (tile.classList.contains("B")) {
			// hitTiles = document.getElementsByClassName("B");
			// shipName = "battleship";
			// } else {
			// hitTiles = document.getElementsByClassName("A");
			// shipName = "aircraft carrier";
			// }
			yourTurn = false;
			// if (checkSunkShip(hitTiles)) {
			// swal("You sunk the opponenent's " + shipName); checkEndGame;
			//}
		} else if (hORm == "W") {
			console.log("you missed");
			tile.classList.add("M");
			yourTurn = false;
		}

		// If it is opp turn call turn
		if (!yourTurn) {
			if (player == 2) {
				var tempTurn = 1;
			} else {
				var tempTurn = 2;
			}
			//attack! updates opp's board
			$.post(WEB_APP, {
				"index" : index,
				"gameID" : gameID,
				"turn" : tempTurn,
				"attack" : hORm,
				"thisPlayer" : player
			}).fail(ajaxFail);
			callTurn(tempTurn);
		}
	}
};

var checkSunkShip = function(tiles, shipName) {

	for (var i = 0; i < tiles.length; i++) {
		if (!(tiles[i].classList.contains("H")))
			return false;
	}
	//Update sunk ship
	$.post(WEB_APP, {
		"player" : player,
		"shipName" : shipName,
		"gameID" : gameID
	}).fail(ajaxFail);
	return true;
};

var clickShip = function(button) {
	shipClicked = button;
};

var setShip = function(tile) {
	if (shipClicked == null) {
		return;
	}
	var shipAdded = false;
	var name = shipClicked.id;
	//alert(name);
	//If ship has been clicked 1st
	if (shipClicked != null) {
		//Check if this is firstClick
		if (firstClick == null) {
			firstClick = tile;
			firstClick.classList.add("selected");
		}
		//2nd click
		else {
			//Get row and column from index
			var index = yourTiles.indexOf(tile);
			var row = Math.floor(index / boardLength);
			var col = index % boardLength;

			var firstIndex = yourTiles.indexOf(firstClick);
			var firstRow = Math.floor(firstIndex / boardLength);
			var firstCol = firstIndex % boardLength;

			//Same row
			if (row == firstRow) {
				//See if correct size
				if (Math.abs(col - firstCol) + 1 == parseInt(shipClicked.innerText)) {
					if (firstCol < col) {
						var j = firstCol;
						var k = col;
					} else {
						var j = col;
						var k = firstCol;
					}

					var overlap = false;
					for (var i = j; i <= k; i++) {
						if (yourBoard[row][i].classList.contains("S")) {
							//console.log("overlap");
							overlap = true;
							break;
						}
					}

					if (!overlap) {//if there is no overlap then add ship normally
						for (var i = j; i <= k; i++) {
							yourBoard[row][i].classList.add("S");
							yourBoard[row][i].classList.add(name);
							boardStringArray[row] = boardStringArray[row].substring(0, i) + "S" + boardStringArray[row].substring(i + 1);
							//TODO add specific names
						}
						shipAdded = true;
					} else {
						swal("You cannot overlap ships.");
					}

				}
			}
			//Check column
			else if (col == firstCol) {
				if (Math.abs(row - firstRow) + 1 == parseInt(shipClicked.innerText)) {
					if (firstRow < row) {
						var j = firstRow;
						var k = row;
					} else {
						var j = row;
						var k = firstRow;
					}

					var overlap = false;
					for (var i = j; i <= k; i++) {
						if (yourBoard[i][col].classList.contains("S")) {
							//console.log("overlap");
							overlap = true;
							break;
						}
					}

					if (!overlap) {//if there is no overlap then add ship normally
						for (var i = j; i <= k; i++) {
							yourBoard[i][col].classList.add("S");
							yourBoard[i][col].classList.add(name);
							boardStringArray[i] = boardStringArray[i].substring(0, col) + "S" + boardStringArray[i].substring(col + 1);
							//TODO add specific names
						}
						shipAdded = true;
					} else {
						swal("You cannot overlap ships.");
					}

				}
			}
			firstClick.classList.remove("selected");
			firstClick = null;
			if (shipAdded == true) {
				removeShip(shipClicked);
				changeBorders(row, firstRow, col, firstCol);
			}
		}
	}
};

var ajaxFail = function(xhr, status, exception) {
	console.log(xhr, status, exception);
};

var removeShip = function(ship) {
	ship.classList.add("hidden");
	ship.classList.remove("S");
	shipClicked = null;
	shipSet++;
	if (shipSet == numShips) {
		document.getElementById("ready").disabled = false;
	}

};

var changeBorders = function(row, firstRow, col, firstCol) {
	if (row == firstRow) {
		if (col < firstCol) {
			yourBoard[row][col].classList.add("no-right");
			for (var i = col + 1; i < firstCol; i++) {
				yourBoard[row][i].classList.add("no-left");
				yourBoard[row][i].classList.add("no-right");
			}
			yourBoard[row][firstCol].classList.add("no-left");
		} else {
			yourBoard[row][col].classList.add("no-left");
			for (var i = firstCol + 1; i < col; i++) {
				yourBoard[row][i].classList.add("no-left");
				yourBoard[row][i].classList.add("no-right");
			}
			yourBoard[row][firstCol].classList.add("no-right");
		}
	} else {
		if (row < firstRow) {
			yourBoard[row][col].classList.add("no-bottom");
			for (var i = row + 1; i < firstRow; i++) {
				yourBoard[i][col].classList.add("no-bottom");
				yourBoard[i][col].classList.add("no-top");
			}
			yourBoard[firstRow][col].classList.add("no-top");
		} else {
			yourBoard[row][col].classList.add("no-top");
			for (var i = firstRow + 1; i < row; i++) {
				yourBoard[i][col].classList.add("no-bottom");
				yourBoard[i][col].classList.add("no-top");
			}
			yourBoard[firstRow][col].classList.add("no-bottom");
		}
	}
};
