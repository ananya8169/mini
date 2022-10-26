<html>

<head>
	<title> Report </title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
	<script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

	<style>
		.navbar {
			text-align: center;
			font-size: 24px;
			background-color: #002366;
			color: white;
		}

		body {
			background-color: white;
		}

		.jumbotron {
			margin-left: auto;
			margin-right: auto;
			background-color: #002366;
			color: white;
			border-radius: 5%;
			text-align: center;
		}

		.footer {
			position: fixed;
			left: 0;
			bottom: 0;
			width: 100%;
			color: white;
			text-align: center;
		}
	</style>

</head>

<body>
	<nav class="navbar navbar-expand-lg">
		<span class="navbar-text">
			Data Leakage Detection
		</span>
	</nav>
	<br><br>
	<div class="container">
		<div class="row">
			<div class="col-md-3 ">
				<div class="list-group ">

					<a href="../Homepage.html" class="list-group-item list-group-item-action">Homepage</a>
					<a href="../Login&Registration/Admin.html" class="list-group-item list-group-item-action">Add User </a>
					<a href="../CosineSimilarity/SimilarityFrontend.html" class="list-group-item list-group-item-action">Similarity of Documents</a>
					<a href="#" style="background-color:#002366;color:white;" class="list-group-item list-group-item-action">Find Guilty</a>
					<a href="searchdata.html" class="list-group-item list-group-item-action">Display User Details</a>
					<a href="logout.php" class="list-group-item list-group-item-action">Logout</a>


				</div>
			</div>
			<div class="col-md-9">
				<div class="jumbotron text-center">
					<?php
					require_once('..\PDF\EmbedPdfLibrary.php');
					require_once('..\EncryptionAndDecryption\aes.php');
					require 'KeyGeneration.php';
					require '..\vendor\autoload.php';
					include('functions.php');

					if (isset($_POST['submit'])) {

						// Store File in Temp folder
						$file_name = $_FILES['file']['name'];
						$file_tmp = $_FILES['file']['tmp_name'];
						if (isset($file_name)) {
							if (!empty($file_name)) {
								move_uploaded_file($file_tmp, "LeakedFiles/" . $file_name);
							}
						}

						$ext = pathinfo($file_name, PATHINFO_EXTENSION);
						$pdfsrc = "LeakedFiles/" . $file_name;
						// echo "<br> pdfsrc </h3>".$pdfsrc;

						// Process
						//PDF 
						// echo "<h3> Extension</h3>".$ext;
						if ($ext == 'pdf' || $ext=='encrypt') {
							// echo "<h3> Entered</h3>";
							$data = extractPdfData($pdfsrc);
							// echo "<br><h3> Data </h3>".$data;
							$list = findUID($data);

							// Display List
							echo "<br> <h3> LIST OF ACCESS </h3>";
							foreach ($list as $person) {
								echo "<br>" . $person;
							}

							$len = count($list);
							if ($len > 2) {
								echo "<br><br> <h2> Leaked Agent : </h2>";
								echo $list[1];
							}
						}
					}
					?>
				</div>
			</div>
		</div>
	</div>
</body>

</html>