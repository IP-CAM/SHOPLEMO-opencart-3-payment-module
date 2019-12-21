<?php
class ControllerExtensionPaymentShoplemo extends Controller {

    public function index(){

        $this->load->language('extension/payment/shoplemo');
        $data['code'] = $this->language->get('code');

		$data['shoplemo_response'] = $this->get_payment_page_url();

        return $this->load->view('extension/payment/shoplemo', $data); 
    }

    public function get_payment_page_url()
    {
        if ( !isset($_SERVER['HTTP_REFERER']) ){ die('NOT FOUND'); }

        $this->load->language('extension/payment/shoplemo');
        
        $data['code'] = $this->language->get('code');
        $data['text_credit_card'] = $this->language->get('text_credit_card');
        $data['text_start_date'] = $this->language->get('text_start_date');
        $data['text_issue'] = $this->language->get('text_issue');
        $data['text_wait'] = $this->language->get('text_wait');

        $data['button_confirm'] = $this->language->get('button_confirm');
        $data['button_back'] = $this->language->get('button_back');

        $this->session->data['payment_method']['code'] == 'shoplemo';
        $this->load->model('checkout/order');
        $this->load->model('account/order');
        
        $order_info = $this->model_checkout_order->getOrder( $this->session->data['order_id'] );
        
        $products = $this->cart->getProducts();

        $api_key    = $this->config->get('payment_shoplemo_api_key');
        $api_secret = $this->config->get('payment_shoplemo_secret_key');

        $items = [];

        foreach($products as $product){
            
            $unit_price = $product["price"];

            if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) 
                $unit_price = $this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax'));
        
            $items[] = [
                "category" => 0,
                "name" => $product["name"].' '.$product["model"],
                "quantity" => $product["quantity"],
                "type" => 1,
                "price" => ( $this->currency->format($unit_price, $order_info['currency_code'], $order_info['currency_value'], false) * 100 )
            ];
        }

        $shippingInfo = $this->shippingInfo();
        
        if(is_object($shippingInfo) || is_array($shippingInfo)) {
            $items[] = [
                "category" => 0,
                "name" => "Kargo Ücreti",
                "quantity" => 1,
                "type" => 1,
                "price" => ( $this->currency->format($shippingInfo['cost'], $order_info['currency_code'], $order_info['currency_value'], false) * 100 )
            ];
        }
     
        $requestBody = [
            "user_email" => $order_info['email'],
            "buyer_details" => [
                "ip" => $this->GetIP(),
                "port" => $_SERVER["REMOTE_PORT"],
                "city" => $order_info['payment_city'],
                "country" => $order_info['payment_country'],
                "gsm" => $order_info['telephone'],
                "name" => $order_info['payment_firstname'],
                "surname" => $order_info['payment_lastname']
            ],
            "basket_details" => [
                "currency" => "TRY",
                "total_price" => ( $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false) * 100 ),
                "coupon_price" => ( $this->currency->format($this->priceInfo('coupon'), $order_info['currency_code'], $order_info['currency_value'], false) * 100 ),
                "items" => $items
            ],
            "shipping_details" => [
                "full_name" => $order_info['payment_firstname'].' '.$order_info['payment_lastname'],
                "phone" => $order_info['telephone'],
                "address" => $order_info['payment_address_1'] . ' ' . $order_info['payment_address_2']. ' '.$order_info['payment_zone'],
                "city" => $order_info['payment_city'],
                "country" => $order_info['payment_country'],
                "postalcode" => $order_info['payment_postcode'],
            ],
            "billing_details" => [
                "full_name" => $order_info['payment_firstname'].' '.$order_info['payment_lastname'],
                "phone" => $order_info['telephone'],
                "address" => $order_info['payment_address_1'] . ' ' . $order_info['payment_address_2'],
                "city" => $order_info['payment_city'],
                "country" => $order_info['payment_zone'],
                "postalcode" => $order_info['payment_postcode'],
            ],
            "custom_params" => json_encode([
                "order_id" => $order_info["order_id"],
                "customer_id" => $order_info["customer_id"],

            ]),
            "user_message" => $order_info["comment"],
            "redirect_url" => $this->getSiteUrl().'index.php?route=checkout/success',
            "fail_redirect_url" => $this->getSiteUrl().'index.php?route=checkout/cart'
        ];

        print_r($requestBody);
        $requestBody  = json_encode($requestBody);
     
        if ( function_exists('curl_version') ) {

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://payment.shoplemo.com/paywith/credit_card" );
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
            curl_setopt($ch, CURLOPT_POST, 1 ) ;
            curl_setopt($ch, CURLOPT_POSTFIELDS, $requestBody );
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0 );
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0 );
            curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 90 );
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 90);
			curl_setopt($ch, CURLOPT_SSLVERSION, 6);
			curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',                                                                                
                'Content-Length: ' . strlen($requestBody),
                'Authorization: Basic '.base64_encode($api_key.':'.$api_secret), 
            ]);
            $result = @curl_exec( $ch );
            
           
            if( curl_errno($ch) )
            {
                die( "Shoplemo connection error. Details: " . curl_error($ch) );
            }

            curl_close($ch);
            try{
                $result = json_decode( $result, 1 );
                if( $result['status'] == 'success' ) {
                    return $result;
                } else {
                    die( "Request to Shoplemo failed.:" . $result['details'] );
                }
            }catch(Exception $ex){
                return "Failed to handle response";
            }
           
		} else { $data['error'] = $this->language->get("Error_message_curl"); }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput( json_encode( $data ) );

        die;
    }

    private function shippingInfo() {

        if(isset($this->session->data['shipping_method'])) {
            
            $shipping_info      = $this->session->data['shipping_method'];
        
        } else {

            $shipping_info = false;
        }

        if($shipping_info) {

            if ($shipping_info['tax_class_id']) {

                $shipping_info['tax'] = $this->tax->getRates($shipping_info['cost'], $shipping_info['tax_class_id']);
            
            } else {

                $shipping_info['tax'] = false;
            }

        }

        return $shipping_info;
    }

    private function priceInfo($type){
        $this->load->model('setting/extension');

        $totals = array();
        $taxes = $this->cart->getTaxes();
        $total = 0;
        
        // Because __call can not keep var references so we put them into an array. 			
        $total_data = array(
            'totals' => &$totals,
            'taxes'  => &$taxes,
            'total'  => &$total
        );
        
        
        // Display prices
        if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
            $sort_order = array();

            $results = $this->model_setting_extension->getExtensions('total');
        
            foreach ($results as $key => $value) {
                $sort_order[$key] = $this->config->get('total_' . $value['code'] . '_sort_order');
            }

            array_multisort($sort_order, SORT_ASC, $results);

            foreach ($results as $result) {
                if ($this->config->get('total_' . $result['code'] . '_status')) {
                    $this->load->model('extension/total/' . $result['code']);
                    
                    // We have to put the totals in an array so that they pass by reference.
                    $this->{'model_extension_total_' . $result['code']}->getTotal($total_data);
                }
            }
            
            $sort_order = array();

            foreach ($totals as $key => $value) {
                $sort_order[$key] = $value['sort_order'];
            }

            array_multisort($sort_order, SORT_ASC, $totals);
        }

        foreach($totals as $total){
            if($total["code"] == $type)
                return $total["value"];
        }
        
        return 0;
    }

    public function GetIP()
    {
        return "176.216.186.142";

        if( isset( $_SERVER["HTTP_CLIENT_IP"] ) ) {
            $ip = $_SERVER["HTTP_CLIENT_IP"];
        } elseif( isset( $_SERVER["HTTP_X_FORWARDED_FOR"] ) ) {
            $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
        } else {
          $ip = $_SERVER["REMOTE_ADDR"];
        }

        return $ip;
    }

    public function log()
    {
        $logFile = fopen('log.txt', 'w');
        
        fwrite($logFile, "IP : " . $this->GetIP() . PHP_EOL);
        fwrite($logFile, "Date : " . date('d-m-Y H:i:s') . PHP_EOL);

        $i = 0;

        foreach( $_POST as $key => $val ) {
            $i++;
            fwrite($logFile, $i . ") " . $key . " = " . $val . PHP_EOL);
        }

        fclose($logFile);
        
    }
    
    public function getSiteUrl()
    {
        if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) { $siteUrl = HTTPS_SERVER; }
        else { $siteUrl = HTTP_SERVER; }

        return $siteUrl;
    }

	public function callback()
    {
        if ( ! $_POST )
            exit("shoplemo"); 

        if($_POST["status"] != "success")
            exit("status must be success");
        

        $api_key    = $this->config->get('payment_shoplemo_api_key');
        $api_secret = $this->config->get('payment_shoplemo_secret_key');
        
        $_data = json_decode($_POST["data"],true);


        $hash = base64_encode(hash_hmac('sha256',$_data["progress_id"].implode("|",$_data["payment"]).$api_key,$api_secret,true));
   
        if ( $hash != $_data['hash'] ) { die('hash calculate is not matched'); }

        $custom_params = json_decode($_data["custom_params"]);
        $order_id = $custom_params->order_id;

        $this->load->model('checkout/order');
        $getOrder = $this->model_checkout_order->getOrder( $order_id );

        if ( $getOrder ) {

            if (  $_data['payment']['payment_status'] == 'COMPLETED' && $getOrder['order_status_id'] == 0 ) {
                $note = "Ödeme onaylandı.<br/><br/>## Shoplemo->Log ##<br/># Müşteri Ödeme Tutarı: ".$_data["payment"]["paid_price"]. "<br/># Shoplemo Id: ".$_data['progress_id']." #Sipariş Id:".$order_id;
                $this->model_checkout_order->addOrderHistory( $order_id, $this->config->get('payment_shoplemo_order_completed_id'), $note, true );

            } elseif (  $_data['payment']['payment_status'] == 'FAILED' ) {

                $note = "Sipariş iptal edildi.<br/><br/>## Shoplemo ->Log ##<br/># Shoplemo Id: ".$_data['progress_id']." #Sipariş Id:".$order_id."<br/># Hata Mesajı: " . $_data['payment']['error_message'];
                $this->model_checkout_order->addOrderHistory( $order_id, $this->config->get('payment_shoplemo_order_canceled_id'), $note, true );

            }

            exit('OK');

        } else { echo 'Boyle bir siparis bulunamadi. Siparis silinmis veya siparis durumuna elle mudahale olmus.'; exit; }

    }

}