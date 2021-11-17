<?php
	require_once "functions.php"; 
	
	if(!empty($_POST)){
		if(isset($_POST['pushOrder'])){
			#var_dump($_POST);
			pushOrder($_POST);
		}
	} else {
		echo(HEAD);
		
		/*
		$json = file_get_contents('./PushOrders.js');
		$jsoned = json_decode($json, true);
		if(is_array($jsoned)){
			$params = $jsoned;
		} else {*/
			$params = [
				 "pushOrder" 		=> true,
				 "order_number" 	=> "12356",
				 "client_ipn"   	=> "123456789",
				 "steps"			=> [
				 [ 
					"step_type_name"	=> "Видача технічних умов на приєднання",
					"created_at"		=> "01.01.2011",
					"payed_at"			=> "02.01.2011",
					"completed_at"		=> "12.01.2011",
					"price"				=> 525,
					"nalog"				=> 0.18
					],
				[
					"step_type_name"	=> "Приєднання до газорозподільних мереж (стандартне)",
					"created_at"		=> "25.01.2011",
					"payed_at"			=> "16.02.2011",
					"completed_at"		=> "12.05.2011",
					"price"				=> 5115,
					"nalog"				=> 0.18	 
					]
				 ]
			];
		/*}*/
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL,"http://localhost/orders_manager.php");
		curl_setopt($ch,CURLOPT_POST,true);
		curl_setopt($ch,CURLOPT_POSTFIELDS,http_build_query($params));
		$result = curl_exec($ch);
		
	}
	//result will contain the response

	/*
	$ctr = 0;
	$json = file_get_contents('./PushOrders.js');
	$jsoned = json_decode($json, true);
	foreach($jsoned as $key => $val){
		if(is_array($val)){
			
			foreach($val as $item){
				echo '<br>'.$key.' ['.$ctr.'] => <ul class="list">';
				$ctr++;
				foreach($item as $sig => $ma){
					echo '<li class="list-item">'.$sig.'  =>  '.$ma.'</li>';
				}
				echo '</ul>';
			}
			
			
		} else {
			echo $key.'  =>  '.$val.'<br>';
		}
	}
	}*/
	echo(FOOTER);
?>