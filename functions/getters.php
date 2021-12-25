<?php

	function getAppealFrom1C($index, $adress=DOCS_1C_GET_APPEAL){
		$request = sprintf('%s?nomer=%s', $adress, $index);
		if( $curl = curl_init() ) {
			curl_setopt($curl, CURLOPT_URL, $request);
			curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'charset:utf-8']);
			curl_setopt($curl, CURLOPT_HEADER, 0);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			$out = curl_exec($curl);
			if (curl_errno($curl)) {
				if($adress === DOCS_1C_GET_APPEAL){
					curl_close($curl);	
					return false;
				}
				curl_close($curl);
				return getAppealFrom1C($client_ipn, DOCS_1C_GET_APPEAL);
			}
			$jsoned = json_decode($out, true, 512 , JSON_OBJECT_AS_ARRAY);
			curl_close($curl);	
			return $jsoned;
		} 
	}
	
	function getOrdersFrom1C($client_ipn, $adress=LOCAL_1C){
		$request = sprintf("%s?client_ipn=%s", $adress, $client_ipn);
		if( $curl = curl_init() ) {
			curl_setopt($curl, CURLOPT_URL, $request);
			curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'charset:utf-8']);
			curl_setopt($curl, CURLOPT_HEADER, 0);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			$out = curl_exec($curl);
			if (curl_errno($curl)) {
				if($adress === OUTER_1C){
					curl_close($curl);	
					return false;
				}
				curl_close($curl);
				return getOrdersFrom1C($client_ipn, OUTER_1C);
			}
			$jsoned = json_decode($out, true, 512 , JSON_OBJECT_AS_ARRAY);
			curl_close($curl);	
			return $jsoned;
		} 
	}
	
	function getSteps($order_id){
		return selectOrderSteps($order_id);
	}
	
	function getOrgSteps($order_id){
		return selectOrderOrgSteps($order_id);
	}
	
	function getClients(){
		if(isset($_SESSION['is_admin'])){
			return selectClients();
		}
	}
	
	function getAppealType($type_id){
		$tmp = selectAppealTypes();
		foreach($tmp as $rec){
			if($rec['id'] === $type_id)
				return $rec['name'];
		}
	}
	
	function getAppealTypes(){
		return selectAppealTypes();
	}
	
	function getAppeals($type_id, $order_id){
		return selectAppeals($type_id, $order_id);
	}
	
	function getClientMessageTypes($client=null){
		return selectClientMessageTypes($client);
	}
	
	function getClientDialogs($client=null){
		return selectClientDialogs($client);
	}
	
	function getClientContracts($client_id=null){
		return selectClientContracts($client_id);
	}
	
	function getContract($ipn, $client_id){
		return selectContract($ipn, $client_id)[0]['id'];		
	}
		
	function getClientMessagesByType($client_id=null, $type_name=null){
		return selectClientMessagesByType($client_id, $type_name);
	}
	
	function getUnreadMessagesForClient($client_id){
		return selectUnreadMessagesForClient($client_id);
	}
	
	function getMessageTypes(){
		return selectMessageTypes();
	}
	
	function getCountUnreadMessagesToClient($client_mail=null, $client_id=null){
		if(!empty($client_id)){
			$tmp = getUnreadMessagesForClient($client_id);
		} elseif(!empty($client_mail)) {
			$client_id = getClientIdByMail($client_mail);
			if(!empty($client_id)){
				$tmp = getUnreadMessagesForClient($client_id);
			}
		}
		if(isset($tmp) && count($tmp)>0){
			return count($tmp);
		}
	}

	function getOrderInfo($order_id=null){
		return selectOrderInfo($order_id);
	}
	
    function getOrderSteps($order_id=null){
        return selectOrderStepsTemplate($order_id);
    }
	
	function getOrderOrgSteps($order_id=null){
        return selectOrderOrgStepsTemplate($order_id);
    }
	
	function getClientId($mail, $password){
		$res = selectClientId($mail, $password);
		if(is_array($res) && !empty($res)){
			return $res[0]['id'];
		} 
	}
	
	function getClientIdByMail($mail){
		$res = selectClient($mail);
		if(is_array($res) && !empty($res)){
			return $res[0]['id'];
		} 
	}
	
	function getStepStatusIcon($elem, $classArr){
		$statusIcon = '';
		$tmp = '';
		
		foreach($elem as $key => $val){

			if(date_create($val)){
				
				if( $key == 'Дата оплати' && strtotime($val)==null){
					$tmp = $GLOBALS['ICONS']['unPayed'];
					break;
				} elseif ( $key == 'Дата виконання'  && strtotime($val)==null) {
					$tmp = $GLOBALS['ICONS']['unCompleted'];
					break;
				} elseif ( $key == 'Дата рахунку'  &&  strtotime($val)==null) {
					$tmp = $GLOBALS['ICONS']['unPayed'];
					break;
				} else {
					$tmp = $GLOBALS['ICONS']['success'];
				}
			}
		}
		if(empty($tmp)){
			$tmp = $GLOBALS['ICONS']['success'];
		}
		$statusIcon = sprintf('
			<td class="%s">
				<img class="%s" src="%s" alt="">
			</td>',
			$classArr['td'],
			$classArr['img'],
			$tmp
			);
		return $statusIcon;
	}
	
?>