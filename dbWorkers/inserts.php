<?php

	
	
    function insertNewRecord($tableName , $params){
        $fields = '';
        $values = '';
        $i = 0;
        foreach ($params as $key => $value) {
			if(!is_array($value)){
            if($i===count($params)-1){
                $fields.= $key;  
                $values.= '"'.$value.'"';
            } else {
                $fields.= $key.', ';
                $values.= '"'.$value.'", ';
            }
			}
            $i++;    
        }
        $query = sprintf("INSERT INTO %s(%s) VALUES (%s)", $tableName, $fields, $values);
       
		
        $rezult = mysqli_query($GLOBALS['CONNECTION'], $query);
        if(!$rezult ){
			var_dump($query);
			echo '<br>';
            var_dump(mysqli_error($GLOBALS['CONNECTION']));
			return false;
        }
		return true;
    }
	function insertAppeal($appeal){
		$params = array(
			'order_id' => $appeal['appeal_order_id'],
			'appeal_type_id'=>$appeal['appeal_type_id'],
			'text'=>$appeal['appeal_text'],
		);
		return insertNewRecord('conn_appeals', $params);
	}
	function insertClient($info){
		$params = [
			#'login' => $info['login'],
			'password' => $info['password'],
			'mail' => $info['mail'],
			#'ipn_key' => $info['ipn_key'],
			];
		
		
		return insertNewRecord('conn_clients', $params);
	}
	function insertContract($ipn, $client_id){
		$tmp = getContract($ipn, $client_id);
		if(!empty($tmp )){
			return false;
		}
		$params = [
			'ipn_key' => $ipn,
			'client_id' => $client_id,
		];
		return insertNewRecord('conn_contracts', $params );
	}
	
	function insertMessage($params){
		return insertNewRecord('conn_messages', $params);
	}

	function insertOrder(array $order){
		if(
			is_array($order) 
			&& !empty($order)
			&& isset($order['order_number'])
			&& isset($order['contract_id'])
			)
			
		{
			$params = [
				'order_number' => $order['order_number'], 
				'contract_id' => $order['contract_id'], 
				'address' => $order['adres_object'],
				'conn_type_name' => $order['tip_priedn'],
				'project_executor' => $order['ispoln_proekta_narugn'],
				'planned_capacity' => $order['plan_moshchn'],
				'technical_condition' => $order['tu'],
			];
			return insertNewRecord('conn_orders', $params);
		}		
	}
	
	function insertStep($step){
		#var_dump($step);
		$ctype = pushStepType($step['step_type_name'], $step['step_type_key1C']);
		$tmp = selectStepTypeId($step['step_type_name']);
		if(!empty($tmp)){
			if(isset($step['sostoyaniye'])){//org_steps
				$tableName = 'conn_org_steps';
				$params = [
					'step_type_id' 	=> selectStepTypeId($step['step_type_name']),
					'order_id' 		=> $step['order_id'],
					'executor' 		=> $step['ispolnitel'],
					#'start_at' 	=> $step['data_nachala'],
					#'deadline_at' 	=> $step['srok'],
					#'done_at' 		=> $step['data_zaversheniya'],
					'commentary' 	=> $step['coment'],
					'sustain' 		=> $step['sostoyaniye'],
					];
				if(strtotime($step['data_nachala'])===false){
					$params['start_at'] = null;
				} else {
					$params['start_at'] = date("Y-m-d H:i:s", strtotime($step['data_nachala']));
				}	
				if(strtotime($step['srok'])===false){
					$params['deadline_at'] = null;
				} else {
					$params['deadline_at'] = date("Y-m-d H:i:s", strtotime($step['srok']));
				}	
				if(strtotime($step['data_zaversheniya'])===false){
					$params['done_at'] = null;
				} else {
					$params['done_at'] = date("Y-m-d H:i:s", strtotime($step['data_zaversheniya']));
				}	
			} else {
				
				$tableName = 'conn_steps';
				$params = [
					'step_type_id' 	=> selectStepTypeId($step['step_type_name']),
					'order_id' 		=> $step['order_id'],
					'n_dogovor'     => $step['n_dogovor'],
					#'created_at' 	=> $step['created_at'],
					#'payed_at' 	=> $step['payed_at'], 
					#'completed_at' => $step['completed_at'], 
					'price' 		=> $step['price'], 
					#'nalog' 		=> $step['nalog'], 
					];
					
				if(strtotime($step['payed_at'])===false){
					$params['payed_at'] = null;
				} else {
					$params['payed_at'] = date("Y-m-d H:i:s", strtotime($step['payed_at']));
				}
				if(strtotime($step['completed_at'])===false){
					$params['completed_at'] = null;
				} else {
					$params['completed_at'] = date("Y-m-d H:i:s", strtotime($step['completed_at']));
				}
				if(strtotime($step['created_at'])===false){
					$params['created_at'] = null;
				} else {
					$params['created_at'] = date("Y-m-d H:i:s", strtotime($step['created_at']));
				}
				if(isset($step['nalog'])){
					$params['nalog'] = $step['nalog'];
				} else {
					$params['nalog'] = 0 ;
				}
			}
			return insertNewRecord($tableName, $params);
		}
		
	}
	function insertStepType($step_type_name, $key_1c=''){
		if(!selectStepTypeId($step_type_name)){
			$params = [ 
				'name' => $step_type_name,
				'key_1c' => $key_1c,
				];
			return insertNewRecord('conn_step_types', $params);
		}
		return false;
	}
	function insertClientsStory($client, $context){
	}
	
?>