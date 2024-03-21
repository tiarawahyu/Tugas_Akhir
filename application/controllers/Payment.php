<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once dirname(__FILE__) . '/../../Midtrans.php'; 
Midtrans\Config::$clientKey = 'SB-Mid-client-M6iWltXl2qrwZkRs';
Midtrans\Config::$serverKey = 'SB-Mid-server-cBZGDpbKwygdGoyAvyj1gBPj';
Midtrans\Config::$isProduction = false;

class Payment extends CI_Controller {

    public $apiKey;

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->apiKey = "1c0f510e542140c008e2b49d73e5f645";
    }

    public function checkout(){
        // non-relevant function only used for demo/example purpose
        $this->printExampleWarningMessage();

        // Uncomment for production environment
        // Config::$isProduction = true;

        // Uncomment to enable sanitization
        // Config::$isSanitized = true;

        // Uncomment to enable 3D-Secure
        // Config::$is3ds = true;

        // Required
        $transaction_details = array(
            'order_id' => rand(),
            'gross_amount' => 145000, // no decimal allowed for creditcard
        );

        // Optional
        $item1_details = array(
            'id' => 'a1',
            'price' => 50000,
            'quantity' => 2,
            'name' => "Apple"
        );

        // Optional
        $item2_details = array(
            'id' => 'a2',
            'price' => 45000,
            'quantity' => 1,
            'name' => "Orange"
        );

        // Optional
        $item_details = array ($item1_details, $item2_details);

        // Optional
        $billing_address = array(
            'first_name'    => "Andri",
            'last_name'     => "Litani",
            'address'       => "Mangga 20",
            'city'          => "Jakarta",
            'postal_code'   => "16602",
            'phone'         => "081122334455",
            'country_code'  => 'IDN'
        );

        // Optional
        $shipping_address = array(
            'first_name'    => "Obet",
            'last_name'     => "Supriadi",
            'address'       => "Manggis 90",
            'city'          => "Jakarta",
            'postal_code'   => "16601",
            'phone'         => "08113366345",
            'country_code'  => 'IDN'
        );

        // Optional
        $customer_details = array(
            'first_name'    => "Andri",
            'last_name'     => "Litani",
            'email'         => "andri@litani.com",
            'phone'         => "081122334455",
            'billing_address'  => $billing_address,
            'shipping_address' => $shipping_address
        );

        // Fill SNAP API parameter
        $params = array(
            'transaction_details' => $transaction_details,
            'customer_details' => $customer_details,
            'item_details' => $item_details,
        );

        try {
            // Get Snap Payment Page URL
            $paymentUrl = Midtrans\Snap::createTransaction($params)->redirect_url;
        
            // Redirect to Snap Payment Page
            header('Location: ' . $paymentUrl);
        }
        catch (\Exception $e) {
            echo $e->getMessage();
        }
    }


    function printExampleWarningMessage() {
        if (strpos(Midtrans\Config::$serverKey, 'your ') != false ) {
            echo "<code>";
            echo "<h4>Please set your server key from sandbox</h4>";
            echo "In file: " . __FILE__;
            echo "<br>";
            echo "<br>";
            echo htmlspecialchars('Config::$serverKey = \'SB-Mid-server-cBZGDpbKwygdGoyAvyj1gBPj\';');
            die();
        } 
    }

    public function hooks(){ // AKA notification / webhook
        try {
            $notif = new Midtrans\Notification();
        }
        catch (\Exception $e) {
            exit($e->getMessage());
        }

        $notif = $notif->getResponse();
        $transaction = $notif->transaction_status;
        $type = $notif->payment_type;
        $order_id = $notif->order_id;
        $fraud = $notif->fraud_status;

        $new_status = 0;

        if ($transaction == 'capture') {
            // For credit card transaction, we need to check whether transaction is challenge by FDS or not
            if ($type == 'credit_card') {
                if ($fraud == 'challenge') {
                    // TODO set payment status in merchant's database to 'Challenge by FDS'
                    // TODO merchant should decide whether this transaction is authorized or not in MAP
                    echo "Transaction order_id: " . $order_id ." is challenged by FDS";
                } else {
                    // TODO set payment status in merchant's database to 'Success'
                    echo "Transaction order_id: " . $order_id ." successfully captured using " . $type;
                }
            }
        } else if ($transaction == 'settlement') {
            // TODO set payment status in merchant's database to 'Settlement'
            $new_status = 2;
            echo "Transaction order_id: " . $order_id ." successfully transfered using " . $type;
        } else if ($transaction == 'pending') {
            // TODO set payment status in merchant's database to 'Pending'
            echo "Waiting customer to finish transaction order_id: " . $order_id . " using " . $type;
        } else if ($transaction == 'deny') {
            $new_status = 5;
            // TODO set payment status in merchant's database to 'Denied'
            echo "Payment using " . $type . " for transaction order_id: " . $order_id . " is denied.";
        } else if ($transaction == 'expire') {
            $new_status = 5;
            // TODO set payment status in merchant's database to 'expire'
            echo "Payment using " . $type . " for transaction order_id: " . $order_id . " is expired.";
        } else if ($transaction == 'cancel') {
            $new_status = 5;
            // TODO set payment status in merchant's database to 'Denied'
            echo "Payment using " . $type . " for transaction order_id: " . $order_id . " is canceled.";
        }
        $params = array(
            'mt_order_id' => $order_id,
            'mt_notif' => json_encode($notif),
        );
        $this->db->insert('midtrans', $params);
        // return to order detail in customer dashboard

        if($new_status != 0){
            $data = array(
                'order_status' => $new_status
            );
            $this->db->where('order_number', $order_id);
            $this->db->update('orders', $data);
        }
        
        var_dump($notif);
        die("hooks received");
        //redirect("Customer/orders");
    }


    public function transaction_status($order_id, $redirect){
        if(!$order_id){
            die("order_id required");
        }
        $status = \Midtrans\Transaction::status($order_id);
        //$status = json_decode($status);
        //var_dump($status);

        $transaction = $status->transaction_status;
        $type = $status->payment_type;
        $order_id = $status->order_id;
        $fraud = $status->fraud_status;

        $new_status = 0;
        if ($transaction == 'capture') {
            // For credit card transaction, we need to check whether transaction is challenge by FDS or not
            if ($type == 'credit_card') {
                if ($fraud == 'challenge') {
                    // TODO set payment status in merchant's database to 'Challenge by FDS'
                    // TODO merchant should decide whether this transaction is authorized or not in MAP
                    echo "Transaction order_id: " . $order_id ." is challenged by FDS";
                } else {
                    // TODO set payment status in merchant's database to 'Success'
                    echo "Transaction order_id: " . $order_id ." successfully captured using " . $type;
                }
            }
        } else if ($transaction == 'settlement') {
            // TODO set payment status in merchant's database to 'Settlement'
            $new_status = 2;
            echo "Transaction order_id: " . $order_id ." successfully transfered using " . $type;
        } else if ($transaction == 'pending') {
            // TODO set payment status in merchant's database to 'Pending'
            echo "Waiting customer to finish transaction order_id: " . $order_id . " using " . $type;
        } else if ($transaction == 'deny') {
            $new_status = 5;
            // TODO set payment status in merchant's database to 'Denied'
            echo "Payment using " . $type . " for transaction order_id: " . $order_id . " is denied.";
        } else if ($transaction == 'expire') {
            $new_status = 5;
            // TODO set payment status in merchant's database to 'expire'
            echo "Payment using " . $type . " for transaction order_id: " . $order_id . " is expired.";
        } else if ($transaction == 'cancel') {
            $new_status = 5;
            // TODO set payment status in merchant's database to 'Denied'
            echo "Payment using " . $type . " for transaction order_id: " . $order_id . " is canceled.";
        }
        $params = array(
            'mt_order_id' => $status->{"order_id"},
            'mt_notif' => json_encode($status),
        );
        // asumsi format JSON saat cek status dan hooks SAMA
        $this->db->insert('midtrans', $params);

        if($new_status != 0){
            $data = array(
                'order_status' => $new_status
            );
            $this->db->where('order_number', $order_id);
            $this->db->update('orders', $data);
        }

        if($redirect != "-"){
            redirect("Customer/orders/view/".$redirect);
        }
        var_dump($order_id);
        die("\nstatus checked");
    }

    public function tes_transaction_status(){
        $order_id = $this->input->post('order_id');
        $redirect = $this->input->post('redirect');
        redirect('Payment/transaction_status/'. $order_id . "/" .$redirect);
    }
    

    public function checkout_order($order_id){
        //$this->printExampleWarningMessage();

        if(!$order_id){
            die("order_id required");
        }

        $order = $this->db->query("SELECT * FROM orders WHERE id = " . $order_id )->result();
        if(count($order) == 0){
            die("order not found");
        }
        $order = $order[0];
        $cust = json_decode($order->{"delivery_data"});

        $transaction_details = array(
            'order_id' => $order->{"order_number"},
            'gross_amount' => (int) $order->{"total_price"} + (int) $cust->{"pengiriman_value"}
        );

        $order_items = $this->db->query("
            SELECT A.*, B.sku, B.name FROM order_item as A
            JOIN products as B ON A.product_id = B.id
            WHERE A.order_id = " . $order_id
            )->result();
        $item_details = [];
        foreach($order_items as $key => $value){
            $new_item_details = array(
                'id' => $value->{"sku"},
                'price' => (int) $value->{"order_price"},
                'quantity' => (int) $value->{"order_qty"},
                'name' => $value->{"name"}
            );
            array_push($item_details, $new_item_details);
        }
        $new_item_details = array(
            'id' => "ONGKIR",
            'price' => (int) $cust->{"pengiriman_value"},
            'quantity' => 1,
            'name' => "Ongkos Kirim"
        );
        array_push($item_details, $new_item_details);

        $cust_name = explode(" ", $cust->{"customer"}->{"name"}, 2);
        $cust_firstname = $cust_name[0];
        $cust_lastname = "";
        if(count($cust_name) > 1){
            $cust_lastname = $cust_name[1];
        }

        $billing_address = array(
            'first_name'    => $cust_firstname,
            'last_name'     => $cust_lastname,
            'address'       => $cust->{"customer"}->{"address"},
            'city'          => $cust->{"kota"},
            'postal_code'   => $cust->{"kode_pos"},
            'phone'         => $cust->{"customer"}->{"phone_number"},
            'country_code'  => 'IDN'
        );

        $shipping_address = array(
            'first_name'    => $cust_firstname,
            'last_name'     => $cust_lastname,
            'address'       => $cust->{"customer"}->{"address"},
            'city'          => $cust->{"kota"},
            'postal_code'   => $cust->{"kode_pos"},
            'phone'         => $cust->{"customer"}->{"phone_number"},
            'country_code'  => 'IDN'
        );

        $customer_details = array(
            'first_name'    => $cust_firstname,
            'last_name'     => $cust_lastname,
            'email'         =>  $cust->{"customer"}->{"email"},
            'phone'         => $cust->{"customer"}->{"phone_number"},
            'billing_address'  => $billing_address,
            'shipping_address' => $shipping_address
        );

        $params = array(
            'transaction_details' => $transaction_details,
            'customer_details' => $customer_details,
            'item_details' => $item_details,
        );

        try {
            $paymentUrl = Midtrans\Snap::createTransaction($params)->redirect_url;
            header('Location: ' . $paymentUrl);
        }
        catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    public function tes_checkout_order(){
        $order_id = $this->input->post('order_id');
        redirect('Payment/checkout_order/'. $order_id);
    }

    

    public function tes1(){
        $query = $this->db->query("SELECT * FROM midtrans");
        $result = $query->result();
        echo ($result[0]->{"mt_notif"});
    }


    // RAJAONGKIR
    /*
    Method 	    Parameter 	    Wajib 	Tipe 	Keterangan
    GET/HEAD 	key 	        Ya 	    String 	API Key
    GET/HEAD 	android-key 	Tidak 	String 	Identitas aplikasi Android
    GET/HEAD 	ios-key 	    Tidak 	String 	Identitas aplikasi iOS
    GET 	    id 	Tidak 	    String 	ID      propinsi
    */
    public function ro_province(){
        $ch = curl_init("https://api.rajaongkir.com/starter/province");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_ENCODING , "");
        curl_setopt($ch, CURLOPT_MAXREDIRS , 10);
        curl_setopt($ch, CURLOPT_TIMEOUT , 30);
        curl_setopt($ch, CURLOPT_HTTP_VERSION , CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST , "GET");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "key: " . $this->apiKey
        ));
        $response = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);
        if ($err) {
            echo "cURL Error #:" . $err;
            return null;
        } else {
            $result = json_encode(json_decode($response, true));

            $this->db->truncate('ro_province');
            foreach (json_decode($response, true)["rajaongkir"]["results"] as $key => $value){
                $params = array(
                    'province_id' => $value["province_id"],
                    'province' => $value["province"],
                );
                $this->db->insert('ro_province', $params);
            }

            $this->output->set_content_type('application/json')->set_output($result);
            return;
        }

    }


    /*
    Method 	    Parameter 	Wajib 	Tipe 	Keterangan
    GET/HEAD 	key 	    Ya 	    String 	API Key
    GET/HEAD 	android-key Tidak 	String 	Identitas aplikasi Android
    GET/HEAD 	ios-key 	Tidak 	String 	Identitas aplikasi iOS
    GET 	    id 	        Tidak 	String 	ID kota/kabupaten
    GET 	    province 	Tidak 	String 	ID propinsi
    */
    public function ro_city(){
        $ch = curl_init("https://api.rajaongkir.com/starter/city");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_ENCODING , "");
        curl_setopt($ch, CURLOPT_MAXREDIRS , 10);
        curl_setopt($ch, CURLOPT_TIMEOUT , 30);
        curl_setopt($ch, CURLOPT_HTTP_VERSION , CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST , "GET");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "key: " . $this->apiKey
        ));
        $response = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);
        if ($err) {
            echo "cURL Error #:" . $err;
            return null;
        } else {
            $result = json_encode(json_decode($response, true));

            $this->db->truncate('ro_city');
            foreach (json_decode($response, true)["rajaongkir"]["results"] as $key => $value){
                $params = array(
                    'city_id' => $value["city_id"],
                    'province_id' => $value["province_id"],
                    'province' => $value["province"],
                    'type' => $value["type"],
                    'city_name' => $value["city_name"],
                    'postal_code' => $value["postal_code"],
                );
                $this->db->insert('ro_city', $params);
            }

            $this->output->set_content_type('application/json')->set_output($result);
            return;
        }

    }


    public function ro_cost(){
        $origin = $this->input->get('origin');
        $destination = $this->input->get('destination');
        $weight = $this->input->get('weight');
        $courier = $this->input->get('courier');
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.rajaongkir.com/starter/cost",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "origin=".$origin."&destination=".$destination."&weight=".$weight."&courier=".$courier,
            CURLOPT_HTTPHEADER => array(
                "content-type: application/x-www-form-urlencoded",
                "key: " . $this->apiKey,
            ),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
            echo "cURL Error #:" . $err;
            return null;
        } else {
            $result = json_encode(json_decode($response, true));
            $this->output->set_content_type('application/json')->set_output($result);
            return;
        }

    }


}
