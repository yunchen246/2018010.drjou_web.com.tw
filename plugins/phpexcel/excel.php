<?php
	@header('Content-Type:text/html;charset=utf-8');
	class Excel{
		//PHPExcel Class 路徑
		var $class_path='Classes';
		//excel暫存檔目錄(使用前需指定)
		var $save_folder='';
		//目前時間(用以作為檔名)=> YmdHis.xls
		var $now;
		//清理暫存檔(刪除超過10分鐘的檔案)
		var $clear_time=600;
		//宣告 PHPExcel 物件
		var $PHPExcel;
		var $PHPExcelWriter;
		
		//目前使用 Sheet id
		var $active_sheet_id;
		//預設 Sheet 名稱
		var $default_sheet_name=array('分頁一','分頁二','分頁三','分頁四','分頁五','分頁六','分頁七','分頁八','分頁九','分頁十','分頁十一','分頁十二');
		//目前 Header 欄位數 (使用快速寫入功能將比對表頭與表身欄位數是否一致)
		var $header_counts;
		
		
		//建構子
		public function __construct(){
			@header('Content-Type:text/html;charset=utf-8');
			date_default_timezone_set('Asia/Taipei');
			$this->now=date('YmdHis');
			
			//引入 PHPExcel 關聯檔
			include($this->class_path.'/'.'PHPExcel.php');
			include($this->class_path.'/'.'PHPExcel/Writer/Excel2007.php');
			//設定 PHPExcel 物件
			$this->PHPExcel=new PHPExcel();
			//設定 PHPExcelWriter 物件
			$this->PHPExcelWriter = PHPExcel_IOFactory::createWriter($this->PHPExcel,'Excel5');
			
			//選取第一個 Sheet
			$this->select_sheet();
			
		}
		
		//指定暫存目錄,如存在則清掉10分鐘前的舊檔案
		public function assign_folder($save_path){
			$this->save_folder=$_SERVER['DOCUMENT_ROOT'].$save_path;
			if(!is_dir($this->save_folder)){
				mkdir($this->save_folder,'0777');
			}
			else{
				$dir=opendir($this->save_folder);
				while($file=readdir($dir)){
					$file_info=pathinfo($file);
					if(is_file($this->save_folder.'/'.$file_info['basename'])){
						if(strtotime($this->now)-strtotime($file_info['filename'])>$this->clear_time){
							if(strlen($file_info['filename'])==14 && is_numeric($file_info['filename']) && $file_info['extension']=='xls'){
								unlink($this->save_folder.'/'.$file_info['basename']);
							}
						}
					}
				}
				closedir($dir);
			}
		}
		
		//選擇 Sheet 功能
		public function select_sheet($sheet_id=0){
			if($sheet_id>0){
				$this->PHPExcel->createSheet();
			}
			$this->active_sheet_id=$sheet_id;
			$this->PHPExcel->setActiveSheetIndex($sheet_id);
			$this->set_sheet_name();
			$this->set_font_style();
			$this->set_font_size();
			$this->set_cols_width();
		}
		
		//設定 Sheet 名稱
		public function set_sheet_name($name=null){
			if($name==null){
				$name=$this->default_sheet_name[$this->active_sheet_id];
			}
			$this->PHPExcel->getActiveSheet()->setTitle($name);
		}
		
		//設定唯讀
		public function set_readonly($var=true){
			$this->PHPExcel->getActiveSheet()->getProtection()->setSheet($var);
		}
		
		//設置字型
		public function set_font_style($font='新細明體',$var1=null,$var2=null){
			if($var1==null && $var2==null){
				$this->PHPExcel->getDefaultStyle()->getFont()->setName($font);
			}
			elseif($var1!=null && $var2==null){
				$this->PHPExcel->getActiveSheet()->getStyle($var1)->getFont()->setName($font);
			}
			else{
				$this->PHPExcel->getActiveSheet()->getStyleByColumnAndRow($var1,$var2)->getFont()->setName($font);
			}
		}
		//設置字型大小
		public function set_font_size($size=12,$var1=null,$var2=null){
			if($var1==null && $var2==null){
				$this->PHPExcel->getDefaultStyle()->getFont()->setSize($size);
			}
			elseif($var1!=null && $var2==null){
				$this->PHPExcel->getActiveSheet()->getStyle($var1)->getFont()->setSize($size);
			}
			else{
				$this->PHPExcel->getActiveSheet()->getStyleByColumnAndRow($var1,$var2)->getFont()->setSize($size);
			}
		}
		//設置欄位寬度 (預設 20 寬度)
		public function set_cols_width($width=20,$cols=null){
			if($cols==null){
				$this->PHPExcel->getActiveSheet()->getDefaultColumnDimension()->setAutoSize(true);
			}
			elseif(strtolower(trim($width))=='auto' && $cols!=null){
				$this->PHPExcel->getActiveSheet()->getColumnDimension($cols)->setAutoSize(true);
			}
			else{
				$this->PHPExcel->getActiveSheet()->getColumnDimension($cols)->setWidth($width);
			}
		}
		//套用框線
		public function set_border($var1,$var2=null){
			$styleArray = array(
			 'borders' => array(
			  'allborders' => array(
			   'style' => PHPExcel_Style_Border::BORDER_THIN,
			   'color' => array('argb' => '000000'),
			  ),
			 ),
			);
			if($var2==null){
				$var2=$var1;
			}
			if(!is_string($var1) && !is_string($var2)){
				$this->PHPExcel->getActiveSheet()->getStyleByColumnAndRow($var1,$var2)->getBorders()->getAllborders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			}
			else{
				$this->PHPExcel->getActiveSheet()->getStyle($var1.':'.$var2)->applyFromArray($styleArray);
			}
		}
		//儲存格置中
		public function set_grid_align($var1,$var2=null){
			if($var2==null){
				$this->PHPExcel->getActiveSheet()->getStyle($var1)->getAlignment($var1)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
			}
			else{
				$this->PHPExcel->getActiveSheet()->getStyleByColumnAndRow($var1,$var2)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
			}
		}
		//文字置中
		public function set_text_align($var1,$var2=null){
			if($var2==null){
				$this->PHPExcel->getActiveSheet()->getStyle($var1)->getAlignment($var1)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			}
			else{
				$this->PHPExcel->getActiveSheet()->getStyleByColumnAndRow($var1,$var2)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			}
		}
		//粗體
		public function set_font_blod($var1,$var2=null){
			if($var2==null){
				$this->PHPExcel->getActiveSheet()->getStyle($var1)->getFont()->setBold(true);
			}
			else{
				$this->PHPExcel->getActiveSheet()->getStyleByColumnAndRow($var1,$var2)->getFont()->setBold(true);
			}
		}
		//設置字體顏色
		public function set_font_color($color,$var1,$var2=null){
			if($var2==null){
				$var2=$var1;
			}
			if(!is_string($var1) && !is_string($var2)){
				$this->PHPExcel->getActiveSheet()->getStyleByColumnAndRow($var1,$var2)->getFont()->getColor()->setRGB($color);
			}
			else{
				$this->PHPExcel->getActiveSheet()->getStyle($var1)->getFont()->getColor()->setRGB($color);
			}
		}
		
		//設定單一儲存格背景色
		public function set_grid_bgcolor($color='FFFFFF',$var1,$var2=null){
			if(!is_string($var1) && !is_string($var2) && $var2!=null){
				//設定為填滿
				$this->PHPExcel->getActiveSheet()->getStyleByColumnAndRow($var1,$var2)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
				//開始填色
				$this->PHPExcel->getActiveSheet()->getStyleByColumnAndRow($var1,$var2)->getFill()->getStartColor()->setRGB($color);
			}
			else{
				//設定為填滿
				$this->PHPExcel->getActiveSheet()->getStyle($var1)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
				//開始填色
				$this->PHPExcel->getActiveSheet()->getStyle($var1)->getFill()->getStartColor()->setRGB($color);
			}
		}
		
		//合併欄位 預設自動置中並套用框線
		public function set_merge_grid($var1,$var2,$auto=true){
			$this->PHPExcel->getActiveSheet()->mergeCells($var1.':'.$var2);
			if($auto){
				//儲存格置中
				$this->set_grid_align($var1);
				//文字置中
				$this->set_text_align($var1);
				//套用框線
				$this->set_border($var1,$var2);
			}
		}
		
		//寫入欄位值
		public function write($data,$var1,$var2=null){
			if($var2==null){
				$this->PHPExcel->getActiveSheet()->setCellValue($var1,$data);
				$this->set_border($var1);
			}
			else{
				$this->PHPExcel->getActiveSheet()->setCellValueByColumnAndRow($var1,$var2,$data);
				$this->set_border($var1,$var2);
			}
		}
		
		/* 快速使用功能集 */
		//寫入 Header (表頭)
		public function insert_header($data,$row=1){
			if(!is_array($data)){
				echo 'Error,Data must be a Array Object!!';
				exit();
			}
			$this->header_counts=count($data);
			foreach($data as $k=>$v){
				//寫入
				$this->PHPExcel->getActiveSheet()->setCellValueByColumnAndRow($k,$row,$v);
				//儲存格置中
				$this->set_grid_align($k,$row);
				//文字置中
				$this->set_text_align($k,$row);
				//套用框線
				$this->set_border($k,$row);
				//設置預設背景色
				$this->set_grid_bgcolor('DCDDCC',$k,$row);
			}
		}
		
		//寫入 Body (表身)
		public function insert_body($data,$row=2){
			if(!is_array($data)){
				echo 'Error,Data must be a Array Object!!';
				exit();
			}
			else{
				foreach($data as $k=>$v){
					if(!is_array($v)){
						echo 'Error,Data\'s SubArray also must be a Array Object!!';
						exit();
					}
				}
			}
			foreach($data as $k=>$v){
				if($this->header_counts!=count($v)){
					echo 'Error,Header Cols:'.$this->header_counts.'<br />'.
										'But Body Cols:'.count($v).',Not equal!!';
					exit();
				}
			}
			foreach($data as $k=>$v){
				foreach($v as $kk=>$vv){
					//寫入
					$this->PHPExcel->getActiveSheet()->setCellValueByColumnAndRow($kk,$row+$k,$vv);
					//套用框線
					$this->set_border($kk,$row+$k);
				}
			}
		}
		
		//Excel 輸出
		public function output($type=null){
			$file=$this->save_folder.'/'.$this->now.'.xls';
			$this->PHPExcelWriter->save($file);
			if(strtolower($type)=='url' && $type!=null){
				$url='http://'.$_SERVER['HTTP_HOST'].str_replace($_SERVER['DOCUMENT_ROOT'],'',$this->save_folder).'/'.$this->now.'.xls';
				return $url;
			}
			else{
				@header('Content-type: application/force-download');
				@header ('Cache-Control: private, pre-check=0, post-check=0, max-age=0');
				@header('Content-Disposition: attachment; filename="'.$this->now.'.xls'.'"');
				@header('Content-Length:"'.filesize($file).'"');
				@readfile($file);
			}
		}
		
	}
?>