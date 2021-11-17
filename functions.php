<?php
	error_reporting(E_ALL);
	session_start();
	session_encode();
    require_once "config.php";
    require_once "db_worker.php";
	
	function resetCurrSession(){
		unset($_SESSION);
		#echo sprintf('<br>%s<br>',$error_text);
	}
	
	function refreshClientInfo($info){
		return updateClientInfo($info);
	}
	function pushOrder($order){
		return createOrder($order);
	}
	function pushStepType($step_type_name){
		return insertStepType($step_type_name);
	}
	function createOrder(array $order){
		if(
		is_array($order) 
		&& !empty($order)
		&& isset($order['order_number']) 
		&& ( isset($order['client_ipn']) or isset($order['contract_ipn']))
		&& ( isset($order['steps']) && is_array($order['steps']) && !empty($order['steps'])	)	)
		{
			$order['order_id'] = selectOrderId($order['order_number']);
			//Установим ИНН клиента
			if(isset($order['client_ipn'])){
				$client_ipn = $order['client_ipn'];					
			} elseif (isset($order['contract_ipn'])){
				$client_ipn = $order['contract_ipn'];
			}
			//Выбрать клиента по ИНН
			$tmp = selectClient($client_ipn);
			if(is_array($tmp) && !empty($tmp)){
				$client_id = $tmp[0]['id'];
			}
			
			if(isset($client_id)){//Если такой существует то подставить его ИД в заказ
				$order['client_id'] = $client_id;
			}
			
			
			//Выбрать ИН контракта по ИНН 
			$contract_id = selectContractId($client_ipn);
			if(isset($contract_id) && !empty($contract_id)){//Если такой существует то подставить его ИД в заказ
				$order['contract_id'] = $contract_id;
			} 
						
			//Создаем заказ/Обновляем если есть
			if(isset($order['order_id']) && updateOrder($order)){
				echo '<br><strong>order updated</strong><br>';

			} elseif (insertOrder($order)){
				echo '<br><strong>order inserted</strong><br>';
				$order['order_id'] = selectOrderId($order['order_number']);
			}		

			if(isset($order['order_id']) && !empty($order['steps']) && isset($order['steps'])){
				#echo '<ul>';
				foreach($order['steps'] as $step){
					$step['order_id'] = $order['order_id'];
					if(isset($step['step_type_name']) && isset($order['order_number'])){
						$step['step_id']=selectStepId($step['step_type_name'], $order['order_number']);
					}
					if(isset($step['order_id'])){
						//Создаем этап/Обновляем если есть 
						if(isset($step['step_id'])  && $step['step_id']!=false && updateStep($step)){
							echo sprintf('<ul><div><strong>step %s updated</strong></div>', $step['step_id']);
							
						} elseif(insertStep($step)){
							$step['step_id']=selectStepId($step['step_type_name'], $order['order_number']);
							echo sprintf('<ul><div><strong>step %s inserted</strong><br></div>', $step['step_id']);
						}
						
						foreach($step as $key => $val){
							echo sprintf('<li><strong>%s</strong> : %s</li>', $key, $val);
						}
					}
					echo '</ul>';
				}
			}
		} else {
			var_dump($order);
		}
	}
	function pushClient($info){
		return insertClient($info);
	}
	function pushMessage($message=null){
		return insertMessage($message);
	}
	function getClientMessageTypes($client=null){
		return selectClientMessageTypes($client);
	}
	function getClientDialogs($client=null){
		return selectClientDialogs($client);
	}
	function getClientMessagesByType($client_id=null, $type_name=null){
		return selectClientMessagesByType($client_id, $type_name);
	}
	function getMessageTypes(){
		return selectMessageTypes();
	}
	
	function showMessageTypes(){
		$res = getMessageTypes();
		foreach($res as $item){
			if(isset($item['Тема повідомлення'])){
				echo 'Тема повідомлення :'.$item['Тема повідомлення'];
			}
		}
	}
	
    function getClientOrders($client=null){
        return selectClientOrdersTemplate($client);
    }

    function getOrderSteps($order=null){
        return selectOrderStepsTemplate($order);
    }
	function getClientMail($login, $password){
		$res = selectClientMail($login, $password);
		if(is_array($res) && !empty($res)){
			return $res[0]['mail'];
		} 
	}
	function getClientIpn($login, $password){
		$res = selectClientIpn($login, $password);
		if(is_array($res) && !empty($res)){
			return $res[0]['ipn_key'];
		} 
	}
	function getClientId($login, $password){
		$res = selectClientId($login, $password);
		if(is_array($res) && !empty($res)){
			return $res[0]['id'];
		} 
	}
	function checkClient($uname, $pwd){
		$tmp = getClientId($uname, $pwd);
		if(!empty ($tmp)){
			return true;
		}
		return false;
	}
	function getClientIdByIpn($ipn_key){
		$res = selectClient($ipn_key);
		if(is_array($res) && !empty($res)){
			return $res[0]['id'];
		} 
	}
	
	function getStepStatusIcon($elem, $classArr){
		$statusIcon = '';
		$tmp = '';
		
		foreach($elem as $key => $val){
			if (is_null($val )){
				if( $key == 'Дата оплати' ){
					$tmp = $GLOBALS['ICONS']['unPayed'];
					break;
				} elseif ( $key == 'Дата виконання' ) {
					$tmp = $GLOBALS['ICONS']['unCompleted'];
					break;
				} else {
					$tmp = $GLOBALS['ICONS']['unPayed'];
					break;
				}
			} else {
				$tmp = $GLOBALS['ICONS']['success'];
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
	function showTable($table, $btns=null){
		if(isset($btns)){
			$cntr = 0;
		}
		if(is_array($table) && !empty($table)){
			$columns = array_keys($table[0]);
			$elemClasses = [ 
				'table' => 'table table-md table-hover table-secondary table-striped table-bordered border-dark',
				'th' => 'table-primary text-dark border-dark',
				'td' => '',
				'thead' => '',
				'tbody' => '',
				'tr' => '',
				'img' => 'light avatar-img',
				];
				
			echo sprintf('
				<table class="%s">
					<thead class="%s">
					<tr class="%s">',
				$elemClasses['table'], 
				$elemClasses['thead'],
				$elemClasses['tr']
			);	
			if (!empty($table[0]["Етап робіт"])){  
				echo sprintf(
				'<th style="width: 5vw;" class="%s" scope="col"></th>',
				$elemClasses['th']);
				}
			foreach($columns as $col){
				
				echo sprintf('
					<th class="%s" scope="col">
						%s 
					</th>', 
					$elemClasses['th'],
					$col				
				);
			
			}
			echo ('
				</tr>
			</thead>
			<tbody>'
			);
			foreach ($table as $item){
				echo sprintf('<tr style="width: 5vw;" class="%s">', $elemClasses['tr']);
				if (!empty($table[0]["Етап робіт"])){
					echo getStepStatusIcon($item, $elemClasses);
				}
				foreach ($item as $key => $val){
					if (DateTime::createFromFormat('Y-m-d H:i:s', $val)){		
						$val = DateTime::createFromFormat('Y-m-d H:i:s', $val)->format('d.m.Y');
					}
					if(isset($btns)){
						
						echo sprintf(
							'<td class="%s">
								<div class="form-check">
								  <input class="form-check-input" type="radio" name="inputRadio" id="inputTg%s" value="%s">
								  <label class="form-check-label" for="inputTg%s">
									%s
								  </label>
								</div>
							</td>',
							$elemClasses['td'], $cntr, $val, $cntr, $val
							);
							$cntr++;	
					} else {
						echo sprintf(
						'<td class="%s">%s</td>',
						$elemClasses['td'], $val
						);
					}
				}
				echo '</tr>';
			}
			echo ('
				</tbody>
			</table>'
			);
		}
	}
			
	function showClientDialogs($client_ipn=null, $client=null){
		if (!empty($client_ipn)) {
			$client_id = getClientIdByIpn($client_ipn);
			$cntr = 0;
			$expanded = "true";
			$collapsed = "";
			$show = "show";
			if (!empty($client_id)){
				$tmp = getClientMessageTypes($client_id);
				if (is_array($tmp) && !empty($tmp)){
					foreach($tmp as $type){
						foreach($type as $key => $val){
							$messages = getClientMessagesByType($client_id, $val);
							if(is_array($messages) && !empty($messages)){
								$cntr++;
								if($cntr>1){ 
									$expanded = "false";
									$collapsed = "collapsed";
									$show = "";
								}
								echo sprintf('
									<div class="card">
										<div class="card-header" id="messHeading%s">
										  <h2 class="mb-0">
											<button class="btn btn-primary %s" type="button" data-toggle="collapse" data-target="#messCollapse%s" aria-expanded="%s" aria-controls="messCollapse%s">
											
												%s
											</button>
										  </h2>
										</div>
										<div id="messCollapse%s" class="collapse %s" aria-labelledby="messHeading%s" data-parent="#accordionExample1">
										  <div class="card-body">
											', $cntr, $collapsed, $cntr, $expanded, $cntr, $key.' - '.$val, $cntr, $show, $cntr
								);
								showClientMessages($client_id, $val);
								echo ('
								</div>
								</div>
								</div>'
								); 	
							}
						}
					}
				} else {
					echo '<div class="alert alert-warning alert-dismissible fw-5 fade show">
						<a href="\messenger.php" type="button" class="btn btn-warning">Написати повідомлення</a>
						<strong>Увага!</strong> Наразі відсутні повідомлення.
					  </div>';
				}
			} else {
				echo '<div class="alert alert-danger alert-dismissible fw-5 fade show">
						<a href="\settings.php" type="button" class="btn btn-danger">Редагувати налаштування</a>
						<strong>Увага!</strong> Наразі відсутні данні по Вашому ІПН.
					  </div>';
			}
		}
	}
	function showClientOrders($client_ipn=null, $client_id=null){
        if (!empty($client_ipn)) {
			$client_id = getClientIdByIpn($client_ipn);
			$cntr = 0;
			$expanded = "true";
			$collapsed = "";
			$show = "show";
			if(!empty($client_id)){
				$tmp = getClientOrders($client_id);
				if (is_array($tmp) && !empty($tmp)){
					foreach($tmp as $order){
						foreach($order as $key => $val){
							$res = getOrderSteps($val);

							if (is_array($res) && !empty($res)){
								$cntr++;
								if($cntr>1){ 
									$expanded = "false";
									$collapsed = "collapsed";
									$show = "";
								}
								echo sprintf('
								<div class="card">
									<div class="card-header" id="orderHeading%s">
									  <h2 class="mb-0">
										<button class="btn btn-primary %s" type="button" data-toggle="collapse" data-target="#orderCollapse%s" aria-expanded="%s" aria-controls="orderCollapse%s">
										
											%s
										</button>
									  </h2>
									</div>
									<div id="orderCollapse%s" class="collapse %s" aria-labelledby="orderHeading%s" data-parent="#accordionExample1">
									  <div class="card-body">
										', $cntr, $collapsed, $cntr, $expanded, $cntr, $key.' - '.$val, $cntr, $show, $cntr);
								showOrderSteps($val);
								echo ('
								</div>
								</div>
								</div>'); 			
							}
						}	
					}
				} else {
					echo '<div class="alert alert-warning alert-dismissible fw-5 fade show">
						<a href="\settings.php" type="button" class="btn btn-warning">Редагувати налаштування</a>
						<strong>Увага!</strong> Наразі відсутні замовлення по Вашому ІПН.
					  </div>';
				}
			} else {
				echo sprintf('<div class="alert alert-warning alert-dismissible  fw-5  fade show">
								<a href="\settings.php" type="button" class="btn btn-warning">Редагувати налаштування</a>
								<strong>Увага!</strong> Відсутня інформація за цим ІПН: %s
							</div>',
							$client_ipn);
			}
        } elseif (!empty($client_id)){
			$tmp = getClientOrders($client_id);
			
			foreach($tmp as $order){
				showOrderSteps($order);
			}
			showTable($tmp);
		} else {
			echo '<div class="alert alert-danger alert-dismissible fw-5 fade show">
					<a href="\settings.php" type="button" class="btn btn-danger">Редагувати налаштування</a>
					<strong>Увага!</strong> Наразі відсутні данні по Вашому ІПН.
				  </div>';
		}
    }
    function showOrderSteps($order=null){
        if(!empty($order)) {
            $tmp = getOrderSteps($order);
			if(is_array($tmp) && !empty($tmp)){
				showTable($tmp);
				return true;
			}
		}
		return false;
    }
	function showClientMessages($client=null, $type=null){
		if(!empty($client) && !empty($type)) {
			$tmp = getClientMessagesByType($client, $type);
			if(is_array($tmp) && !empty($tmp)){
				showTable($tmp);
				return true;
			}
		}
		return false;
	}/*
	function showDialogMessages($client=null,  $dialog_id=null){
		if(!empty($client) && !empty($dialog_id)) {
			$tmp = getClientMessagesByDialog($client, $dialog_id);
			if(is_array($tmp) && !empty($tmp)){
				showTable($tmp);
				return true;
			}
		}
		return false;
	}*/

?>