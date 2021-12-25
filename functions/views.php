<?php
	
	function showClients(){
		showTable(getClients());
	}
	
	function showTable($table, $btns=null){
		if(isset($btns)){
			$cntr = 0;
		}
		if(is_array($table) && !empty($table)){
			$columns = array_keys($table[0]);
			$elemClasses = [ 
				'table' => 'table table-responsive table-md table-hover table-secondary table-striped table-bordered border-dark',
				'th' => 'table-primary text-dark border-dark',
				'td' => '',
				'thead' => '',
				'tbody' => '',
				'tr' => '',
				'img' => 'light avatar-img',
				];
				
			echo sprintf('
				<div class="overflow-auto">
				<table class="%s">
					<thead class="%s">
					<tr class="%s">',
				$elemClasses['table'], 
				$elemClasses['thead'],
				$elemClasses['tr']
			);	
			if (!empty($table[0]["Послуга"])){  
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
				if (!empty($table[0]["Послуга"])){
					echo getStepStatusIcon($item, $elemClasses);
				}
				foreach ($item as $key => $val){
					
					if (DateTime::createFromFormat('Y-m-d H:i:s', $val) ){	
						
						if ($val !== '0000-00-00 00:00:00'){
							$val = DateTime::createFromFormat('Y-m-d H:i:s', $val)->format('d.m.Y');
						} else {
							$val =Null;
						}
						
					} 
					if(isset($btns)){
						if(is_array($btns)){
							$btns_html = '';
							foreach($btns as $btn){
								$btns_html = sprintf(
									'%s<button class="btn" type="submit" id="%s_%s" name="%s" value="%s"><img class="light avatar-img" src="%s"></img></button><strong>Видалити</strong>',
									$btns_html,
									$btn,
									$cntr,
									$btn,
									$val,
									$GLOBALS['ICONS'][$btn]									
								);
							}
							echo sprintf(
							'<td class="%s">
								<div class="form-check">
								  %s
								  <label class="form-check-label" for="inputTg%s">
										<strong>%s</strong>
								  </label>
									</div>
								</td>',
								$elemClasses['td'], $btns_html, $cntr, $val, $cntr, $val
							);
							
						} else {
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
						}
						
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
			</table>
			</div>'
			);
		}
	}
	
	function showMessageTypes(){
		$res = getMessageTypes();
		foreach($res as $item){
			if(isset($item['Тема повідомлення'])){
				echo 'Тема повідомлення :'.$item['Тема повідомлення'];
			}
		}
	}	
	
	function showAppealOrders(){
		$client_id = getClientId($_SESSION['mail'], $_SESSION['password']);
		$contracts = getClientContracts($client_id);
		$tmp = selectClient_order_id($client_id);
		
		echo('<select class="form-select" name="appeal_order_id" required>
				<option value="" disabled selected>Виберіть замовлення</option>');
		foreach($tmp as $rec)
			echo '<option value="'.$rec['id'].'">'.$rec['Номер замовлення'].'</option>';

		echo '</select>';
	}
	
	function showAppealTypes(){
		$tmp = getAppealTypes();
		
		echo('<select class="form-select" name="appeal_type_id" required>
				<option value="" disabled selected>Виберіть тип звернення</option>');
		foreach($tmp as $rec)
			echo '<option value="'.$rec['id'].'">'.$rec['name'].'</option>';
		echo '</select>';
	}
	
	function showAppeals(){
		$client_id = getClientId($_SESSION['mail'], $_SESSION['password']);
		$contracts = getClientContracts($client_id);
		$tmp = selectClient_order_id($client_id);
		
		$appeals= [];
		foreach($tmp as $rec){
			$appeals= array_merge_recursive($appeals, getAppeals('',$rec['id']));
		}
		showTable($appeals);

	}
	function showClientDialogs($client_mail=null, $client=null){
		if (!empty($client_mail)) {
			$client_id = getClientIdByMail($client_mail);
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
						<strong>Увага!</strong> Наразі відсутні Ваші повідомлення.
					  </div>';
				}
			} else {
				echo '<div class="alert alert-warning alert-dismissible fw-5 fade show">
						<a href="\settings.php" type="button" class="btn btn-warning">Редагувати налаштування</a>
						<strong>Увага!</strong> Наразі відсутні Ваші повідомлення.
					  </div>';
			}
		}
	}
	
	function showClientEditableContracts(){
		$client_id = getClientId($_SESSION['mail'], $_SESSION['password']);
		$contracts = getClientContracts($client_id);
		
		if(empty($contracts) || !isset($contracts) || is_null($contracts)){
			echo '<div class="alert alert-warning alert-dismissible fw-5 fade show">
						<a href="\settings.php" type="button" class="btn btn-warning">Редагувати налаштування</a>
						<strong>Увага!</strong> Наразі відсутні Ваші контракти.
					  </div>';
			return false;
		}
		showTable($contracts, [ 'delete']);
	}
	
	function showClientContracts(){
		$client_id = getClientId($_SESSION['mail'], $_SESSION['password']);
		$contracts = getClientContracts($client_id);
		if(empty($contracts) or !isset($contracts)){
			echo '<div class="alert alert-warning alert-dismissible fw-5 fade show">
						<a href="\settings.php" type="button" class="btn btn-warning">Редагувати налаштування</a>
						<strong>Увага!</strong> Наразі відсутні Ваші контракти.
					  </div>';
			return false;
			
		}
		$cntr = 0;
		$expanded = "true";
		$collapsed = "";
		$show = "show";
		
		foreach($contracts as $key => $val){
			$orders = selectClientOrdersTemplate($client_id, getContract($val['ІПН/ЄДРПОУ'], $client_id));
			if(!empty($orders)){
				$cntr++;
				if($cntr>1){ 
					$expanded = "false";
					$collapsed = "collapsed";
					$show = "";
				}
				echo sprintf(
					'<div class="card p-3" style="background: #cae2f266">
						<div class="card-header" id="contractHeading%s">
							<h2 class="mb-0 text-center">
								<button class="btn btn-primary w-25 %s" type="button" data-toggle="collapse" data-target="#contractCollapse%s" aria-expanded="%s" aria-controls="contractCollapse%s">
								%s
								</button>
							</h2>
						</div>
					<div id="contractCollapse%s" class="collapse %s" aria-labelledby="contractHeading%s" data-parent="#accordionExample1">
					<div class="card-body ">',
					$cntr, 
					$collapsed, 
					$cntr, 
					$expanded, 
					$cntr, 
					$val['ІПН/ЄДРПОУ'],
					$cntr, 
					$show, 
					$cntr
				);
				showOrders(getContract($val['ІПН/ЄДРПОУ'], $client_id));
				echo (
					'</div>
					</div>
					
					</div>'); 
			}  else {
				$cntr++;
				if($cntr>1){ 
					$expanded = "false";
					$collapsed = "collapsed";
					$show = "";
				}
				echo sprintf(
					'
					<div class="card p-3" style="background: #cae2f266">
						<div class="card-header" id="contractHeading%s">
							<h2 class="mb-0 text-center">
								<button class="btn btn-primary w-25 %s" type="button" data-toggle="collapse" data-target="#contractCollapse%s" aria-expanded="%s" aria-controls="contractCollapse%s">
								%s
								</button>
							</h2>
						</div>
					<div id="contractCollapse%s" class="collapse %s" aria-labelledby="contractHeading%s" data-parent="#accordionExample1">
					<div class="card-body">
					',
					$cntr, 
					$collapsed, 
					$cntr, 
					$expanded, 
					$cntr, 
					$val['ІПН/ЄДРПОУ'],
					$cntr, 
					$show, 
					$cntr
				);
				
				echo sprintf(
					'<div class="alert alert-warning alert-dismissible fw-5 fade show">
						<strong>Увага!</strong> Наразі відсутні замовлення за цим контрактом.
					</div></div>
					</div>
					</div>'
				);
			}
		}
	}

	function showOrders($contract_id){
		$client_id = getClientId($_SESSION['mail'], $_SESSION['password']);
		$orders = selectClientOrdersTemplate($client_id, $contract_id);
			
		$cntr = 0;
		$expanded = "true";
		$collapsed = "";
		$show = "show";
		foreach($orders as $order){
			foreach($order as $key => $val){
				$res = getOrderSteps(selectOrderId($val, $client_id, $contract_id));
				$res_org = getOrderOrgSteps(selectOrderId($val, $client_id, $contract_id));
				$cntr++;
				if($cntr>1){ 
					$expanded = "false";
					$collapsed = "collapsed";
					$show = "";
				}
				echo sprintf(
					'<div class="card">
						<div class="card-header" id="orderHeading%s">
							<h2 class="mb-0">
								<button class="btn btn-primary %s" type="button" data-toggle="collapse" data-target="#orderCollapse%s" aria-expanded="%s" aria-controls="orderCollapse%s">
								%s
								</button>
							</h2>
						</div>
					<div id="orderCollapse%s" class="collapse %s" aria-labelledby="orderHeading%s" data-parent="#contractCollapse%s">
					<div class="card-body">', 
					$cntr, 
					$collapsed, 
					$cntr, 
					$expanded,
					$cntr, 
					$key.' - '.$val,
					$cntr, 
					$show,
					$cntr,
					$cntr
				);
				showOrderInfo(selectOrderId($val, $client_id, $contract_id)) ;
				if(!empty($res_org)){
					showOrderOrgSteps( selectOrderId($val, $client_id, $contract_id) );
				}
				if(!empty($res)){
					showOrderSteps( selectOrderId($val, $client_id, $contract_id) );
				}
				
				echo (
					'</div>
					</div>
					</div>'
				); 	
				if(empty($res)){
					echo sprintf(
						'<div class="alert alert-warning alert-dismissible fw-5 fade show">
							<strong>Увага!</strong> Наразі відсутні послуги за цим %s замовленням.
						</div>',
						$val
					);
				} elseif(empty($res_org)){
					echo sprintf(
						'<div class="alert alert-warning alert-dismissible fw-5 fade show">
							<strong>Увага!</strong> Наразі відсутні етапи приєднання за цим %s замовленням.
						</div>',
						$val
					);
				}
			}
		}
	}
	
	function showOrderOrgSteps($order_id=null){
        if(!empty($order_id)) {
			
            $tmp = getOrderOrgSteps($order_id);

			if(is_array($tmp) && !empty($tmp)){
				echo '<div class="alert alert-info alert-dismissible fw-5 fade show"><strong>Етапи приєднання</strong></div>';
				showTable($tmp);
				return true;
			}
		}
		return false;
    }
	
    function showOrderSteps($order_id=null){
        if(!empty($order_id)) {
            $tmp = getOrderSteps($order_id);

			if(is_array($tmp) && !empty($tmp)){
				echo (
					'<form method="POST" action="\printInvoice.php" >
					<div class="alert alert-info alert-dismissible fw-5 fade show">
						<strong>Послуги</strong>
					'
				);
				if($order_id){
					$unpayed = selectUnpayedSteps($order_id);
					if(!empty($unpayed )){
						echo (
							'<button name="printInvoiceNoConnBtn" 
							method="POST" type="submit" 
							class="btn btn-primary m-2" value="'.$order_id.'">
								Друкувати рахунок на інші послуги
							</button>'
						);
						echo (
							'<button name="printInvoiceConnBtn" 
							method="POST" type="submit" 
							class="btn btn-primary m-2" value="'.$order_id.'">
								Друкувати рахунок на приєднання
							</button>'
						);
					}
				}
				echo (	
					'</div>
					</form>'
				);
				showTable($tmp);
				return true;
			}
		}
		return false;
    }
	
	function showOrderInfo($order_id=null){
		if(!empty($order_id)){
			$tmp = getOrderInfo($order_id);
			
			if(is_array($tmp) && !empty($tmp)){
				echo '<div class="alert alert-info alert-dismissible fw-5 fade show">
						<strong>Інформація по об\'єкту замовлення</strong>
						</div>';
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