<?php
	require_once "functions.php"; 

	if(!empty($_POST)){

		if( isset($_POST['uname']) 
			&& isset($_POST['pswd'])
			&& isset($_POST['pswd_double'])
			&& isset($_POST['email'])
			&& isset($_POST['ipnKey']) && isset($_POST['signUpBtn']) ){

			$_SESSION['login'] = $_POST['uname'];
			$_SESSION['password'] = $_POST['pswd'];
			$_SESSION['mail'] = $_POST['email'];
			$_SESSION['ipn'] = $_POST['ipnKey'];
			
			if ( $_POST['pswd'] === $_POST['pswd_double']){
				$params = [
					'login' => $_POST['uname'],
					'password' => $_POST['pswd'],
					'mail' => $_POST['email'],
					'ipn_key' => $_POST['ipnKey'],
				];
				if ( pushClient($params) ){
					echo '<div class="alert alert-success alert-dismissible fw-5 fade show text-dark">
						<strong>Чудово!</strong> Ви зареєстровані.
						<a href="\index.php" type="button" class="btn btn-success">Повернутися до головної</a>
						<a href="\signIn.php" type="button" class="btn btn-success">Виконати вхід</a>
					  </div>';
				} else {
					echo '<div class="alert alert-danger alert-dismissible fw-5 fade show text-danger">
						<a href="\signUp.php" type="button" class="btn btn-danger">Ще раз</a>
						<strong>Увага!</strong> Не вдалося зареєструватися, спробуйте ще раз.
					  </div>';
				}
			}
			unset($_POST);
			#header("Location: \index.php"); 
			#exit();
		} else {
			unset($_SESSION['login']);
			unset($_SESSION['password']);
			unset($_SESSION['mail']);
			unset($_SESSION['ipn']);
		}
	} else {
	echo(HEAD_SIGN_UP);
	echo ('
		<div class="container">
			<form id="signUpForm" name="signUpForm" action="\signUp.php" class="was-validated" method="POST">
				<div class="input-group">
					<div class="input-group">
						<span class="input-group-text">Логін</span>
						<input type="text" class="form-control" id="uname" placeholder="Введіть логін" name="uname" required>
						<span class="valid-feedback ">Коректно.</span>
						<span class="invalid-feedback">Будь ласка, заповніть логін.</span>
					</div>
					<div class="input-group">
						<span class="input-group-text">Пароль</span>
						<input type="password" class="form-control" id="pswd" placeholder="Введіть пароль" name="pswd" required>
						<span class="valid-feedback">Коректно.</span>
						<span class="invalid-feedback">Будь ласка, заповніть пароль.</span>
					</div>
					<div class="input-group">
						<span class="input-group-text">Пароль</span>
						<input type="password" class="form-control" id="pswd_double" placeholder="Підтвердити пароль" name="pswd_double" required>
						<span class="valid-feedback">Коректно.</span>
						<span class="invalid-feedback">Будь ласка, підтвердіть пароль.</span>
					</div>
					<div class="input-group">
											
						<span class="input-group-text">E-mail</span>
						<input name="email" id="email" type="email" class="form-control" placeholder="Введіть адресу електронної скриньки" required>
						<span class="valid-feedback">Коректно.</span>
						<span class="invalid-feedback">
							Будь ласка, заповніть адресу електронної скриньки.
						</span>
					</div>
					<div class="input-group">
						<span class="input-group-text">Код ІПН</span>
						<input name="ipnKey" id="ipnKey" type="text" class="form-control" placeholder="Введіть код ІПН" required>
						<span class="valid-feedback">Коректно.</span>
						<span class="invalid-feedback">Будь ласка, заповніть код ІПН.</span>
					</div>
					
					<button type="submit" name="signUpBtn" id="signUpBtn" class="btn btn-primary">Реєстрація</button>
				</div>
			</form>
		 </div>
		');
		
	echo(FOOTER);
	}

	
?>