<?php
	require_once "functions.php"; 
	
	function showMessageTypesControl(){
		$res = getMessageTypes();
		if(isset($res) && count($res)>0){
			$cntr = 0;
			echo ('
			<div class="input-group">
				<span class="input-group-text">Тип повідомлення</span>
				<select class="form-select" name="messageType" id="messageType">
			');

			foreach($res as $item){
				
				$cntr++;
				if($cntr==0){
					echo sprintf('<option value="%s">%s</option>', $item['Код'], $item['Тип повідомлення']);	
				} else {
					echo sprintf('<option value="%s">%s</option>', $item['Код'], $item['Тип повідомлення']);	
				}
			}
			echo ('
			</select>
			<div class="valid-feedback">Коректно.</div>
			<div class="invalid-feedback">Будь ласка, виберіть тип повідомлення.</div>
			</div>
			');
		}	
	}
	if(isset($_SESSION['login']) and isset($_SESSION['password'])){
		echo(HEAD_MESSAGER);
		
		if( (!empty($_POST) && (
		isset($_POST['messageType']) &&
		isset($_POST['messageTheme']) &&
		isset($_POST['messageText']) &&
		isset($_POST['sendMessageBtn'])))){
			$message = [
				'message_type_id' => $_POST['messageType'], 
				'message_tittle' => $_POST['messageTheme'],  
				'message_context' => $_POST['messageText'], 
				'client_id' => getClientId($_SESSION['login'], $_SESSION['password'])
				]; 
			if (pushMessage($message)){
				echo '<div class="alert alert-success alert-dismissible fw-5 fade show text-dark">
						<strong>Чудово!</strong> Ваше повідомлення було успішно відправлено.
						<a href="\index.php" type="button" class="btn btn-success">Повернутися до головної</a>
						<a href="\messager.php" type="button" class="btn btn-success">Ще раз</a>
					  </div>';
			} else {
				echo '<div class="alert alert-danger alert-dismissible fw-5 fade show text-danger">
						<a href="\messager.php" type="button" class="btn btn-danger">Ще раз</a>
						<strong>Увага!</strong> Ваше повідомлення не було відправлено, спробуйте ще раз.
					  </div>';
			}
		} else {
		
			echo ('
			<div class="container">
				<form name="messForm" action="" class="was-validated" method="POST">				
					<div class="input-group">
			');
						showMessageTypesControl();
			echo('	
					<div class="input-group">
						<span class="input-group-text">Тема повідомлення</span>
						<input type="text" class="form-control" id="messageTheme" name="messageTheme" placeholder="Введіть тему повідомлення" required>
						<div class="valid-feedback">Коректно.</div>
						<div class="invalid-feedback">Будь ласка, заповніть тему повідомлення.</div>
					</div>
					<div class="input-group">
						<span class="input-group-text">Текст повідомлення</span>
						<input type="text" class="form-control" id="messageText" name="messageText" placeholder="Введіть текст повідомлення" required>
						<div class="valid-feedback">Коректно.</div>
						<div class="invalid-feedback">Будь ласка, заповніть текст повідомлення.</div>
					</div>
						<button type="submit" name="sendMessageBtn" id="sendMessageBtn" class="btn btn-primary">Надіслати повідомлення</button>
					</div>
					
				</form>
			</div>
			');
			echo(FOOTER);
		}
	} else {
		header("Location: \signIn.php"); 
		exit();
	}
?>