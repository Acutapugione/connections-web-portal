<?php
	
	require_once "functions.php"; 
	
	if(!isset($_SESSION['mail']) or !isset($_SESSION['password'])){
		header("Location: \signIn.php"); 
		exit();
	}
	if(isset($_POST['sendAppealBtn']) 
		and !empty($_POST['appeal_order_id']) 
		and !empty($_POST['appeal_type_id']) 
		and !empty($_POST['appeal_text'])
	){
		pushAppeal($_POST);
		
	}
	refreshAppeals();
	echo(HEAD_MESSAGER);
	echo('<div class="container col-lg-7 col-md-9 col-sm-9 col-xs-10 col-12"> 
			<form name="Appeals" action="" class="was-validated" method="POST"> 
				<div class="input-group text-center">');
				
	echo '<div class="input-group mt-2">';
	showAppealTypes();

	echo '</div><div class="input-group mt-2">';
	showAppealOrders();

	echo('</div><div class="input-group mt-2">		
			<span class="input-group-text col-md-3 col-sm-3 col-xs-12 col-12">Текст звернення</span>
			<textarea name="appeal_text" id="appeal_text" type="text" class="form-control" placeholder="Введіть текст звернення" required></textarea>
			<span class="valid-feedback">Коректно.</span>
			<span class="invalid-feedback">
				Будь ласка, заповніть текст звернення.
			</span>
		</div></div>');

	echo ('<div class="text-center d-block ">
			<button type="submit" name="sendAppealBtn" id="sendAppealBtn" class="btn btn-primary w-50 m-2">
				Відправити
			</button>
			</div>');
			
	echo '</form>';
	showAppeals();
	echo '</div>';
	echo(FOOTER);

?>