<?php
	require_once "functions.php"; 
	
	echo(HEAD_SIGN_UP);
	if(!empty($_POST)){

		if(  isset($_POST['pswd'])
			&& isset($_POST['pswd_double'])
			&& isset($_POST['email'])
			#&& isset($_POST['uname']) 
			#&& isset($_POST['ipnKey'])
			&& isset($_POST['signUpBtn']) 
			){

			#$_SESSION['login'] = $_POST['uname'];
			$_SESSION['password'] = $_POST['pswd'];
			$_SESSION['mail'] = $_POST['email'];
			#$_SESSION['ipn'] = $_POST['ipnKey'];
			
			if ( $_POST['pswd'] === $_POST['pswd_double']){
				$params = [
					#'login' => $_POST['uname'],
					'password' => $_POST['pswd'],
					'mail' => $_POST['email'],
					#'ipn_key' => $_POST['ipnKey'],
				];
				
				if ( pushClient($params) ){
					header("Location: \index.php"); 
					exit();
					/* echo '<div class="alert alert-success alert-dismissible fw-5 fade show">
						<strong>Чудово!</strong> Ви зареєстровані.
						<a href="\index.php" type="button" class="btn btn-success">Повернутися до головної</a>
						<a href="\signIn.php" type="button" class="btn btn-success">Виконати вхід</a>
					  </div>'; */
					
				} else {
					echo '<div class="alert alert-danger alert-dismissible fw-5 fade show">
						<a href="\signUp.php" type="button" class="btn btn-danger">Ще раз</a>
						<strong>Увага!</strong> Не вдалося зареєструватися, спробуйте ще раз.
					  </div>';
				}
			} else {
				echo '<div class="alert alert-danger alert-dismissible fw-5 fade show">
						<a href="\signUp.php" type="button" class="btn btn-danger">Ще раз</a>
						<strong>Увага!</strong> Не вірно вказано паролі або вони не співпадають, спробуйте ще раз.
					  </div>';
				#unset($_SESSION['login']);
				unset($_SESSION['password']);
				unset($_SESSION['mail']);
				#unset($_SESSION['ipn']);
				unset($_POST);
			}
			
			header("Location: \index.php"); 
			exit();
		} else {
			#unset($_SESSION['login']);
			unset($_SESSION['password']);
			unset($_SESSION['mail']);
			#unset($_SESSION['ipn']);
		}
	} else {
	
	echo ('
		<div class="container col-lg-7 col-md-9 col-sm-9 col-xs-10 col-12">
			<form id="signUpForm" name="signUpForm" action="\signUp.php" class="was-validated" method="POST">
				<div class="input-group">
					<!--<div class="input-group">
						<span class="input-group-text col-md-3 col-sm-3 col-xs-12 col-12">Логін</span>
						<input type="text" class="form-control" id="uname" placeholder="Введіть логін" name="uname" required>
						<span class="valid-feedback ">Коректно.</span>
						<span class="invalid-feedback">Будь ласка, заповніть логін.</span>
					</div>-->
					
					<div class="input-group">		
						<span class="input-group-text col-md-3 col-sm-3 col-xs-12 col-12">E-mail</span>
						<input name="email" id="email" type="email" class="form-control" placeholder="Введіть адресу електронної скриньки" required>
						<span class="valid-feedback">Коректно.</span>
						<span class="invalid-feedback">
							Будь ласка, заповніть дійсну адресу електронної скриньки, так як на неї будуть надсилатися відповіді.
						</span>
					</div>
					
					<div class="input-group">
						<span class="input-group-text col-md-3 col-sm-3 col-xs-12 col-12">Пароль</span>
						<input type="password" class="form-control" id="pswd" placeholder="Введіть пароль" name="pswd" required>
						<span class="valid-feedback">Коректно.</span>
						<span class="invalid-feedback">Будь ласка, заповніть пароль.</span>
					</div>
					
					<div class="input-group">
						<span class="input-group-text col-md-3 col-sm-3 col-xs-12 col-12">Пароль</span>
						<input type="password" class="form-control" id="pswd_double" placeholder="Підтвердити пароль" name="pswd_double" required>
						<span class="valid-feedback">Коректно.</span>
						<span class="invalid-feedback">Будь ласка, підтвердіть пароль.</span>
					</div>

					<!--<div class="input-group">
						<span class="input-group-text col-md-3 col-sm-3 col-xs-12 col-12">Код ІПН</span>
						<input name="ipnKey" id="ipnKey" type="text" class="form-control" placeholder="Введіть код ІПН" required>
						<span class="valid-feedback">Коректно.</span>
						<span class="invalid-feedback">Будь ласка, заповніть код ІПН.</span>
					</div>-->
					
					<div class="col-12 text-center">
						<button type="submit" name="signUpBtn" id="signUpBtn" class="btn btn-primary w-50">Реєстрація</button>
						<div class="text-center d-block d-lg-none">
							<a href="\signIn.php" type="button" class="btn btn-primary w-50 m-2">Вже зареєстровані?</a>
						</div>
					</div>	
				</div>
			</form>
		 </div>
		');
		
	echo(FOOTER);
	}

	
?>