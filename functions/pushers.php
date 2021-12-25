<?php
		
	function postAppealTo1C($appeal, $adress=DOCS_1C_POST_APPEAL){
		
		if( $curl = curl_init() ) {
			curl_setopt($curl, CURLOPT_URL, $adress);
			curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'charset:utf-8']);
			curl_setopt($curl, CURLOPT_HEADER, 0);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($appeal));
			
			$out = curl_exec($curl);
			if (curl_errno($curl)) {
				if($adress === DOCS_1C_POST_APPEAL){
					curl_close($curl);	
					return false;
				}
				curl_close($curl);
				return getAppealFrom1C($appeal, DOCS_1C_POST_APPEAL);
			}
			
			curl_close($curl);	
			return $out;
		} 
	}
	
	function pushClient($info){
		if(checkClient($info['mail'], $info['password'])){
			header(sprintf("Location: \signIn.php?POST&pswd=%s&email=%s&signInBtn", $info['password'], $info['mail'])); 
			return true;
		}
		$tmp = insertClient($info);
		return $tmp;
	}
	
	function pushAppeal($appeal=null){

		if(!empty($appeal)){
			insertAppeal($appeal);
			$tmp = selectAppealsInfo($appeal['appeal_type_id'], $appeal['appeal_order_id']);

			foreach($tmp as $rec){
				if(!getAppealFrom1C($rec['nomer'])){
					$n_appeal = array(
						'client_name' => getOrdersFrom1C($rec['client_ipn'])[0]['client_name'],
						'client_ipn' => $rec['client_ipn'],
						'adres_object' => $rec['adres_object'],
						'client_email' => $rec['client_email'],
						
						'nomer' => $rec['nomer'],
						'data_reg' => $rec['data_reg'],
						'tip' => $rec['tip'],
						'text' => $rec['text'],
					);

					$result = postAppealTo1C($n_appeal);
				}
				refreshAppeals();
			}
		}
	}
	
	function pushContract($ipn){
		$tmp = insertContract($ipn, getClientId($_SESSION['mail'], $_SESSION['password']));
		if(isset($tmp) && !empty($tmp)){
			refreshOrders();
		}
		return $tmp;
	}
	
	function pushOrder($order){
		return createOrder($order);
	}
	
	function pushStepType($step_type_name, $key_1c=''){
		$tmp = selectStepTypeId($step_type_name);
		if($tmp){
			return updateStepType($tmp, $step_type_name, $key_1c);
		} else {
			return insertStepType($step_type_name, $key_1c);
		}
	}

	function pushSteps(array $steps, $order, $log_str){
		foreach($steps as $step){
			$step['order_id'] = $order['order_id'];
			if( isset($step['step_type_name']) 
				&& isset($order['order_number']) )
			{
				if(isset($step['sostoyaniye'])){
					$step['step_id']= selectOrgStepId(
						$step['step_type_name'],
						$order['order_number'],
						$order_id = $step['order_id']
					);
				} else {
					$step['step_id']= selectStepId(
						$step['step_type_name'],
						$order['order_number'],
						$order_id = $step['order_id']
					);
				}
			}
			if(isset($step['order_id'])){
				if( isset($step['step_id'])
					&& $step['step_id']!=false
					&& updateStep($step) ){
						$log_str = sprintf(
							'%s<ul><div><strong>step %s updated</strong></div>',
							$log_str, 
							$step['step_id']
						);
				} elseif( insertStep($step) ){
					if(isset($step['sostoyaniye'])){
						$step['step_id']= selectOrgStepId(
							$step['step_type_name'],
							$order['order_number'],
							$order_id = $step['order_id']
						);
					} else {
						$step['step_id']= selectStepId(
							$step['step_type_name'],
							$order['order_number'],
							$order_id = $step['order_id']
						);
					}
					$log_str= sprintf(
						'%s<ul><div><strong>step %s inserted</strong><br></div>',
						$log_str, 
						$step['step_id']
					);
				} else {
					$log_str= sprintf(
						'%s<ul><div><strong>error  in pushSteps() 92</strong><br></div>',
						$log_str
					);
				}
				foreach($step as $key => $val){
					if(strtotime($val)===false){
						$log_str= sprintf(
							'%s<li><strong>%s</strong> : %s</li>', 
							$log_str, 
							$key,
							$val
						);
					}else{
						$log_str= sprintf(
							'%s<li><strong>%s</strong> : %s</li>', 
							$log_str, 
							$key,
							$val
						);
					}
				}
			}
			$log_str=sprintf( '%s</ul>', $log_str);
		}
		return $log_str;
	}
	
?>