<?php 
	
	require_once "functions.php"; 
	
	if( isset($_POST['isExit'])){
		resetCurrSession();
	}
	if(!isset($_SESSION['mail']) or !isset($_SESSION['password'])){
		if(!empty($_POST) && !empty($_POST['email']) && !empty($_POST['pswd'])){
			$_SESSION['mail'] = $_POST['email'];
			$_SESSION['password'] = $_POST['pswd'];
			if(isset($_POST['signUpBtn'])){
				$params = [
					
					'password' => $_POST['pswd'],
					'mail' => $_POST['email'],
					
				];
				
				
				$_POST['signUpBtn'] = null;
			} 
		} else {
			resetCurrSession();
			header("Location: \signIn.php"); 
			exit();
		}
	} else if (!checkClient($_SESSION['mail'], $_SESSION['password'])){
		echo "Not checked";
		resetCurrSession();
		header("Location: \signIn.php"); 
		exit();
	}

	refreshOrders();
	echo(HEAD);
	
	$html_string = '';
	$tempCount = getCountUnreadMessagesToClient($_SESSION['mail'], $_SESSION['password']);
	if(!empty($tempCount) && $tempCount>0){
		$html_string = sprintf('<span class="badge badge-info">%s</span>', $tempCount);
	}
	echo sprintf('
		<div class="container">
			
			<ul class="nav nav-tabs nav-fill">
				<!--<li class="nav-item">
					<a class="nav-link" data-toggle="tab" href="\settings.php">Налаштування користувача</a>
				</li>-->
				<li class="nav-item">
					<a class="nav-link active" data-toggle="tab" href="#tab1">Замовлення</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" data-toggle="tab" href="#tab2">Повідомлення %s</a> 
				</li>
			</ul>
			<div class="tab-content" id="accordionExample1">
				<div class="tab-pane fade show active" id="tab1">
			', $html_string );
	showClientContracts();
    
	#showClientContracts($_SESSION['mail'], $_SESSION['password']);
	echo('<p class="fw-3 fade show text-center">Для відображення інформації по приєднанням необхідно додати до свого кабінету контракт із <strong>Вашим індивідуальним податковим номером</strong>.</p><div class="text-center d-block ">
			<a class="btn btn-primary w-50 m-2" name="add_contract" href="\contracts_manager.php">Додати новий контракт</a> 
			</div>');
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
	<div class="tab-pane fade" id="tab2">');
	showAppeals();		
	#showClientDialogs($_SESSION['mail'], $_SESSION['password']);//'123456789'
	
	
	echo ('
		</div>
		<div class="tab-pane fade" id="tab3">
		..
		</div>
		</div>
	</div>');
	echo(FOOTER);
	
?>