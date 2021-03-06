﻿<?php

	require("../functions.php");
	
	require("../class/User.class.php");
	$User = new User($mysqli);
	
	require("../class/Helper.class.php");
	$Helper = new Helper();
	
	//kas on sisse loginud, kui ei ole, siis suunata login lehele
	
	if (!isset ($_SESSION["userId"])) {
		
		header("Location: login.php");
		exit();
	}
	
	if (!isset($_GET["m"]) && !isset($_GET["y"])) {
		
		header("Location: user.php?m=0&y=0");
		exit();
	}
	
	//kas ?logout on aadressireal
	if (isset($_GET["logout"])) {
		
		session_destroy ();
		header ("Location: login.php");
		exit();
	}
	
	$user_msg= "";
	if(isset($_SESSION["user_message"])){
		$user_msg = $_SESSION["user_message"];
	
		unset($_SESSION["user_message"]);
	}
	
	$trainingdate = "";
	if(!isset($_GET["date"]) && !isset($_GET["month"]) && !isset($_GET["year"])){
		$trainingdate = "";
	} else {
		$trainingdate = $_GET["date"].'.  '.$_GET["month"].' '.$_GET["year"];
	}
	
	//ei ole tühjad väljad, mida salvestada
	if  (isset($_POST["exercise"]) &&
		!empty($_POST["exercise"])&&
		($trainingdate != "")
		) {
			$User->saveExercise($trainingdate, $Helper->cleanInput($_POST["exercise"]), $Helper->cleanInput($_POST["sets"]), $Helper->cleanInput($_POST["repeats"]), $Helper->cleanInput($_POST["notes"]));
		}
	
	$exercise = "";
	$exerciseError = "";
	$trainingDateError = "";
	$sets = "";
	$repeats = "";
	
	if (isset ($_POST["exercise"]) ){ 
		if (empty ($_POST["exercise"]) ){ 
			$exerciseError = "<p style='color: red;'>Palun täida see väli!</p>";		
		} else {
			$exercise= $_POST["exercise"];
		}
		
		if ($trainingdate == ""){
			$trainingDateError = "<p style='color: red;'>Palun vali kuupäev!</p>";
		}
	}
	
	if (isset ($_POST["sets"]) ){ 
		if (!empty ($_POST["sets"]) ){ 
			$sets= $_POST["sets"];	
		} 
	}
	
	if (isset ($_POST["repeats"]) ){ 
		if (!empty ($_POST["repeats"]) ){ 
			$repeats= $_POST["repeats"];	
		} 
	}
	
	if (isset ($_POST["q"]) ){ 
		$q= $_POST["q"];
		header('Location: user.php?m=0&y=0&q='. $q .''); //wooow nüüd töötab!!
		exit();
	}
	
	if(isset($_GET["q"])) {
		
		//kui otsib, võtame otsisõna aadressirealt
		$q = $_GET["q"];
		//echo "Hakkab";
	} else {
		//kas otsisõna on tühi
		$q = "";
	}
	
	$sort = "id";
	$order = "ASC";
	
	if (isset($_GET["sort"]) && isset($_GET["order"])) {
		$sort = $_GET["sort"];
		$order = $_GET["order"];
	}

	$userData = $User->addToArray();
	$userExercises = $User->get($q, $sort, $order);
	
	$est_gender = "";
	if ($userData->gender == "female") {
		$est_gender = "naine";
	}
	
	if ($userData->gender == "male"){
		$est_gender = "mees";
	}
	
	$exerciseMsg = "";
	if(isset($_SESSION["Exercise_message"])){
		$exerciseMsg = $_SESSION["Exercise_message"];
	
		unset($_SESSION["Exercise_message"]);
	}
	
	if(isset($_GET["exerciseId"]) && isset($_GET["delete"]) ) {
		
		$User->delExercise($_GET["exerciseId"]);
		header("Location: user.php?m=0&y=0");
		exit();
 	}
	
	$exerciseDelMsg = "";
	if(isset($_SESSION["Del_exercise_message"])){
		$exerciseDelMsg = $_SESSION["Del_exercise_message"];
	
		unset($_SESSION["Del_exercise_message"]);
	}
	

?>
<?php require("../header.php"); ?>
<?php require("../CSS.php")?>

<div class "data" style="padding-left:20px; padding-right:20px">
	<div align="center"> <h1>Minu treeningpäevik</h1>
		<p>
			<b><a href="data.php">&larr; Tagasi foorumisse</a></b><br>
		</p>
	</div>

	<div class="user" style="padding-left:10px;"> 
		<div class="row">
			
			<div class="col-sm-3 col-md-3" style="padding-left:3%">
				<?=$exerciseMsg;?>
				<?=$exerciseDelMsg;?>
				<h2>Lisa tehtud treening</h2>
					<p><b>Vali kalendrist kuupäev: <span style="color:green;"><?php echo $trainingdate; ?> </span></b><?php echo $trainingDateError; ?> </p>
					<form method="POST"> 
					<label>Treeningharjutus</label><br>
								
							<input class="form-control input-sm" type="text" name="exercise" value="<?=$exercise;?>"> <?php echo $exerciseError;?> <br>
						
					<label>Seeria</label><br>
							
							<input class="form-control input-sm" type="text" name="sets" value="<?=$sets;?>">  <br>
							
					<label>Kordus</label><br>
							
							<input class="form-control input-sm" type="text" name="repeats" value="<?=$repeats;?>"> <br>
							
					
							
						
					<label>Märkmed</label><br>
					
					<textarea class="form-control input-sm" cols="40" rows="2" name="notes" <?=$newNotes= ""; if (isset($_POST['notes'])) { $newNotes = $_POST['notes'];}?>><?php echo $newNotes; ?></textarea> <br>
					
					<input type="submit"  class="btn btn-success" value="Salvesta">	
					</form>
					<br>
			</div>
		
			<div class="col-sm-7 col-md-5" style="padding-left:3%; padding-top:3%;">
				<br>
				<head></head>
				<body>
					<?php
					error_reporting(E_ALL ^ E_WARNING);
					require_once('../calender/lib/donatj/SimpleCalendar.php');

					$calendar = new donatj\SimpleCalendar($_GET['y'], $_GET['m']);

					$calendar->setStartOfWeek('Monday');

					$calendar->addDailyHtml( 'Täna', 'today');
					$calendar->show(true);
					?>
				</body>
			</div>
			
			<div class="col-sm-7 col-md-4" style="padding-right:4%;">
				<?=$user_msg;?>
				<h2>Minu andmed</h2>
					
					<p>Eesnimi: <?php echo $userData->firstname;?></p>
					<p>Perekonnanimi: <?php echo $userData->lastname;?></p>
					<p>Kasutaja e-post: <?php echo $userData->email;?></p>
					<p>Sugu: <?php echo $est_gender?></p>
					<p>Telefoninumber: <?php echo $userData->phonenumber;?></p>
					
					<p><a class="btn btn-default btn-sm" href="editUser.php"><span class='glyphicon glyphicon-pencil'></span> Muuda andmeid</a></p>
					<p><a href="editPassword.php">Muuda parooli</a></p>
					<br><br><br>
					<h3>Otsi tehtud treeninguid</h3>
					<form method="POST">
					<div class="form-group">
						<div class="row">
							<div class="col-sm-8">
								<input type="text" class="form-control input-sm" name="q" value="<?=$q;?>">
							</div> 
							<div class="col-sm-4">
								<input type="submit"  class="form-control btn-md" value="Otsi">
							</div> 
						</div>
					</div>
					</form>
					<br>
			</div>
		</div>
	</div>


	<?php

		$html = "<table class='table table-hover'>";

		//TABELI SORTEERIMINE
		$html .= "<tr>";
		
			$exerciseOrder = "ASC";
			$setsOrder="ASC"; 
			$repeatsOrder="ASC"; 
			//$createdOrder="ASC";
			$exerciseArrow = "&uarr;";
			$setsArrow = "&uarr;";
			$repeatsArrow = "&uarr;";
			//$createdArrow = "&uarr;";

			
			if (isset($_GET["sort"]) && $_GET["sort"] == "exercise") {
				if (isset($_GET["order"]) && $_GET["order"] == "ASC") {
					$exerciseOrder="DESC";
					$exerciseArrow = "&darr;";
				}
			}
			
			if (isset($_GET["sort"]) && $_GET["sort"] == "sets") {
				if (isset($_GET["order"]) && $_GET["order"] == "ASC") {
					$setsOrder="DESC"; 
					$setsArrow = "&darr;";
				}
			}
			
			if (isset($_GET["sort"]) && $_GET["sort"] == "repeats") {
				if (isset($_GET["order"]) && $_GET["order"] == "ASC") {
					$repeatsOrder="DESC";
					$repeatsArrow = "&darr;";
				}
			}
			
			//SRY sõbrad, ei saa, tegu pole timestambiga. 
			/*if (isset($_GET["sort"]) && $_GET["sort"] == "training_time") {
				if (isset($_GET["order"]) && $_GET["order"] == "ASC") {
					$createdOrder="DESC";
					$createdArrow = "&darr;";
				}
			}*/

		$html .= "<thead class='exercises_head'>";
			$html .= "<th>
					<a href='?m=0&y=0&q=".$q."&sort=exercise&order=".$exerciseOrder."'><font color='white'><u>
						Treeningharjutus".$exerciseArrow."</u></font>
					</a>
					</th>";
			$html .= "<th>
					<a href='?m=0&y=0&q=".$q."&sort=sets&order=".$setsOrder."'><font color='white'><u>
						Seeria ".$setsArrow."</u></font>
					</a>	
					</th>";
			$html .= "<th>
					<a href='?m=0&y=0&q=".$q."&sort=repeats&order=".$repeatsOrder."'><font color='white'><u>
						Kordused ".$repeatsArrow."</u></font>
					</a>
					</th>";
			$html .= "<th><font color='white'>Kuupäev</font>
					</a>
					</th>";
			$html .= "<th><font color='white'>Märkmed</font></th>";
			$html .= "<th></th>";
		$html .= "</tr>";
		$html .= "</thead>";
		
		foreach($userExercises as $p) {
			$html .= "<tr class='exercises_body'>";
				$html .= "<td>".$p->exercise."</td>";
				$html .= "<td>".$p->sets."</td>";
				$html .= "<td>".$p->repeats."</td>";
				$html .= "<td>".$p->training_time."</td>";
				$html .= "<td>".$p->notes."</td>";
				$html .= "<td><a class='btn btn-danger btn-sm' href='user.php?m=0&y=0&exerciseId=".$p->id."&delete=true'><span class='glyphicon glyphicon-trash'></span></a></td>";
			$html .= "</tr>";	
		}

		$html .= "</table>";
		echo $html;
	?>
</div>

<?php require("../footer.php"); ?>