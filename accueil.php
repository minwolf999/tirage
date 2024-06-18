<?php
$image = "";
$phpFileUploadErrors = [
	1 => 'La taille du fichier dépasse celle définie dans la configuration du php.ini',
	2 => 'La taille du fichier dépasse celle spécifié dans le formulaire',
	3 => 'Le fichier a été upload partiellement',
	4 => 'Pas de fichier uploadé',
	6 => 'Le dossier temporaire n\'existe pas',
	7 => 'Impossible de sauvegarder le fichier sur le disque. Manque de droits',
	8 => 'Une extension PHP a arrêté l\'upload',
];

$errors = [];
$success = [];

if(isset($_POST['submit'])){
	if(empty($_FILES['image'])){
		$errors[] = sprintf("L'image a été oubliée !");
	}
}

if(!empty($_FILES['image'])){
	$total_count = count($_FILES['image']['name']);
	
	for( $i=0 ; $i < $total_count ; $i++ ) {
		$extension = pathinfo($_FILES['image']['name'][$i], PATHINFO_EXTENSION);

		if ($extension != "png" && $extension != "jpg" && $extension != "jpeg") {
			$errors['move_failed'] = "Le fichier joint n'est pas une image";
		} else {
			$tmpFilePath = $_FILES['image']['tmp_name'][$i];
   
			if ($tmpFilePath != ""){
			   $newFilePath = "image/" . $_FILES['image']['name'][$i];
			   if(move_uploaded_file($tmpFilePath, $newFilePath)) {
				//Other code goes here
			 }
			}
		}
	}
	if ($errors == []) {
		$success[] = sprintf("L'image a bien été ajoutée");
	}
}

	if (isset( $_POST['vider'] )) {

		$dir_name = "image/";  // nom du répertoire
		$dir = opendir( $dir_name );  // ouvre le répertoire
		$files = readdir( $dir );
	
		while ( $files = readdir( $dir ) ) {
		if ( $files != ".." ) // exception avec l'index.php
			unlink( "$dir_name/$files" );  // supprime chaque fichier du répertoire
		}
		closedir( $dir );
	}
	
	if (isset($_POST['tirage'])) {
		$count = -2;
		foreach (scandir("image/") as $i) :
			$count++;
		endforeach;
		if ($count >= 1) {
			$nbr = random_int(0, $count-1);
			$image = "image/".scandir("image/")[$nbr+2];
		}
	}
?>

<head>
    <link rel="stylesheet" href="style.css">
    <title>Tirage d'image</title>
</head>

<div class="form-message">
	<?php if(!empty($errors)): ?>
		<?php foreach($errors as $error): ?>
			<div class="alert">
				<span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
				<?= $error ?>
			</div>
		<?php endforeach; ?>
	<?php endif; ?>
	<?php if(!empty($success)): ?>
		<?php foreach($success as $msg): ?>
			<div class="alert sucess">
				<span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
				<?= $msg ?>
			</div>
		<?php endforeach; ?>
	<?php endif; ?>
</div>

<body>
    <center>
      	<h1>Tirage d'image aléatoire</h1>

		  <div class="image">
			<?php if ($image != "" ) : ?>
				<progress value="0" max="100" id="progress" class="progress"></progress>
				<img src="<?= $image ?>" alt="Image" id="completion-image" style="display: none;" width="352px" height="207px">
				
			<?php endif; ?>
		</div> <br>

		<form method="post" enctype="multipart/form-data" name="sendimage">
			<input type="file" id="image" name="image[]" multiple="multiple">
			<button type="submit" name="submit">Ajouter</button>
		</form>

		<form method="post" name="delimage">
			<button type="submit" name="vider">Supprimer toutes les images</button>
		</form>

		<form method="post" name="openfolder">
			<button onclick="page = window.open('http://localhost/tirage maman/image');">Ouvrir le dossier de téléchargement d'image</button>
		</form>

		<form method="post" name="tirage">
			<button type="submit" name="tirage">Afficher une image au hasard</button>
		</form>
	</center>
</body>

<script>
    var progress = document.querySelector("#progress");
    var completionImage = document.querySelector("#completion-image");
    var i = 0;
    var interval = setInterval(() => {
        progress.value = i;
        if (i === progress.max) {
            clearInterval(interval); // Arrêter l'intervalle une fois que la valeur maximale est atteinte
            progress.style.display = "none"; // Masquer la barre de progression
            completionImage.style.display = "inline"; // Afficher l'image de complétion
			completionImage.style.animation = "fadein 4s"
        }
        i++;
    }, 1);
</script>
