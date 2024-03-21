<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Orders extends CI_Controller {
    public function __construct()
    {
        parent::__construct();

        verify_session('admin');

        $this->load->model(array(
            'order_model' => 'order'
        ));
    }

    public function index()
    {
        $params['title'] = 'Kelola Order';

        $config['base_url'] = site_url('admin/orders/index');
        $config['total_rows'] = $this->order->count_all_orders();
        $config['per_page'] = 10;
        $config['uri_segment'] = 4;
        $choice = $config['total_rows'] / $config['per_page'];
        $config['num_links'] = floor($choice);
 
        $config['first_link']       = '«';
        $config['last_link']        = '»';
        $config['next_link']        = '›';
        $config['prev_link']        = '‹';
        $config['full_tag_open']    = '<div class="pagging text-center"><nav><ul class="pagination justify-content-center">';
        $config['full_tag_close']   = '</ul></nav></div>';
        $config['num_tag_open']     = '<li class="page-item"><span class="page-link">';
        $config['num_tag_close']    = '</span></li>';
        $config['cur_tag_open']     = '<li class="page-item active"><span class="page-link">';
        $config['cur_tag_close']    = '<span class="sr-only">(current)</span></span></li>';
        $config['next_tag_open']    = '<li class="page-item"><span class="page-link">';
        $config['next_tagl_close']  = '<span aria-hidden="true">&raquo;</span></span></li>';
        $config['prev_tag_open']    = '<li class="page-item"><span class="page-link">';
        $config['prev_tagl_close']  = '</span>Next</li>';
        $config['first_tag_open']   = '<li class="page-item"><span class="page-link">';
        $config['first_tagl_close'] = '</span></li>';
        $config['last_tag_open']    = '<li class="page-item"><span class="page-link">';
        $config['last_tagl_close']  = '</span></li>';

        $this->load->library('pagination', $config);
        $page = ($this->uri->segment(4)) ? $this->uri->segment(4) : 0;
 
        $filter_periode = $this->input->get('filter_periode');
        $filter_status = $this->input->get('filter_status');
        if(!$filter_periode){
            $filter_periode = "_ALL_";
        }
        if(!$filter_status){
            $filter_status = "_ALL_";
        }
        $orders["filter_periode"] = $filter_periode;
        $orders["filter_status"] = $filter_status;

        $mOrders = $this->order->get_all_orders_filter($config['per_page'], $page, $filter_periode, $filter_status);
        foreach($mOrders as $k => $v){
            $v->{"M_ID"} = $k+1;
        }
        $orders['orders'] = $mOrders;
        $orders['pagination'] = $this->pagination->create_links();

        $export = $this->input->get('export');
        if($export){
            $content = [];
            foreach($mOrders as $k => $v){
                array_push($content, [
                    "Order No." => $v->order_number,
                    "Tanggal" => $v->order_date,
                    "Total Order" => "Rp ". number_format($v->total_price, 2, ",", "."),
                    "Pembeli" => $v->customer,
                ]);
            }
            $json_params = [];
            $json_params["filename"] = "Data Order";
            $json_params["title"] = "Data Order";
            $json_params["subtitle"] = "";
            $json_params["column_width"] = ["20", "20", "20", "25"];
            $json_params["data"] = $content;
            return $this->exportData($json_params);
        }else{
            $this->load->view('header', $params);
            $this->load->view('orders/orders', $orders);
            $this->load->view('footer');
        }
    }


    public function exportData($orders)
    {
        $api_endpoint = 'http://62.72.51.244:5555/export_excel';
        $file_save_path = FCPATH . 'storages/';

        $json_data = json_encode($orders);
        $curl = curl_init($api_endpoint);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $json_data);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($json_data))
        );
        $response = curl_exec($curl);
        $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if ($http_status === 200) {
            $file_name = 'order.xlsx';
            if (file_put_contents($file_save_path . $file_name, $response)) {
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment;filename="'. $file_name .'"');
                header('Cache-Control: max-age=0');
                readfile($file_save_path . $file_name);
                unlink($file_save_path . $file_name);
                exit;
            } else {
                echo "Gagal menyimpan file.";
            }
        } else {
            echo "Gagal mengunduh file. Status HTTP: $http_status";
        }
        curl_close($curl);
    }


    public function view($id = 0)
    {
        if ( $this->order->is_order_exist($id))
        {
            $data = $this->order->order_data($id);
            $items = $this->order->order_items($id);
            $banks = json_decode(get_settings('payment_banks'));
            $banks = (Array) $banks;

            $midtrans = $this->db->query("SELECT * FROM midtrans WHERE mt_order_id = '" . $data->{"order_number"} . "' ORDER BY mt_id DESC ")->result();
            if(count($midtrans) == 0){
                //$response = array('code' => 200, 'error' => TRUE, 'message' => 'Riwayat transaksi tidak tercatat.');
                $midtrans = null;
            }else{
                $midtrans = json_decode($midtrans[0]->{"mt_notif"});
            }
 
            $params['title'] = 'Order #'. $data->order_number;

            $order['data'] = $data;
            $order['midtrans'] = $midtrans;
            $order['items'] = $items;
            $order['delivery_data'] = json_decode($data->delivery_data);
            $order['banks'] = $banks;
            $order['order_flash'] = $this->session->flashdata('order_flash');
            $order['payment_flash'] = $this->session->flashdata('payment_flash');

            $this->load->view('header', $params);
            $this->load->view('orders/view', $order);
            $this->load->view('footer');
        }
        else
        {
            show_404();
        }
    }

    public function status()
    {
        $status = $this->input->post('status');
        $order = $this->input->post('order');

        $this->order->set_status($status, $order);
        $this->session->set_flashdata('order_flash', 'Status berhasil diperbarui');

        redirect('admin/orders/view/'. $order);
    }
}