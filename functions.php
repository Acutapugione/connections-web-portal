<?php
	error_reporting(E_ALL);
	session_start();
	session_encode();
    require_once "config.php";
    require_once "db_worker.php";
	
	function resetCurrSession(){
		/*session_regenerate_id();
		session_unset($_SESSION);
		unset($_POST);
		header("Location: \signIn.php"); 
		exit();*/
	}
	function refreshClientInfo($info){
		return updateClientInfo($info);
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
	function getClientMessagesByType($client=null, $type=null){
		return selectClientMessagesByType($client, $type);
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
		#var_dump($res);
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
	function showTable($table){
		
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
					echo sprintf(
					'<td class="%s">%s</td>',
					$elemClasses['td'], $val
					);
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
			
			if(!empty($client_id)){
				$cntr = 0;
				$expanded = "true";
				$collapsed = "";
				$show = "show";
				$tmp = getClientMessageTypes($client_id);
				
				foreach($tmp as $dialog){
					
					foreach($dialog as $key => $val){
						
						$res = getClientMessagesByType($client_id, $val);
						
						if (is_array($res) && !empty($res)){
							$cntr++;
							if($cntr>1){ 
								$expanded = "false";
								$collapsed = "collapsed";
								$show = "";
							}
							echo sprintf('
							<div class="card">
								<div class="card-header" id="messageHeading%s">
								  <h2 class="mb-0">
									<button class="btn btn-primary %s" type="button" data-toggle="collapse" data-target="#messageCollapse%s" aria-expanded="%s" aria-controls="messageCollapse%s">
										%s
									</button>
								  </h2>
								</div>
								<div id="messageCollapse%s" class="collapse %s" aria-labelledby="messageHeading%s" data-parent="#accordionExample1 #tab2">
								  <div class="card-body">
									', $cntr, $collapsed, $cntr, $expanded, $cntr, $key.' - '.$val, $cntr, $show, $cntr);
							showClientMessages($client_id, $val);
							echo ('
							</div>
							</div>
							</div>');
						} else {
							echo '<div class="alert alert-danger alert-dismissible fade show">Повідомлення відсутні</div>';
						}
					}
				}
			} else {
				echo sprintf('<div class="alert alert-danger alert-dismissible  fw-5  fade show text-danger">
								<a href="\settings.php" type="button" class="btn btn-danger">Редагувати налаштування</a>
								<strong>Увага!</strong> Відсутня інформація за цим ІПН: %s
							</div>',
							$client_ipn);
			}
		} elseif (!empty($client_id)){
			$tmp = getClientMessageTypes($client_id);
			foreach($tmp as $type){
				showClientMessages($type);
			}
			showTable($tmp);
		} else {
			echo '<div class="alert alert-danger alert-dismissible fw-5 fade show text-danger">
					<a href="\settings.php" type="button" class="btn btn-danger">Редагувати налаштування</a>
					<strong>Увага!</strong> Наразі відсутні данні по Вашому ІПН.
				  </div>';
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
				echo sprintf('<div class="alert alert-danger alert-dismissible  fw-5  fade show text-danger">
								<a href="\settings.php" type="button" class="btn btn-danger">Редагувати налаштування</a>
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
			echo '<div class="alert alert-danger alert-dismissible fw-5 fade show text-danger">
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
	}

?>