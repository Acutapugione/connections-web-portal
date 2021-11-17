<?php
	require_once "functions.php"; 
	echo(HEAD_SIGN_IN);
	if(!empty($_POST)){
		if( isset($_POST['uname']) && isset($_POST['pswd']) && isset($_POST['signInBtn'])){
			$_SESSION['login'] = $_POST['uname'];
			$_SESSION['password'] = $_POST['pswd'];
			 if (!checkClient($_SESSION['login'], $_SESSION['password'])){
				var_dump($_POST);
				$error_text = 'Перевірте будь-ласка логін та пароль, та введіть правильний або зареєструйтеся';
				#echo(HEAD_SIGN_IN);
				resetCurrSession();
				echo '<div class="alert alert-danger alert-dismissible fw-5 fade show ">
						<strong>Увага!</strong> Не вірно вказано логін або пароль
						<a href="\signIn.php" type="button" class="btn btn-danger">спробуйте ще раз</a>
						або <a href="\signUp.php" type="button" class="btn btn-danger">зареєструйтеся</a>
					  </div>';
				#header("Location: \signIn.php"); 
				exit();
			} else {
				header("Location: \index.php"); 
				exit();
			}
		} else {
			unset($_SESSION['login']);
			unset($_SESSION['password']);
		}
	} else {
		
		unset($_POST);
		
		echo ('
		<div class="container">
				<form name="signInForm" action="\signIn.php" class="was-validated" method="POST">
					<div class="input-group">		
						<div class="input-group">
							<span class="input-group-text">Логін</span>
							<input type="text" class="form-control" id="uname" placeholder="Введіть логін" name="uname" required>
							<div class="valid-feedback">Коректно.</div>
							<div class="invalid-feedback">Будь ласка, заповніть логін.</div>
						</div>
						<div class="input-group">
							<span class="input-group-text">Пароль</span>
							<input type="password" class="form-control" id="pswd" placeholder="Введіть пароль" name="pswd" required>
							<div class="valid-feedback">Коректно.</div>
							<div class="invalid-feedback">Будь ласка, заповніть пароль.</div>
						</div>
						<button type="submit" name="signInBtn" id="signInBtn" class="btn btn-primary">Увійти</button>
					</div>
				</form>
			</div>');
		echo(FOOTER);
	} 
	
	
?>