<?php
	define('LOCAL_1C', "{your 1c html service local address}");
	define('OUTER_1C', "{your 1c html service address}");
	define('TEST_1C',  "{your 1c html service test address}");
	
	define('DOCS_1C_POST_APPEAL', "{your 1c html service address for post appeals}");
	define('DOCS_1C_GET_APPEAL', "{your 1c html service address for get appeals}");
	
	define('DATA_BASE',	'{your db name}');
	define('LOGIN',	'{your login}');
	define('PASSWORD',	'{your password}');
	
	define('PORT',	'3306');
    define('AP_PORT', '80');
    
	define('ADDRESS',	'127.0.0.1');
	define('HOST',	ADDRESS.':'.PORT);
	define('AP_HOST', ADDRESS.':'.AP_PORT);	
	
    $GLOBALS['CONNECTION'] = mysqli_connect(HOST, LOGIN, PASSWORD);
	
    define('CHAR_SET',"default charset='utf8'");
	
	if(!empty($GLOBALS['CONNECTION'])){
        define('SELECT_DB',	mysqli_select_db($GLOBALS['CONNECTION'], DATA_BASE));
    }
	$GLOBALS['ICONS']= [
		'success'=> 'img/pngwing1.png', 
		'unCompleted'=> 'img/pngwing2.png', 
		'unPayed'=> 'img/pngwing3.png',
		'edit' => 'img/green_edit_pencil.png',
		'delete' => 'img/red_delete_ico.png',
		];
	
	/***Typical example***/
	
	define('FOOTER', ('
			<footer class="page-footer">
				<div class="container">
					<div class="row mb-5">
						<div class="col-lg-6 py-6">
							<h5>АТ Херсонгаз</h5>
							<p>73036, м.Херсон, вул. Поповича, 3</p>
							<p><a href="mailto:Secretary@gaz.kherson.ua">Secretary@gaz.kherson.ua</a></p>
							<p><a target="_blank" href="http://gaz.kherson.ua"> http://gaz.kherson.ua </a></p>
							<p>Служба підтримки сервісу з питань реєстрації та роботи в персональному кабінеті оператора ГРМ в Херсонській області - АТ Херсонгаз</p>
							<p><a href="tel:+380552354787">Телефон (0552) 35-47-87</a></p>
						</div>
						<div class="col-lg-6 py-6">
							<h5><span>Контактні телефони</span></h5>
							<p>
								Великоолександрівський та Високопільський райони – 050-49-48-801<br />
								Білозерський район – 050-44-58-526<br />
								Олешківський район – 050-326-34-38<br />
								Голопристанський район – 7-82-77<br />
								Каланчацький та Чаплинський райони – 3-11-64<br />
								Скадовський район – 050-326-34-40<br />
								Каховський, Бериславський райони м Нова Каховка – 050-494-76-03<br />
								м. Херсон – 050-396-70-93, 050-318-52-42<br />
							</p>
						</div>
					</div>
					<p class="text-center" id="copyright">&copy; 2021 КАБІНЕТ ПРИЄДНАННЯ <a href="http://gaz.kherson.ua/" target="_blank"> АТ ХЕРСОНГАЗ</a></p>
				</div>
			</footer>

		<!-- JavaScript includes -->

		<script src="js/jquery.min.js"></script>
		<script src="js/bootstrap.js"></script>
		<script src="js/script.js"></script>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
		</body>
	</html>'));
	define('HEAD_SIGN_IN', ('
			<!DOCTYPE html>
			<html>
			<head>
				<meta charset="utf-8" />
				<title>Вхід</title>
			
				<!-- CSS stylesheet file -->
				<link rel="stylesheet" href="css/bootstrap.css" />
				<link rel="stylesheet" href="css/theme.css" />
			
			</head>
			<body>
				<!-- Back to top button -->
				<div class="back-to-top"></div>
				<header>
					<nav class="navbar navbar-expand-lg navbar-light bg-white sticky" data-offset="500">
						<div class="container">
							<a href="https://gaz.kherson.ua/" class="navbar-brand"><span class="text-primary">АТ Херсонгаз</span></a>

							<button class="navbar-toggler" data-toggle="collapse" data-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
								<span class="navbar-toggler-icon"></span>
							</button>

							<div class="navbar-collapse collapse justify-content-end" id="navbarContent">
								<ul class="navbar-nav ml-auto">
									<!--<li class="nav-item active">
										<a class="nav-link" href="#">Головна</a>
									</li>
									<li class="nav-item">
										<a class="btn btn-primary ml-lg-2" href="\messenger.php">Написати повідомлення</a>
									</li>-->
									<li class="nav-item">
										<a class="btn btn-primary ml-lg-2" href="\signUp.php" type="button" class="btn btn-danger">Ще не реєструвалися?</a>
									</li>
									
								</ul>
							</div>
						</div>
					</nav>
					<div class="container">
						<div class="page-banner">
							<div class="text-center wow fadeInUp">
								<div class="subhead">електронний сервіс</div>
								<h2 class="title-section">Персональний кабінет приєднання</h2>
								<div class="divider mx-auto"></div>
							</div>
						</div>
					</div>
				</header>'));
	define('HEAD_SIGN_UP', ('
			<!DOCTYPE html>
			<html>
			<head>
				<meta charset="utf-8" />
				<title>Реєстрація</title>
			
				<!-- CSS stylesheet file -->
				<link rel="stylesheet" href="css/bootstrap.css" />
				<link rel="stylesheet" href="css/theme.css" />
			
			</head>
			<body>
				<!-- Back to top button -->
				<div class="back-to-top"></div>
				<header>
					<nav class="navbar navbar-expand-lg navbar-light bg-white sticky" data-offset="500">
						<div class="container">
							<a href="https://gaz.kherson.ua/" class="navbar-brand"><span class="text-primary">АТ Херсонгаз</span></a>

							<button class="navbar-toggler" data-toggle="collapse" data-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
								<span class="navbar-toggler-icon"></span>
							</button>

							<div class="navbar-collapse collapse justify-content-end" id="navbarContent">
								<ul class="navbar-nav ml-auto">
									<!--<li class="nav-item active">
										<a class="nav-link" href="#">Головна</a>
									</li>
									<li class="nav-item">
										<a class="nav-link" href="#">Контакти</a>
									</li>
									<li class="nav-item">
										<a class="btn btn-primary ml-lg-2" href="\messenger.php">Написати повідомлення</a>
									</li>-->
									<li class="nav-item">
										<a class="btn btn-primary ml-lg-2" href="\signIn.php" type="button" class="btn btn-danger">Вже зареєстровані?</a>
									</li>
									<!--<li class="nav-item">
										<form  name="headerMenu" action="" method="POST">
											<button class="btn btn-primary ml-lg-3" href="" name="isExit">Вихід</button>
										</form>
									</li>-->
								</ul>
							</div>
						</div>
					</nav>
					<div class="container">
						<div class="page-banner">
							<div class="text-center wow fadeInUp">
								<div class="subhead">електронний сервіс</div>
								<h2 class="title-section">Персональний кабінет приєднання</h2>
								<div class="divider mx-auto"></div>
							</div>
						</div>
					</div>
				</header>'));
	define('HEAD_MESSAGER', ('
			<!DOCTYPE html>
			<html>
			<head>
				<meta charset="utf-8" />
				<title>Головна</title>
			
				<!-- CSS stylesheet file -->
				<link rel="stylesheet" href="css/bootstrap.css" />
				<link rel="stylesheet" href="css/theme.css" />
			
			</head>
			<body>
				<!-- Back to top button -->
				<div class="back-to-top"></div>
				<header>
					<nav class="navbar navbar-expand-lg navbar-light bg-white sticky" data-offset="500">
						<div class="container">
							<a href="https://gaz.kherson.ua/" class="navbar-brand"><span class="text-primary">АТ Херсонгаз</span></a>

							<button class="navbar-toggler" data-toggle="collapse" data-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
								<span class="navbar-toggler-icon"></span>
							</button>

							<div class="navbar-collapse collapse justify-content-end" id="navbarContent">
								<ul class="navbar-nav ml-auto">
									<li class="nav-item active">
										<a class="btn btn-primary ml-lg-2" href="\index.php">Головна</a>
									</li>
									<!--<li class="nav-item">
										<a class="btn btn-primary ml-lg-2" href="#">Контакти</a>
									</li>
									<li class="nav-item">
										<a class="btn btn-primary ml-lg-2" href="\messenger.php">Написати повідомлення</a>
									</li>-->
								</ul>
							</div>
						</div>
					</nav>
					<div class="container">
						<div class="page-banner">
							<div class="text-center wow fadeInUp">
								<div class="subhead">електронний сервіс</div>
								<h2 class="title-section">Персональний кабінет приєднання</h2>
								<div class="divider mx-auto"></div>
							</div>
						</div>
					</div>
				</header>'));
	define('HEAD_SETTINGS', ('
			<!DOCTYPE html>
			<html>
			<head>
				<meta charset="utf-8" />
				<title>Користувацькі налаштування</title>
			
				<!-- CSS stylesheet file -->
				<link rel="stylesheet" href="css/bootstrap.css" />
				<link rel="stylesheet" href="css/theme.css" />
			
			</head>
			<body>
				<!-- Back to top button -->
				<div class="back-to-top"></div>
				<header>
					<nav class="navbar navbar-expand-lg navbar-light bg-white sticky" data-offset="500">
						<div class="container">
							<a href="https://gaz.kherson.ua/" class="navbar-brand"><span class="text-primary">АТ Херсонгаз</span></a>

							<button class="navbar-toggler" data-toggle="collapse" data-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
								<span class="navbar-toggler-icon"></span>
							</button>

							<div class="navbar-collapse collapse justify-content-end" id="navbarContent">
								<ul class="navbar-nav ml-auto">
									<li class="nav-item active">
										<a class="btn btn-primary ml-lg-2" href="\index.php">Головна</a>
									</li>
									<!--<li class="nav-item">
										<a class="btn btn-primary ml-lg-2" href="#">Контакти</a>
									</li>
									<li class="nav-item">
										<a class="btn btn-primary ml-lg-2" href="\messenger.php">Написати повідомлення</a>
									</li>-->
								</ul>
							</div>
						</div>
					</nav>
					<div class="container">
						<div class="page-banner">
							<div class="text-center wow fadeInUp">
								<div class="subhead">електронний сервіс</div>
								<h2 class="title-section">Персональний кабінет приєднання</h2>
								<div class="divider mx-auto"></div>
							</div>
						</div>
					</div>
				</header>'));
	define('HEAD', ('
			<!DOCTYPE html>
			<html>
			<head>
				<meta charset="utf-8" />
				<title>Головна</title>
			
				<!-- CSS stylesheet file -->
				<link rel="stylesheet" href="css/bootstrap.css" />
				<link rel="stylesheet" href="css/theme.css" />
			
			</head>
			<body>
				<!-- Back to top button -->
				<div class="back-to-top"></div>
				<header>
					<nav class="navbar navbar-expand-lg navbar-light bg-white sticky" data-offset="500">
						<div class="container">
							<a href="https://gaz.kherson.ua/" class="navbar-brand"><span class="text-primary">АТ Херсонгаз</span></a>

							<button class="navbar-toggler" data-toggle="collapse" data-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
								<span class="navbar-toggler-icon"></span>
							</button>

							<div class="navbar-collapse collapse justify-content-end" id="navbarContent">
								
									<ul class="navbar-nav ml-auto">
										<!--<li class="nav-item active">
											<a class="nav-link" href="#">Головна</a>
										</li>-->
										<li class="nav-item">
											<a class="btn btn-primary ml-lg-6" href="\settings.php">Налаштування користувача</a>
										</li>
										<li class="nav-item">
											<a class="btn btn-primary ml-lg-6" href="\messenger.php">Написати повідомлення</a>
										</li>
										<li class="nav-item">
										<form  name="headerMenu" action="" method="POST">
											<button class="btn btn-primary ml-lg-3" href="" name="isExit">Вихід</button>
										</form>
										</li>
									</ul>
								
							</div>
						</div>
					</nav>
					<div class="container">
						<div class="page-banner">
							<div class="text-center wow fadeInUp">
								<div class="subhead">електронний сервіс</div>
								<h2 class="title-section">Персональний кабінет приєднання</h2>
								<div class="divider mx-auto"></div>
							</div>
						</div>
					</div>
				</header>'));
?>