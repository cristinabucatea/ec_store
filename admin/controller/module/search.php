<?php
class ControllerModuleSearch extends Controller {
	private $error = array();

	public function index() {

		$this->load->model('catalog/search');
		$this->load->model('tool/image');

		$this->language->load('module/search');
		$this->load->model('catalog/category'); 
 		$this->load->model('catalog/product');

	    $this->document->setTitle($this->language->get('heading_title')); 

	    $data['heading_title'] = $this->language->get('heading_title');
	    $data['text_setting']  = $this->language->get('text_setting');

	    $data['button_cancel'] = $this->language->get('button_cancel');
	    $data['button_update']  = $this->language->get('button_update');

	    if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
			
	    	$results = $this->model_catalog_search->getAllProducts() ;
	    	$data['products'] = array();	
			foreach ($results as $result) {
				if ($result['image']) {
					$image = $this->model_tool_image->resize($result['image'], $this->config->get('config_image_product_width'), $this->config->get('config_image_product_height'));
				} else {
					$image = $this->model_tool_image->resize('placeholder.png', $this->config->get('config_image_product_width'), $this->config->get('config_image_product_height'));
				}

				if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
					$price = $this->currency->format($result['price']);
				} else {
					$price = false;
				}

				if ((float)$result['special']) {
					$special = $this->currency->format($result['special']);
				} else {
					$special = false;
				}

				if ($this->config->get('config_tax')) {
					$tax = $this->currency->format((float)$result['special'] ? $result['special'] : $result['price']);
				} else {
					$tax = false;
				}

				if ($this->config->get('config_review_status')) {
					$rating = (int)$result['rating'];
				} else {
					$rating = false;
				}

				// Categories
				

				if (isset($result['product_id'])) {
					$categories = $this->model_catalog_product->getProductCategories($result['product_id']);
				} else {
					$categories = array();
				}

				$data['product_categories'] = array();

				foreach ($categories as $category_id) {
					$category_info = $this->model_catalog_category->getCategory($category_id);

					if ($category_info) {
						$product_categories = array(
							'category_id' => $category_info['category_id'],
							'name' => ($category_info['path']) ? $category_info['path'] . ' &gt; ' . $category_info['name'] : $category_info['name']
						);
					}
				}

				$data['products'][] = array(
					'product_id'  => $result['product_id'],
					'product_categories' => $product_categories,
					'thumb'       => $image,
					'name'        => $result['name'],
					'description' => utf8_substr(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8')), 0, $this->config->get('config_product_description_length')) . '..',
					'price'       => $price,
					'special'     => $special,
					'tax'         => $tax,
					'minimum'     => $result['minimum'] > 0 ? $result['minimum'] : 1,
					'rating'      => $result['rating'],
					'href'        => str_replace('admin/', '', $this->url->link('product/product', 'product_id=' . $result['product_id']))
				);
				$data['product_key'][] = strtolower($this->make($result['name']));
			}
			header('Content-Type: text/html; charset=utf-8');
			$file = "../products.txt";
			file_put_contents($file, serialize($data['products']));
			file_put_contents("../product_key.txt", serialize($data['product_key']));

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'));
		}

        $data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$data['action'] = '';
		$data['cancel'] = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_module'),
			'href' => $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('module/category', 'token=' . $this->session->data['token'], 'SSL')
		);
		$this->response->setOutput($this->load->view('module/search.tpl', $data));
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