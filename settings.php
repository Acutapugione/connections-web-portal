<?php
	require_once "functions.php"; 

	if(isset($_POST['delete'])){
		removeContract($_POST['delete']);
		header("Location: \contracts_manager.php"); 
		exit();
	}

	if( isset($_POST['isExit'])){
		resetCurrSession();
	}
	if( isset($_SESSION['mail']) &&
		isset($_SESSION['password']) 
	){
		$_POST['client_id'] = getClientId($_SESSION['mail'], $_SESSION['password']);
		echo(HEAD_SETTINGS);
		if( (!empty($_POST) && (
				isset($_POST['pswd']) &&
				isset($_POST['pswd_double']) &&
				isset($_POST['email']) &&
				isset($_POST['updateBtn'])))){
		
		if($_POST['pswd'] == $_POST['pswd_double']){
			if (refreshClientInfo($_POST)){
					echo '<div class="alert alert-success alert-dismissible fw-5 fade show text-dark">
							<strong>Чудово!</strong> Ваші налаштування оновлено.
							<a href="\index.php" type="button" class="btn btn-success">Повернутися до головної</a>
							<a href="\settings.php" type="button" class="btn btn-success">Назад</a>
						  </div>';
					resetCurrSession();
				
			} else {
				echo '<div class="alert alert-danger alert-dismissible fw-5 fade show text-danger">
						<a href="\settings.php" type="button" class="btn btn-danger">Ще раз</a>
						<strong>Увага!</strong> Ваші налаштування не було оновлено, спробуйте ще раз.
					  </div>';
			}
		} else {
			echo '<div class="alert alert-danger alert-dismissible fw-5 fade show text-danger">
					<a href="\settings.php" type="button" class="btn btn-danger">Ще раз</a>
					<strong>Увага!</strong> Ваші налаштування не було оновлено, спробуйте ще раз.
				  </div>';
		}
		} else {
		
			echo sprintf('
			<div class="container">
				<form name="settingsForm" action="" class="was-validated" method="POST">	
				
					<!--<div class="input-group">
						<span class="input-group-text">Логін</span>
						<input id="uname" name="uname" type="text" class="form-control"  placeholder="Введіть логін" value="%s" required>
						<div class="valid-feedback">Коректно.</div>
						<div class="invalid-feedback">Будь ласка, заповніть логін.</div>
					</div>-->
					
					<div class="input-group">
						<span class="input-group-text col-2">Пароль</span>
						<input id="pswd" name="pswd" type="password" class="form-control" placeholder="Введіть пароль" value="%s" required>
						<div class="valid-feedback">Коректно.</div>
						<div class="invalid-feedback">Будь ласка, заповніть пароль.</div>
					</div>
					
					<div class="input-group">
						<span class="input-group-text col-2">Пароль</span>
						<input id="pswd_double" name="pswd_double" type="password" class="form-control"  placeholder="Підтвердити пароль" value="%s" required>
						<div class="valid-feedback">Коректно.</div>
						<div class="invalid-feedback">Будь ласка, заповніть пароль.</div>
					</div>
					<div class="input-group">
						<span class="input-group-text col-2">E-mail</span>
						<input name="email" id="email" type="email" class="form-control" placeholder="Введіть адресу електронної скриньки" value="%s" required>
						<div class="valid-feedback">Коректно.</div>
						<div class="invalid-feedback">
							Будь ласка, заповніть адресу електронної скриньки.
						</div>
					</div>
					<!--<div class="input-group">
						<span class="input-group-text">Код ІПН</span>
						<input name="ipnKey" id="ipnKey" type="text" class="form-control" placeholder="Введіть код ІПН" value="%s" required>
						<div class="valid-feedback">Коректно.</div>
						<div class="invalid-feedback">Будь ласка, заповніть код ІПН.</div>
					</div>-->
					<div class="col-12 text-center">
							
							<button type="submit" name="updateBtn" id="updateBtn" class="btn btn-primary  w-50">Підтвердити зміну паролю та/або E-mail</button>
							<div class="text-center d-block ">
								<a class="btn btn-primary w-50 m-2" name="add_contract" href="\contracts_manager.php">Додати новий контракт</a> 
							</div>
					</div>							
				</form>
			<!--</div>-->
			', 
			'',
			$_SESSION['password'],
			$_SESSION['password'],
			$_SESSION['mail'],
			''
			);
			echo '<form name="contractsForm" action="" method="POST">';
			showClientEditableContracts();
			echo '</form>';
			echo(FOOTER);
		}
	} else {
		header("Location: \signIn.php"); 
		exit();
	}
?>
