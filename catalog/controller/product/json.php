<?php
class ControllerProductJson extends Controller {
	public function index() {

		$this->load->model('catalog/product');
		$this->load->model('tool/image');

		if (isset($this->request->get['search'])) {
			$search = $this->request->get['search'];
		} else {
			$search = '';
		}
		if (isset($this->request->get['categoryId'])) {
			$categoryId = $this->request->get['categoryId'];
		} else {
			$categoryId = 0;
		}
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'ASC';
		}
		if (isset($this->request->get['check_amount']) && isset($this->request->get['amount'])) {
			$amount = $this->request->get['amount'];
			$amount_params = explode('-', str_replace(array('$', ' '), '', $amount));
			$min = (int) $amount_params[0];
			$max = (int) $amount_params[1];
		}
		if (isset($this->request->get['search'])) {
			$results = $this->model_catalog_product->getProducts($filter_data);
			$input = preg_quote(strtolower($this->make($search)), '~'); 
			$current = unserialize(file_get_contents("product_key.txt"));
			$data_products = unserialize(file_get_contents("products.txt"));
			//$result = preg_grep('~' . $input . '~', $current);
			$results = array();
			foreach($result as $key => $val)
			{
				$results[] = $data_products[$key];
				
			}
		}else
		{
			$results = array();
		}
		$sortArray = array(); 

		foreach($results as $result){ 
		    foreach($result as $key=>$value){ 
		        if(!isset($sortArray[$key])){ 
		            $sortArray[$key] = array(); 
		        } 
		        $sortArray[$key][] = $value; 
		    } 
		} 

		$orderby = "name"; //change this to whatever key you want from the array 

		if(count($results) > 0)
		{
			array_multisort($sortArray[$orderby],SORT_ASC,$results); 

			if($sort == 'DESC')
			{
				array_multisort($sortArray[$orderby],SORT_DESC,$results); 
			}
		}
		

		header('Content-Type: application/json');
		header('Content-Type: text/html; charset=utf-8');
		echo json_encode($results);
	}

	private function make($str){
  	        if(!$str) return false;
  	        $unicode = array(
                'a'=>array('á','à','ả','ã','ạ','ă','ắ','ặ','ằ','ẳ','ẵ','â','ấ','ầ','ẩ','ẫ','ậ'),
  	            'A'=>array('Á','À','Ả','Ã','Ạ','Ă','Ắ','Ặ','Ằ','Ẳ','Ẵ','Â','Ấ','Ầ','Ẩ','Ẫ','Ậ'),
  	            'd'=>array('đ'),
  	            'D'=>array('Đ'),
  	            'e'=>array('é','è','ẻ','ẽ','ẹ','ê','ế','ề','ể','ễ','ệ'),
  	            'E'=>array('É','È','Ẻ','Ẽ','Ẹ','Ê','Ế','Ề','Ể','Ễ','Ệ'),
  	            'i'=>array('í','ì','ỉ','ĩ','ị'),
  	            'I'=>array('Í','Ì','Ỉ','Ĩ','Ị'),
  	            'o'=>array('ó','ò','ỏ','õ','ọ','ô','ố','ồ','ổ','ỗ','ộ','ơ','ớ','ờ','ở','ỡ','ợ'),
  	            '0'=>array('Ó','Ò','Ỏ','Õ','Ọ','Ô','Ố','Ồ','Ổ','Ỗ','Ộ','Ơ','Ớ','Ờ','Ở','Ỡ','Ợ'),
  	            'u'=>array('ú','ù','ủ','ũ','ụ','ư','ứ','ừ','ử','ữ','ự'),
  	            'U'=>array('Ú','Ù','Ủ','Ũ','Ụ','Ư','Ứ','Ừ','Ử','Ữ','Ự'),
  	            'y'=>array('ý','ỳ','ỷ','ỹ','ỵ'),
  	            'Y'=>array('Ý','Ỳ','Ỷ','Ỹ','Ỵ'),
  	           // ' '=>array(' ','&quot;','.',"'",'"'),
                //''=>array('&', '%', '+', '*', '/', '\\', '?', ';', ',', '|', '_', '^', '$', '@', '#', '(', ')'),
                ' '=>array('?')
  	        );

  	        foreach($unicode as $nonUnicode=>$uni){
  	        	foreach($uni as $value)
            	$str = str_replace($value,$nonUnicode,$str);
  	        }
			$str=trim(strtolower($str));
      $str=rtrim($str,"-");
      return $str;
  	}
}