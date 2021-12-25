<?php

	function updateRecord($filter){
		$setText = '';
		$i = 0;
		foreach($filter["coms"] as $key => $val){
			if($i===count($filter["coms"])-1){
				$setText.=sprintf("%s='%s' ", $key, $val);
            } else {
                $setText.=sprintf("%s='%s', ", $key, $val);
			}
            $i++;
		}		
        	
		$query = sprintf(
			"UPDATE %s SET %s WHERE %s = '%s' ",
			$filter['tableName'],
			$setText, 
			$filter['key'], 
			$filter['val']
		);

        $rezult = mysqli_query($GLOBALS['CONNECTION'], $query);
        if(!$rezult ){
            var_dump(mysqli_error($GLOBALS['CONNECTION']));
			echo '<br>on updateRecord<br>';
			
			return false;
        }
		return true;
    }
	function updateAppealStatus($appeal_id, $status){
		$filter = [
			'tableName' => 'conn_appeals', 
			'coms' => [
				'conn_appeals.status' => $status,
				],
			'key' => 'id',
			'val' => $appeal_id,
		];
		return updateRecord($filter);
	}
	function updateOrder($order){
		$filter = [
			'tableName' => 'conn_orders', 
			'coms' => [
				'conn_orders.address' => $order['adres_object'],
				'conn_orders.conn_type_name' => $order['tip_priedn'],
				'conn_orders.project_executor' => $order['ispoln_proekta_narugn'],
				'conn_orders.planned_capacity' => $order['plan_moshchn'],
				'conn_orders.technical_condition' => $order['tu'],
				],
			'key' => 'id',
			'val' => $order['order_id'],
			];
		if(isset($order['contract_id']) ){
			$filter['coms']['conn_orders.contract_id']=$order['contract_id'];
		} 
		return updateRecord($filter);
	}
	function updateStep($step){
		if(empty($step['step_id'])){
			return false;
		}
		$ctype = pushStepType($step['step_type_name'], $step['step_type_key1C']);
		if(isset($step['sostoyaniye'])){
			$filter = [
				'tableName' => 'conn_org_steps', 
				'coms' => [
					'conn_org_steps.executor' => $step['ispolnitel'],
					'conn_org_steps.commentary' => $step['coment'],
					'conn_org_steps.sustain' => $step['sostoyaniye'],
					],
				'key' => 'id',
				'val' => $step['step_id'],
				];
			if(strtotime($step['data_nachala'])===false){
				$filter['coms']['conn_org_steps.start_at'] = null;
			} else {
				$filter['coms']['conn_org_steps.start_at'] = date("Y-m-d H:i:s", strtotime($step['data_nachala']));
			}	
			if(strtotime($step['srok'])===false){
				$filter['coms']['conn_org_steps.deadline_at'] = null;
			} else {
				$filter['coms']['conn_org_steps.deadline_at'] = date("Y-m-d H:i:s", strtotime($step['srok']));
			}	
			if(strtotime($step['data_zaversheniya'])===false){
				$filter['coms']['conn_org_steps.done_at'] = null;
			} else {
				$filter['coms']['conn_org_steps.done_at'] = date("Y-m-d H:i:s", strtotime($step['data_zaversheniya']));
			}	
			if(isset($step['deleted'])){
				$filter['coms']['conn_org_steps.deleted_at'] = date("Y-m-d H:i:s") ;
			} else {
				$filter['coms']['conn_org_steps.deleted_at'] = null;
			}
		} else {
			$filter = [
				'tableName' => 'conn_steps', 
				'coms' => [
					'conn_steps.price' => $step['price'],
					'conn_steps.n_dogovor' => $step['n_dogovor'],
					],
				'key' => 'id',
				'val' => $step['step_id'],
				];
				
			if(strtotime($step['payed_at'])===false){
				$filter['coms']['conn_steps.payed_at'] = null;
			} else {
				$filter['coms']['conn_steps.payed_at'] = date("Y-m-d H:i:s", strtotime($step['payed_at']));
			}
			if(strtotime($step['completed_at'])===false){
				$filter['coms']['conn_steps.completed_at'] = null;
			} else {
				$filter['coms']['conn_steps.completed_at'] = date("Y-m-d H:i:s", strtotime($step['completed_at']));
			}
			if(strtotime($step['created_at'])===false){
				$filter['coms']['conn_steps.created_at'] = null;
			} else {
				$filter['coms']['conn_steps.created_at'] = date("Y-m-d H:i:s", strtotime($step['created_at']));
			}
			if(isset($step['deleted'])){
				$filter['coms']['conn_steps.deleted_at'] = date("Y-m-d H:i:s");
			} else {
				$filter['coms']['conn_steps.deleted_at'] = null;
			}
			if(isset($step['nalog'])){
				$filter['coms']['conn_steps.nalog'] = $step['nalog'];
			} else {
				$filter['coms']['conn_steps.nalog']	= 0 ;
			}
		}
		return updateRecord($filter);
	}
	function updateClientInfo($info){
		$filter = [
			'tableName' => 'conn_clients', 
			'coms' => [
				'conn_clients.password' => $info['pswd'],
				'conn_clients.mail' => $info['email'],
				],
			'key' => 'id',
			'val' => $info['client_id']
			];
		return updateRecord($filter);
	}
	function updateStepType($id, $step_type_name, $key_1c=''){
		$filter = array(
			'tableName' => 'conn_step_types', 
			'coms' => [
				'conn_step_types.name' => $step_type_name,
				'conn_step_types.key_1c' => $key_1c,
				],
			'key' => 'id',
			'val' => $id,
		);
		return updateRecord($filter);
	}
	
?>