<?php
	
	//must have module
	require_once('tfpdf/wordwrap.php');
	
	//my extend
	class ADPDF extends PDF{
		protected $col_sizes = array();
		protected $total=0;
		protected $total_nalog=0;
		
		public function CalcTotal(array $data, $filter){
			$total = 0;
			foreach($data as $rec){
				
				if($this->RulesCompare($rec, $filter) )
					$total += $rec['Сума з ПДВ'];
			}
			return $total;
		}
		private function RulesCompare($rec, $filter){
			
			if($filter == 'no_conn'){
				
				if( strpos( $rec['n_dogovor'], '-ВР/') ){
					
					return true;
				}
			} elseif($filter == 'conn'){
				
				if(! strpos( $rec['n_dogovor'], '-ВР/') ){
					
					return true;
				}
			}
			return false;
		}
		public function CalcColSizes($data, $pageMargin){
			$k = 20;
			foreach(array_keys($data[0]) as $key){
				if($key!='n_dogovor'){
					$this->col_sizes[]= $this->GetStringWidth($key)*$k;
				}
			}
			while(array_sum($this->col_sizes)>$this->GetPageWidth()-$pageMargin*2) {
				$k-=0.5;
				if($k<=0){
					break;
				}
				$this->col_sizes = [];
				foreach(array_keys($data[0]) as $key){
					if($key!='n_dogovor'){
						$this->col_sizes[]= $this->GetStringWidth($key)*$k;
					}
				}
				###Uncomment for debug
				#$this->Cell(0, 10, $this->GetPageWidth().'  '. array_sum($this->col_sizes).' '.$k);
				#$this->Ln();
			} 
			
			return $this->col_sizes;	
		}
	
		public function GetColSizes(){
			return $this->col_sizes;
		}
		
		public function TableHead($data, $font_size, $col_sizes=Null, $bordered=1, $pageMargin=10,  $filter){
			if(empty($col_sizes)){
				$col_sizes =$this->col_sizes;
			}
			$cnt=0;
			foreach($data as $rec){
				if($this->RulesCompare($rec, $filter) )
					$cnt++;
				
			}
			if($cnt==0)
				return;
			$i = 0;
			$this->SetXY( $pageMargin+$this->GetX(), $this->GetY() ); 
			foreach(array_keys($data[0]) as $key){
				if($key!='n_dogovor'){
					if($col_sizes[$i]-$pageMargin>0) {
						$w = $col_sizes[$i]-$pageMargin;
					} else {
						$w = $pageMargin-$col_sizes[$i];
					}
					$i++;
					
					$this->Cell( $w, $font_size, $key, $bordered);
				}
			}
			$this->Ln();
		}
		
		public function TableBody($data, $font_size, $col_sizes=Null, $bordered=1, $pageMargin=10,  $filter){
			if(empty($col_sizes)){
				$col_sizes =$this->col_sizes;
			}
			$rows = $dH = 0;
			foreach($data as $rec){
				$i = 0;
				$old_y = $this->GetY();
				$this->SetXY( $pageMargin + $this->GetX(), $this->GetY()+$dH );
				if(! $this->RulesCompare($rec, $filter) )
					continue;
				
				foreach($rec as $key => $val){
					if($key!='n_dogovor'){
						
						$x = $this->GetX();
						$y = $this->GetY();
						if($col_sizes[$i]-$pageMargin>0) {
							$w = $col_sizes[$i]-$pageMargin;
						} else {
							$w = $pageMargin-$col_sizes[$i];
						}
						$rows = $this->WordWrap($val, $w);
						$i++;
						if(in_array($key, array('Сума з ПДВ', 'Ціна з ПДВ') )){
							$val = number_format($val, 2, ",", " ");
						}
						if($rows > 1){
							$this->MultiCell( $w, $font_size/2, $val, $bordered);
							$dH = $this->GetY() - $y - $font_size;
							$this->SetXY($x+$w, $y);
						} else {
							$this->Cell( $w, $dH+$font_size, $val, $bordered);
						}			
					}						
				}
				$dH = 0;
				$this->Ln();
			}
		}
		
		public function TableFooter($data, $font_size, $pageMargin=10,  $filter){
			$this->total = $this->CalcTotal($data, $filter);
			$this->total_nalog = $this->total/6;
			
			$size = $this->GetPageWidth()/2 - $pageMargin/2;
			
			$this->Cell($size, $font_size/2, '');
			$txt = sprintf('Разом: %s',  number_format($this->total, 2, ",", " "));
			$this->Cell(
				$this->GetStringWidth(sprintf('У тому числі ПДВ: %s',  number_format($this->total_nalog, 2, ",", " ") )), 
				$font_size/2,
				$txt,
				0,
				0,
				'R'
			);
			$this->Ln();
			$this->Cell($size, $font_size/2, '');
			$txt = sprintf('У тому числі ПДВ: %s',  number_format($this->total_nalog, 2, ",", " ") );
			$this->Cell(
				$this->GetStringWidth(sprintf('У тому числі ПДВ: %s',  number_format($this->total_nalog, 2, ",", " ") )),
				$font_size/2,
				$txt,
				0,
				0,
				'R'
			);
			$this->Ln();
			if($this->total>0 && $this->total_nalog>0){
				$this->Cell(0, $font_size/2, mb_ucfirst(strtolower(num2text_ua(number_format($this->total, 2,'.','')))));
				$this->Ln();
				$this->Cell(0, $font_size/2, 'У т.ч. ПДВ: '.mb_ucfirst(strtolower(num2text_ua(number_format($this->total_nalog, 2,'.','')))) );
			}
		}
		
		public function MakeBodyInvoice($data,  $filter){
			if($this->CalcTotal($data, $filter) == 0 )
				return;
			$font_size = 9;
			$pageMargin = 0;
			
			$this->SetFont('DejaVu','',$font_size);
			$this->SetXY($this->GetX(), $this->GetY()+2);
			$col_sizes = $this->CalcColSizes($data, $pageMargin);
			$this->SetFont('DejaVuSans-Bold','',$font_size);
			$this->TableHead($data, $font_size, $col_sizes, 1, $pageMargin, $filter);
			$this->SetFont('DejaVu','',$font_size);
			$this->TableBody($data, $font_size, $col_sizes, 1, $pageMargin, $filter);
			$this->SetFont('DejaVuSans-Bold','',$font_size);
			$this->TableFooter($data, $font_size, $pageMargin, $filter);
		}
		
		public function MakeHeaderInvoice($clientInfo, array $contracts, array $steps,  $filter){
			$font_size = 12;
			$pageMargin = 5;
			
			$this->AddPage();
			$this->SetFont('DejaVuSans-Bold','',$font_size);
			if($this->CalcTotal($steps, $filter) == 0 ){
				
				$this->SetXY($this->GetPageWidth()/2-$this->GetStringWidth('Рахунки за цим видом діяльності наразі відсутні')/2, $this->GetY());
				$this->Cell($this->GetStringWidth('Рахунки за цим видом діяльності наразі відсутні'), $font_size, 'Рахунки за цим видом діяльності наразі відсутні');
				return;
			}
				
			$x = $this->GetPageWidth()/2 - $this->GetStringWidth('Рахунок на оплату')/2;
			$this->SetXY($x, $this->GetY());
			$this->Cell($this->GetStringWidth('Рахунок на оплату'), $font_size, 'Рахунок на оплату');
			$this->SetLineWidth(0.8);
			#Line(float x1, float y1, float x2, float y2)
			$this->Ln();
			$this->Line(
				$this->GetX(),
				$this->GetY(), 
				$this->GetPageWidth()-2*$pageMargin,
				$this->GetY() 
				);
			
			$this->SetLineWidth(0.4);
			$font_size = 9;
			$this->SetFont('DejaVu','',$font_size);
			
			$this->Cell($this->GetStringWidth('Постачальник:')+$pageMargin, $font_size/2, 'Постачальник:');
			$this->SetFont('DejaVuSans-Bold','',$font_size);
			$this->Cell(0, $font_size/2, 'Акціонерне товариство "Херсонгаз"');
			$this->SetFont('DejaVu','',$font_size);
			$this->Ln();
			$text = 'Р/р UA393808050000000026008619725, Банк АТ "Райффайзен Банк", МФО 380805, Юр. адреса: 73036, Херсонська обл., Дніпровський р-н, м. Херсон, вул. Поповича, буд. 3, тел.: (0552) 41-70-78, 41-70-81, код за ЄДРПОУ 03355353, ІПН 033553521036, Є платником податку на прибуток на загальних підставах';
			if($filter == 'conn')
				$text = 'Р/р UA533808050000000026002619732, Банк АТ "Райффайзен Банк", МФО 380805, Юр. адреса: 73036, Херсонська обл., Дніпровський р-н, м. Херсон, вул. Поповича, буд. 3, тел.: (0552) 41-70-78, 41-70-81, код за ЄДРПОУ 03355353, ІПН 033553521036, Є платником податку на прибуток на загальних підставах';
			$this->WordWrap($text, $this->GetPageWidth() -$this->GetStringWidth('Постачальник:')- 35);
			$this->SetXY($this->GetStringWidth('Постачальник:')+$pageMargin+15, $this->GetY());
			$this->MultiCell(0, $font_size/2, $text);
			
			
			$this->Cell($this->GetStringWidth('Постачальник:')+$pageMargin, $font_size/2, 'Покупець:');
			$this->SetFont('DejaVuSans-Bold','',$font_size);
			$this->Cell(0, $font_size/2, $clientInfo[0]['client_name']);
			$this->SetFont('DejaVu','',$font_size);
			$this->Ln();
			
			$this->Cell($this->GetStringWidth('Постачальник:')+$pageMargin, $font_size/2, 'Договір:');
			$str='';
			
			$tmp = [];
			for($i=0; $i < count($steps); $i++){
				if($this->RulesCompare($steps[$i], $filter) )
					$tmp[]= $steps[$i]['n_dogovor'];
			}
			$dogovor_array = array_reverse(array_unique($tmp));
			
			for($i=0; $i < count($dogovor_array); $i++){

				if($i == count($dogovor_array)-1){
					$str = sprintf('%s%s', $str, $dogovor_array[$i]);
				} else {
					$str = sprintf('%s%s, ', $str, $dogovor_array[$i]);
				}
			}
			
			$this->total = $this->CalcTotal($steps, $filter);
			$this->total_nalog += $this->total/6;
			
			$this->MultiCell(0, $font_size/2, $str );
			
			
			$text = 'Призначення платежу:';
			$rows = $this->WordWrap($text, $this->GetStringWidth('Постачальник:')+$pageMargin);
			$x = $this->GetX()+$this->GetStringWidth('Постачальник:')+$pageMargin;
			$y = $this->GetY();
			$this->MultiCell($this->GetStringWidth('Постачальник:')+$pageMargin, $font_size/2, $text);
			$this->SetXY($x, $y);
			$str = sprintf('%s, в т.ч. ПДВ %s', $str, number_format($this->total_nalog, 2, ",", " "));
			$this->Cell(0, $font_size/2, 'Сплата за послуги згідно договору '.$str );
			for($i=0; $i<$rows; $i++){
				$this->Ln();
			}
		}
		
		public function MakeFooterInvoice($data){
			
		}
	}	
?>