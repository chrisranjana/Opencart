<?php 
class ControllerPaymentDibspw extends Controller {
    private $error = array(); 

    public function index() {
        $this->data = $this->load->language('payment/dibspw');
	$this->document->setTitle($this->language->get('heading_title'));
	$this->load->model('setting/setting');
			
	if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting('dibspw', $this->request->post);				
            $this->session->data['success'] = $this->language->get('text_success');
            $this->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'));
	}

 	if (isset($this->error['warning'])) {
            $this->data['error_warning'] = $this->error['warning'];
	}
        else {
            $this->data['error_warning'] = '';
	}

 	if (isset($this->error['mid'])) {
            $this->data['error_mid'] = $this->error['mid'];
	}
        else {
            $this->data['error_mid'] = '';
	}

  	$this->data['breadcrumbs'] = array();
   	$this->data['breadcrumbs'][] = array(
            'text'      => $this->language->get('text_home'),
            'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),      		
            'separator' => false
        );

        $this->data['breadcrumbs'][] = array(
            'text'      => $this->language->get('text_payment'),
            'href'      => $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => ' :: '
   	);

   	$this->data['breadcrumbs'][] = array(
            'text'      => $this->language->get('heading_title'),
            'href'      => $this->url->link('payment/dibspw', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => ' :: '
   	);
				
	$this->data['action'] = $this->url->link('payment/dibspw', 'token=' . $this->session->data['token'], 'SSL');
	$this->data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');

        $this->load->model('localisation/order_status');
	$this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
        
        $this->loadSettings($this->data);
        
        $this->load->model('localisation/geo_zone');
        $this->data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();
		
	$this->template = 'payment/dibspw.tpl';
	$this->children = array(
            'common/header',
            'common/footer',
	);
				
	$this->response->setOutput($this->render());
    }
        
    function loadSettings(&$data) {
        $aTmp = $data;
        foreach($aTmp as $key => $val) {
            if(strpos($key, 'entry_') !== FALSE) {
                $sTmpKey = str_replace("entry_","dibspw_",$key);
                if (isset($this->request->post[$sTmpKey])) {
                    $data[$sTmpKey] = $this->request->post[$sTmpKey];
                }
                else {
                    $data[$sTmpKey] = $this->config->get($sTmpKey);
                }
                unset($sTmpKey);
            }
        }
        unset($aTmp);
    }
        
    private function validate() {		
        if (!$this->user->hasPermission('modify', 'payment/dibspw')) {
            $this->error['warning'] = $this->language->get('error_permission');
	}
		
	if (isset($this->request->post['dibspw_mid']) && strlen(trim($this->request->post['dibspw_mid'])) == 0) {
            $this->error['mid'] = $this->language->get('error_mid');
	}
		
	if ($this->error && !isset($this->error['warning'])) {
            $this->error['warning'] = $this->language->get('error_warning');
	}
	
	if (!$this->error) {
            return true;
	}
        else {
            return false;
	}	
    }
}
?>