<?php
//phpinfo();


$db = new PDO("mysql:dbname=brownje", "brownje", "dbpassword");
//print("hi");


// Returns the turn
if(isset($_REQUEST["turn"]))
{	
	$gameID = $_REQUEST["turn"];
	$rows = $db->query("SELECT g.Turn FROM Games g WHERE g.GameID = $gameID;");
	foreach($rows as $row)
	{
		print($row["Turn"]);
	}

}

// Updates your board 
if(isset($_REQUEST["board"]))
{
	$playerNum = $_REQUEST["player"];
	$gameID = $_REQUEST["oppGameID"];
	$stringBoard = implode($_REQUEST["board"]);
//		print($gameID);
//		print($stringBoard);
	if($playerNum == 1) {
		$board = 'Board1';
	} else {
		$board = 'Board2';
	}
	$stringBoard = $db->quote($stringBoard);
//		print($stringBoard);
	$sql = "UPDATE Games g SET $board = $stringBoard WHERE g.GameID = $gameID";
//	print($sql);
	$db->exec($sql);
	
} 
//Gets turn, opponents board, and player
if(isset($_REQUEST["oppGameID"]))
{
	$playerNum = $_REQUEST["player"];
	$gameID = $_REQUEST["oppGameID"];
	
	if($playerNum == 1) {
		$oppPlayer = "Player2";
		$oppBoard = "Board2";
	} else {
		$oppPlayer = "Player1";
		$oppBoard = "Board1";
	}
	
	//Get turn and other player and their board
	$sql = "SELECT g.Turn, g.$oppPlayer, g.$oppBoard FROM Games g WHERE g.GameID = $gameID";
	//print($sql);
	$rows = $db->query($sql);
	foreach($rows as $row) {
		$turn = $row['Turn'];
		$playerName = $row[$oppPlayer];
		$opponBoard = $row[$oppBoard];
	}
	//print($turn);
	$arr = "$turn $playerName $opponBoard";
	print $arr;
}

// Returns the tile.
if(isset($_GET["tile"])) {
	$index = $_GET["tile"];
	$gameID = $_GET["gameID"];
	$thisPlayer = $_GET["player"];
	if($thisPlayer == 1) {
		$board = "Board2";
	} else {
		$board = "Board1";
	}
	$sql = "SELECT g.$board FROM Games g WHERE g.GameID = $gameID";
	$rows = $db->query($sql);
	$board_quote = $db->quote($board);
	foreach($rows as $row) {
		$oppBoard = $row[$board];
	}
	$tile = substr($oppBoard, $index, 1);
	$arr = "$index $tile";
	print $arr;
} 

// Update opponent's board
if(isset($_REQUEST["attack"])){
	$index = $_REQUEST["index"];
	$gameID = $_REQUEST["gameID"];
	$turn = $_REQUEST["turn"];
	$tile = $_REQUEST["attack"];
	$thisPlayer = $_REQUEST["thisPlayer"];
	if($thisPlayer == 1) {
		$board = "Board2";
	} else {
		$board = "Board1";
	}
	$sql = "SELECT g.$board FROM Games g WHERE g.GameID = $gameID";
	$rows = $db->query($sql);
	$board_quote = $db->quote($board);
	foreach($rows as $row) {
		$oppBoard = $row[$board];
	}
	if($tile == "S") {
		$oppBoard = substr($oppBoard, 0, $index) . "H" . substr($oppBoard, $index+1);
	} else { 
		$oppBoard = substr($oppBoard, 0, $index) . "M" . substr($oppBoard, $index+1);
	} 
	$oppBoard_quoted = $db->quote($oppBoard);
	//print($oppBoard_quoted);
	$sql = "UPDATE Games g SET $board = $oppBoard_quoted, Turn = $turn WHERE g.GameID = $gameID";
	//print($sql);
	$db->exec($sql);
}

//Gets the index of the tile that was clicked by opponent and the ship sunk
if(isset($_REQUEST["yourBoard"])) {
	$player = $_REQUEST["player"];
	$board = $_REQUEST["yourBoard"];
	$gameID = $_REQUEST["gameID"];
	//print($player);
	//print($gameID);
	if($player == 1) {
		$queryBoard = "Board1";
		$ship = "Ship2";
	} else {
		$queryBoard = "Board2";
		$ship = "Ship1";
	}
	$sql = "SELECT g.$queryBoard, g.$ship FROM Games g WHERE g.GameID = $gameID";
	$rows = $db->query($sql);
	//print($sql);
	//$queryBoardQuoted = $db->quote($queryBoard);
	foreach($rows as $row) {
		$yourBoard = $row[$queryBoard];
		$shipSunk = $row[$ship];
	}
	//print($board . "\n");
	//print($yourBoard);
	// Found on StackOverflow http://stackoverflow.com/questions/7475437/find-first-character-that-is-different-between-two-strings
	$index = strspn($board ^ $yourBoard, "\0"); 
	$arr = "$index $shipSunk";
	print $arr;												
}

// Called to get them out of the game
if(isset($_REQUEST["reset"])){
	$gameID = $_REQUEST["reset"];
	$sql = "DELETE FROM Users WHERE GameID = $gameID";
	$db->exec($sql);
	$sql = "DELETE FROM Games WHERE GameID = $gameID";
	$db->exec($sql);
}
//Adding sunk ship
if(isset($_REQUEST["shipName"]))
{
	$shipName = $_REQUEST["shipName"];
	$gameID = $_REQUEST["gameID"];
	$player = $_REQUEST["player"];
	
	if($player == 1) {
		$ship = 'Ship1';
	} else {
		$ship = 'Ship2';
	}
	$shipNameQuote = $db->quote($shipName);
//		print($stringBoard);
	$sql = "UPDATE Games g SET $ship = $shipNameQuote WHERE g.GameID = $gameID";
//	print($sql);
	$db->exec($sql);
}

?>
