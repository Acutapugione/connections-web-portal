<?php
	require_once "functions.php"; 
	
	if(!empty($_POST)){
		if(isset($_POST['delete'])){
			removeContract($_POST['delete']);
			header("Location: \contracts_manager.php"); 
			exit();
		}
		if(isset($_POST['pushContract'])){

			if(isset($_SESSION['mail'])){

				pushContract($_POST['ipn']);
				header("Location: \index.php"); 
				exit();
			} else {
				echo(HEAD);
				echo '<div class="alert alert-danger alert-dismissible fw-5 fade show">
						<a href="\index.php" type="button" class="btn btn-danger">Виконати вхід</a>
						<strong>Увага!</strong> Виконайте вхід та спробуйте ще раз.
					  </div>';
				
			}
		}
	} else {
		
		echo(HEAD);
		
		unset($_POST);
		
		echo ('
		<div class="container col-lg-7 col-md-9 col-sm-9 col-xs-10 col-12">
				<form name="pushContractForm" action="" class="was-validated" method="POST">
					<div class="input-group text-center">		
						
						<div class="input-group">
							<span class="input-group-text col-md-3 col-sm-3 col-xs-12 col-12">ІПН</span>
							<input type="text" class="form-control" id="ipn" placeholder="Введіть ІПН" name="ipn" required>
							<div class="valid-feedback">Коректно.</div>
							<div class="invalid-feedback">Будь ласка, заповніть ІПН.</div>
						</div>
						
						<div class="col-12 text-center">
							<button type="submit" name="pushContract" id="pushContract" class="btn btn-primary w-50">Створити контракт</button>
							<div class="text-center d-block">
								<a href="\settings.php" type="button" class="btn btn-primary w-50 m-2">Повернутися?</a>
							</div>
						</div>	
						
					</div>
				</form>
				');
		echo '<form name="contractsForm" action="" method="POST">';
		showClientEditableContracts();
		echo '</form></div>';
		echo(FOOTER);
		
	}
?>