<?php
	require_once "functions.php"; 
	echo(HEAD_SIGN_IN);
	#var_dump($_POST);
	if(!empty($_POST)){
		#var_dump($_POST);
		if( 	
			isset($_POST['pswd']) 
			&& isset($_POST['email'])
			&& isset($_POST['signInBtn'])
			#&& isset($_POST['uname']) 
		){
			#$_SESSION['login'] = $_POST['uname'];
			$_SESSION['password'] = $_POST['pswd'];
			$_SESSION['mail'] = $_POST['email'];
			 if (!checkClient($_SESSION['mail'], $_SESSION['password'])){
				#var_dump($_POST);
				#$error_text = 'Перевірте будь-ласка логін та пароль, та введіть правильний або зареєструйтеся';
				#echo(HEAD_SIGN_IN);
				resetCurrSession();
				echo '<div class="alert alert-danger alert-dismissible fw-5 fade show ">
						<strong>Увага!</strong> Не вірно вказано E-mail або пароль
						<a href="\signIn.php" type="button" class="btn btn-danger">спробуйте ще раз</a>
						або <a href="\signUp.php" type="button" class="btn btn-danger">зареєструйтеся</a>
					  </div>';
				#header("Location: \signIn.php"); 
				#exit();
			} else {
				
				header("Location: \index.php"); 
				exit();
			}
		} else {
			unset($_SESSION['mail']);
			unset($_SESSION['password']);
		}
	} else {
		
		unset($_POST);
		
		echo ('
		<div class="container col-lg-7 col-md-9 col-sm-9 col-xs-10 col-12">
				<form name="signInForm" action="\signIn.php" class="was-validated" method="POST">
					<div class="input-group text-center">		
						<!--<div class="input-group">
							<span class="input-group-text col-md-3 col-sm-3 col-xs-12 col-12">Логін</span>
							<input type="text" class="form-control" id="uname" placeholder="Введіть логін" name="uname" required>
							<div class="valid-feedback">Коректно.</div>
							<div class="invalid-feedback">Будь ласка, заповніть логін.</div>
						</div>-->
						
						<div class="input-group">		
							<span class="input-group-text col-md-3 col-sm-3 col-xs-12 col-12">E-mail</span>
							<input name="email" id="email" type="email" class="form-control" placeholder="Введіть адресу електронної скриньки" required>
							<span class="valid-feedback">Коректно.</span>
							<span class="invalid-feedback">
								Будь ласка, заповніть адресу електронної скриньки.
							</span>
						</div>
						
						<div class="input-group">
							<span class="input-group-text col-md-3 col-sm-3 col-xs-12 col-12">Пароль</span>
							<input type="password" class="form-control" id="pswd" placeholder="Введіть пароль" name="pswd" required>
							<div class="valid-feedback">Коректно.</div>
							<div class="invalid-feedback">Будь ласка, заповніть пароль.</div>
						</div>
						
						<div class="col-12 text-center">
							<button type="submit" name="signInBtn" id="signInBtn" class="btn btn-primary w-50">Увійти</button>
							<div class="text-center d-block d-lg-none">
								<a href="\signUp.php" type="button" class="btn btn-primary w-50 m-2">Ще не реєструвалися?</a>
							</div>
						</div>	
						
					</div>
				</form>
			</div>');
		echo(FOOTER);
	} 
	
	
?>