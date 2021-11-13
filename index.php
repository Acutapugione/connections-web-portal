<?php 
	require_once "functions.php"; 
		
	if(!isset($_SESSION['login']) or !isset($_SESSION['password'])){
		if(!empty($_POST) && !empty($_POST['uname']) && !empty($_POST['pswd'])){
			$_SESSION['login'] = $_POST['uname'];
			$_SESSION['password'] = $_POST['pswd'];
			if(isset($_POST['signUpBtn'])){
				$params = [
					'login' => $_POST['uname'],
					'password' => $_POST['pswd'],
					'mail' => $_POST['email'],
					'ipn_key' => $_POST['ipnKey'],
				];
				
				pushClient($params);
				$_POST['signUpBtn'] = null;
			}
		} else {
			header("Location: \signIn.php"); 
			exit();
		}
	} 
	
	$ipn_key = getClientIpn($_SESSION['login'], $_SESSION['password']);//'123456789';
	$_SESSION['ipn'] =  $ipn_key;
	#var_dump($ipn_key);
	$_SESSION['mail'] = getClientMail($_SESSION['login'], $_SESSION['password']);
	echo(HEAD);
	echo ('
		<div class="container">
			<ul class="nav nav-tabs nav-fill">
				<!--<li class="nav-item">
					<a class="nav-link" data-toggle="tab" href="\settings.php">Налаштування користувача</a>
				</li>-->
				<li class="nav-item">
					<a class="nav-link active" data-toggle="tab" href="#tab1">Статус замовлень</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" data-toggle="tab" href="#tab2">Повідомлення</a>
				</li>
			</ul>
			<div class="tab-content" id="accordionExample1">
				<div class="tab-pane fade show active" id="tab1">
			');
	showClientOrders($_SESSION['ipn']);

	echo('
		<div class="divider mx-auto"></div>
		<div class="row">
			<div class="col-md-12 text-center">
				<ul class="list-inline">
					<li class="list-inline-item"><img class="avatar-img" src="img/pngwing1.png" alt=""> - завершено </li>
					<li class="list-inline-item"><img class="avatar-img" src="img/pngwing2.png" alt=""> - в роботі</li>
					<li class="list-inline-item"><img class="avatar-img" src="img/pngwing3.png" alt=""> - очікується</li>
				</ul>
			</div>
		</div>
	</div>
	<div class="tab-pane fade" id="tab2">
		');
			
	showClientDialogs($_SESSION['ipn']);//'123456789'
	
	echo ('
		</div>
		<div class="tab-pane fade" id="tab3">
		..
		</div>
		</div>
	</div>');
	echo(FOOTER);
	
?>