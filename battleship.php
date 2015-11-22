<?php
/* Sweet
 * http://t4t5.github.io/sweetalert/
 */
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<?php
		if(isset($_POST["user"]) && isset($_POST["BoardSize"]) && isset($_POST["numships"]))
		{ ?>
			<script src="sweetalert-master/dist/sweetalert.min.js"></script> 
			<link rel="stylesheet" type="text/css" href="sweetalert-master/dist/sweetalert.css">
			<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
			<script type="text/javascript" src="battleship.js"></script>
		<?php
		}
		?>
		<link rel="stylesheet" href="battleship.css" type="text/css" />
		
		<title>BattleShip</title>
	</head>
	<body>
		<h1>Battleship</h1>
		<?php
		if(!(isset($_POST["user"]) && isset($_POST["BoardSize"]) && isset($_POST["numships"])))
		{ ?> <div id="login">
			<form action="battleship.php" method="post" id = "form">
				Player name
				<input name="user" id = "userName"/>
				<br />
				<!-- Password
				<input name="password" id = "password" type = "password"/>-->
				<br /> 
				Board Size
	            <select name="BoardSize">
	            	<?php
	           			for($i = 7; $i < 11; $i++) {
	           				?>
	           					<option><?=$i . " x " . $i ?></option>
	           				<?php
							}
	            	?>
	            </select>
	            <br />
	            Number of Ships
	            <select name="numships">
	            	<?php
	           			for($i = 2; $i < 6; $i++) {
	           				?>
	           					<option><?=$i ?></option>
	           				<?php
							}
	            	?>
	            </select>
	            <br />
	        	<input type="submit" value="Submit" id="submit"/>
	       	</form>
	    </div>
	    <?php
		}
		else
		{?>
			<div id ="user" class = "hidden"><?=$_POST["user"]?></div>
	       	<?php
	       	error_reporting(E_ALL);
	       	$size = 0;
	       	$numShips = 0;
			$db = new PDO("mysql:dbname=brownje", "brownje", "dbpassword");
			
			$user = $db->quote($_POST["user"]);
			$userNoQuote = $_POST["user"];
			//print($user);
			//$password = $db->quote($_POST["password"]);
			$size = $_POST["BoardSize"];
			$sizeQuote = $db->quote(intval($size));
			$numShips = $_POST["numships"];
			$numShipsQuote = $db->quote($_POST["numships"]);
			$sql = "SELECT u.Username FROM Users u WHERE u.UserName = $user;";
			//print($sql);
			$userName = $db->query($sql);
			$userNum = $userName->rowCount();
			//print($userNum);
			$gameID = 0;
			
			//new user
			if($userNum == 0)
			{
				$sql = "INSERT INTO Users (Username, BoardLength, NumShips)
				VALUES ($user, $sizeQuote, $numShipsQuote);";
				$db->exec($sql);
			}
			//Multiple user error!
			else if($userNum > 1)
			{
				print("ERRORRRR multiple users with same name");
			}
			//Returning user
			// else
			// {
				// $sql = "SELECT u.Password, u.GameID, u.BoardLength FROM Users u WHERE u.UserName = $user;";
				// $userInfo = $db->query($sql);
				// //Gets the one user's password and gameID
				// foreach ($userInfo as $info) {
					// $dbPass = $info['Password'];
					// $gameID = $info['GameID'];	
					// $length = $info['BoardLength'];
				// }
				// //print($dbPass. " " . $gameID ." " . $length);
				// if($dbPass != $_POST["password"])
				// {
					// print("Wrong pass");
					// $header("location:battleship.php");
				// }
				// //Make new game
				// //TODO maybe get rid of tell JOE
				// if($gameID == 0)
				// {
					// print("gameID = 0; returning user");
					 // // makeNewBoard($size, "yourTiles"); 
	        		 // // makeShips($numShips);
	        		 // // makeNewBoard($size, "oppTiles" );
	        		 // ?>
	        		 <!-- // <div id="player" class="hidden">1 <?=$gameID?></div> -->
	        		 <?php
				// }
				// // //Get game from db
				// // else 
				// // {
					// // print("getting game from db");
					// // $sql = "SELECT * FROM Games g
					// // WHERE g.GameID = $gameID;";
					// // $gameInfo = $db->query($sql);
					// // //Gets one game's info
					// // foreach($gameInfo as $info)
					// // {
						// // $p1 = $info['Player1'];
						// // $p2 = $info['Player2'];
						// // $turn = $info['Turn'];
						// // $b1 = $info['Board1'];
						// // $b2 = $info['Board2'];
					// // }
					// // print("Game info Player 1 $p1 Player 2 $p2 Turn $turn Board1 $b1 Board2 $b2");
					// // //Player1
					// // if($userNoQuote == $p1)
					// // {
						// // print("u r player1");
						// // $yourBoardToMake = $b1;
						// // $oppBoardToMake = $b2;
						// // ?>
	        			<!-- // // <div id="player" class="hidden">1 <?=$gameID?></div> -->
	        		 	 <?php
					// // }
					// // //Player2
					// // else
					// // {
						// // print("u r player2");
						// // $yourBoardToMake = $b2;	
						// // $oppBoardToMake = $b1;
						// // ?>
		        		 <!-- // // <div id="player" class="hidden">2 <?=$gameID?></div> -->
		        		 <?php
					// // }
					// // //Make board
					// // makeBoard($yourBoardToMake, $length, "yourTiles");
					// // makeBoard($oppBoardToMake, $length, "oppTiles");
				// // }
// 				
			// }
         		?>
			<br/>
			<div id = "yourName"></div>
   			<input type="submit" value="Ready?" id="ready" disabled="true"/>
       	
       		<?php
       		// Getting into a new game(either create or join)
       		//Create a game if cannot match
			if ($gameID == 0) {
				$found = false;;
				$time = 0;
				while ($found == false && $time < 10) {
					$sql = "SELECT * FROM Users u WHERE u.BoardLength = $sizeQuote 
				AND u.NumShips = $numShipsQuote AND u.GameID != 0 AND u.InGame = 0
				Limit 1;";
					$usersFound = $db -> query($sql);
					if($usersFound->rowCount() > 0)
						$found = true;
					sleep(0.5);
					$time += 1;
				}
				//Join game
				if ($usersFound->rowCount() > 0) {
					//Only 1
					foreach ($usersFound as $userFound) {
						$gameID = $userFound["GameID"];
					}
					$gameID = $db->quote($gameID);
					//print("joining game $gameID");
					$sql = "UPDATE Games SET Player2 = $user WHERE GameID = $gameID;";
					$db -> exec($sql);
					$sql = "UPDATE Users SET GameID = $gameID, InGame = '1' WHERE Username = $user;";
					$db -> exec($sql);
					$sql = "SELECT g.Player1 FROM Games g 
					WHERE g.GameID = $gameID;";
					$player1Info = $db->query($sql);
					foreach($player1Info as $player)
					{
						$player1 = $player["Player1"];
					}
					$player1 = $db->quote($player1);
					//print("Player 1 is $player1");
					$sql = "UPDATE Users SET InGame = '1' WHERE Username = $player1;";		
					$db->exec($sql);
					?>
	        		 <div id="player" class="hidden">2 <?=$gameID?></div>
	        		 <?php
				}
				//Create game
				else {
					$sql = "INSERT INTO Games (Turn, Player1)
					VALUES ('1', $user);";
					$db -> exec($sql);
					$sql = "SELECT g.GameID FROM Games g WHERE g.Player1 = $user;";
					//print($sql);
					$gameIDs = $db -> query($sql);
					//Returns 1 ID
					foreach ($gameIDs as $gameID) {
						$gID = $gameID['GameID'];
					}
					//print($gID);
					$gID = $db->quote($gID);
					//print("You created game $gID");
					$sql = "UPDATE Users SET GameID = $gID WHERE Username = $user;";
					$db -> exec($sql);
					//print("You updated game $gID");
					?>
	        		 <div id="player" class="hidden">1 <?=$gID?></div>
	        		 <?php
				}
				//Make board
				//print("Making board");
				
				?>
				<div id = "oppName"></div> 
				<div id = "boards">
				<?php
				?>
				<?php
				 makeNewBoard($size, "yourTiles"); 
        		 makeShips($numShips);
				 ?>
				 <?php
        		 makeNewBoard($size, "oppTiles");
				 ?>
        		 </div>
        		 <?php
			}
				
		}
       ?>
		<div id = "turn"></div>
		
       	
       	
       	
       	<?php
       function makeNewBoard($length, $name) {
				?>
				<div class="board"><?php
				$letters = ["A", "B", "C", "D", "E", "F", "G", "H", "I", "J"];
				for($i=0; $i<=$length; $i++) {
					for($j=0; $j <= $length; $j++)
					{
						if($i == 0 && $j == 0)
						{ 
							?><span class = "blank"></span><?php
						}
							//First row
							else if($i == 0)
							{
							?><span class="coord"><?=$letters[$j - 1] ?></span><?php
							}
							//First col
							else if($j == 0)
							{
							?><span class="coord"><?=$i ?></span><?php
							}
							//Tiles
							else
							{
							?><span class="<?=$name ?>"></span><?php
							}
							}
					?><br /><?php
					}
				?></div><?php
				}
			?>
		
		 	<?php
		 //Board form db
       function makeBoard($board, $length, $name) {
				?>
				<div class="board"><?php
				$boardArr = split("\n", $board);
				$letters = ["A", "B", "C", "D", "E", "F", "G", "H", "I", "J"];
				for($i=0; $i<=$length; $i++) {
					for($j=0; $j <= $length; $j++)
					{
						if($i == 0 && $j == 0)
						{ 
							?><span></span><?php
							}
							//First row
							else if($i == 0)
							{
							?><span class="coord"><?=$letters[$j - 1] ?></span><?php
							}
							//First col
							else if($j == 0)
							{
							?><span class="coord"><?=$i ?></span><?php
							}
							//Tiles
							else
							{
								//Get the letter for the class	
								$className = substr($boardArr[$i-1], $j-1, 1);
							?><span class="<?="$name $className"?>"></span><?php
							}
					}
					?><br /><?php
				}
				?></div><?php
				}
			?>
		
		
		
		 <?php function makeShips($num)
		{ ?>
			<div id = "shipBoard">
			<?php
			$ships = ["A", "P", "B", "D", "U"];
			$nums = [5, 2, 4, 3, 3];
			for($i = 0; $i < $num; $i++)
			{ ?>
				<div class="S" id="<?=$ships[$i] ?>"><?=$nums[$i] ?></div>
			<?php } ?> </div>
		<?php } ?>
		
	</body>
</html>

