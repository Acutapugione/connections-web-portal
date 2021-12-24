<?php
	//must have modules
    require_once "config.php";
    require_once "db_worker.php";
	
	//extends modules
	require_once "functions/pdfExecutions.php";
	require_once "functions/getters.php";
	require_once "functions/pushers.php";
	require_once "functions/views.php";
	require_once "functions/str_price.php";
	
	//debugging all
	error_reporting(E_ALL); 
	
	//sessions handlers
	session_start();
	session_encode();
	
	function refreshAppeals(){
		$client_id = getClientId($_SESSION['mail'], $_SESSION['password']);
		$contracts = getClientContracts($client_id);
		$tmp = selectClient_order_id($client_id);
		foreach($tmp as $rec){
			refreshAppeal($rec);

		}
	}
	
	function refreshAppeal($order){
		if(isset($order['id'])){
			
			$appeals = selectAppealsInfo('', $order['id']);
			foreach($appeals as $item){
				$result_1c = getAppealFrom1C($item['nomer']);
				foreach($result_1c as $_obj){
					updateAppealStatus($_obj['nomer'], $_obj['status']);
				}
			}
		}
	}
	function makeInvoice($clientInfo, $contracts, $steps, $filter){//create Invoice{now}.pdf
		
		$pdf = new ADPDF();
		$pdf->AddFont('DejaVu','','DejaVuSansCondensed.ttf',true);
		$pdf->AddFont('DejaVuSans-Bold','','DejaVuSans-Bold.ttf', true);

		$pdf->MakeHeaderInvoice($clientInfo, $contracts, $steps, $filter);
		
		$pdf->MakeBodyInvoice($steps, $filter);
		
		$data = [];
		$pdf->MakeFooterInvoice($data);
		
		$pdf->Output('invoice'.date("Ymd").'.pdf','I');
	}
	
	function removeContract($ipn){
		$client_id = getClientId($_SESSION['mail'], $_SESSION['password']);
		$contract_id = selectContract($ipn, $client_id)[0]['id'];
		if(isset($contract_id )){
			deleteContract($contract_id);
		}
	}
	
	function resetCurrSession(){
		unset($_SESSION);
	}
	
	function refreshClientInfo($info){
		return updateClientInfo($info);
	}
	
	function compareOrders(array $orders){
		foreach($orders as $order){
			$client_id =  getClientId($_SESSION['mail'], $_SESSION['password']);
			
			if(	isset($order['client_ipn'])	){
				$client_ipn = $order['client_ipn'];					
			} elseif (	isset($order['contract_ipn'])	){
				$client_ipn = $order['contract_ipn'];
			}
			if(isset($client_ipn) && isset($client_id)){
				$contract_id = getContract( $client_ipn, $client_id );
				$tmp = selectClientOrdersTemplate($client_id, $contract_id);
				
				foreach($tmp as $tmp_order){
					if(strval($tmp_order['Номер замовлення']) == strval($order['order_number'])){
						//Заказ такой есть
						$order_id = selectOrderId($tmp_order['Номер замовлення'], $client_id , $contract_id);
						
						$steps = getSteps($order_id);
						
						foreach($steps as $step){
							$is_compare = false;
							foreach($order['steps'] as $step_1c){
								
								if(strval($step['step_type_name']) == strval($step_1c['step_type_name'])){
									$is_compare = true;
								}	
							}
							if(!$is_compare){						
								$step['deleted'] = true;
								if(strtotime($step['deleted_at'])==null){
									updateStep($step);
								}
							}
						}
						$org_steps = getOrgSteps($order_id);
						
						foreach($org_steps as $step){
							$is_compare = false;
							foreach($order['steps_org'] as $step_1c){
								
								if(strval($step['step_type_name']) == strval($step_1c['step_type_name'])){
									$is_compare = true;
								}
							}
							if(!$is_compare){
								$step['deleted'] = true;
								if(strtotime($step['deleted_at'])==null){
									updateStep($step);
								}
							}
						}
					}
				}
			}
		}
	}
	
	function createOrder(array $order, $showLog=Null){
		if(
			is_array($order) 
			&& !empty($order)
			&& isset($order['order_number']) 
			&& ( isset($order['client_ipn']) or isset($order['contract_ipn']))		)				
		{
			$log_str='';
			$order['order_id'] = selectOrderId( 
				$order['order_number'], 
				$client_id = getClientId($_SESSION['mail'], $_SESSION['password']) 
				);
			//Установим ИНН клиента
			if(isset($order['client_ipn'])){
				$client_ipn = $order['client_ipn'];					
			} elseif (isset($order['contract_ipn'])){
				$client_ipn = $order['contract_ipn'];
			}
				
			$contract_id = getContract( $client_ipn, getClientId($_SESSION['mail'], $_SESSION['password']) );

			if(isset($contract_id) && !empty($contract_id)){
				$order['contract_id'] = $contract_id;
			} 
				
			//Создаем заказ/Обновляем если есть
			if(isset($order['order_id']) && $order['order_id']>0 && updateOrder($order)){
				$log_str = sprintf('%s<br><strong>order updated</strong><br>', $log_str);
			} elseif (insertOrder($order)){
				$log_str = sprintf('%s<br><strong>order inserted</strong><br>', $log_str);
				$order['order_id'] = selectOrderId($order['order_number'], getClientId($_SESSION['mail'], $_SESSION['password']));
			} else {
				return;
			}

			if(isset($order['order_id'])){
				if(!empty($order['steps']) && isset($order['steps'])){
					$log_str = pushSteps($order['steps'], $order, $log_str);
				}	
				if(!empty($order['steps_org']) && isset($order['steps_org'])){
					$log_str = pushSteps($order['steps_org'], $order, $log_str);
				}
			}
			if($showLog){
				echo($log_str);
			}
		} else {
			//("INCORRECT ORDER");
		}
	}
	
	function refreshOrders(){
		$tmp = getClientContracts( getClientId($_SESSION['mail'], $_SESSION['password']) );
		if(isset($tmp) && is_array($tmp)){
			
			foreach($tmp as $key => $val){
				$orders = getOrdersFrom1C($val['ІПН/ЄДРПОУ']);

				if(!empty($orders)&&isset($orders)){
					compareOrders($orders);
					foreach($orders as $order){
						pushOrder($order);
					}
				}	
			}
		}	
	}
	
	function checkClient($mail, $pwd){
		
		$tmp = getClientId($mail, $pwd);
		if(!empty ($tmp)){
			return true;
		}
		return false;
	}
	
	function refreshIsAdmin(){
		if (checkIsAdmin(  getClientId( $_SESSION['mail'], $_SESSION['password'] ) ) == 1){
			$_SESSION['is_admin'] = true;
		} else {
			unset ($_SESSION['is_admin'] );
		}
	}
	
	function checkIsAdmin($client_id){
		return selectClientIsAdmin($client_id);
	}


?>