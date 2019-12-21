<?php

class ControllerExtensionPaymentShoplemo extends Controller
{
    private $error = [];

    public function index()
    {
        ini_set('display_errors', 0);
        error_reporting(0);
        $this->load->language('extension/payment/shoplemo');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('setting/setting');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate())
        {
            $this->model_setting_setting->editSetting('payment_shoplemo', $this->request->post);
            $this->session->data['success'] = '<strong>Shoplemo</strong> modül ayarları kaydedildi.!';

            $this->response->redirect($this->url->link('extension/payment/shoplemo', 'user_token=' . $this->session->data['user_token'], true));
        }

        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_edit'] = $this->language->get('text_edit');
        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');
        $data['text_all_zones'] = $this->language->get('text_all_zones');

        $data['api_information'] = $this->language->get('api_information');
        $data['information_tab'] = $this->language->get('information_tab');
        $data['shoplemo_key'] = $this->language->get('api_key');
        $data['shoplemo_secret'] = $this->language->get('secret_key');
        $data['order_status'] = $this->language->get('order_status');
        $data['payment_approved'] = $this->language->get('payment_approved');
        $data['error_payment_approved'] = $this->language->get('error_payment_approved');
        $data['payment_notapproved'] = $this->language->get('payment_notapproved');
        $data['error_payment_notapproved'] = $this->language->get('error_payment_notapproved');
        $data['module_settings'] = $this->language->get('module_settings');
        $data['module_active'] = $this->language->get('module_active');
        $data['module_closed'] = $this->language->get('module_closed');
        $data['module_status'] = $this->language->get('module_status');
        $data['module_language'] = $this->language->get('module_language');
        $data['please_select'] = $this->language->get('please_select');

        $data['help_total'] = $this->language->get('help_total');

        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');

        $data['errors_message'] = [
            'warning' => $this->language->get('error_warning'),
            'payment_shoplemo_api_key' => $this->language->get('error_shoplemo_api_key'),
            'payment_shoplemo_secret_key' => $this->language->get('error_shoplemo_secret_key'),
            'payment_shoplemo_order_completed_id' => $this->language->get('error_shoplemo_order_completed_id'),
            'payment_shoplemo_order_canceled_id' => $this->language->get('error_shoplemo_order_canceled_id'),
            'payment_shoplemo_order_status_general' => $this->language->get('error_shoplemo_order_status_general'),
            'payment_shoplemo_merchant_general' => $this->language->get('error_shoplemo_merchant_general'),
        ];

        $data['breadcrumbs'][] = [
            'text' => $this->language->get('text_shoplemo_breadcrumbs'),
            'href' => HTTPS_SERVER . 'index.php?route=extension/payment/payment/shoplemo&user_token=' . $this->session->data['user_token'],
            'separator' => ' :: ',
        ];

        $data['action'] = HTTPS_SERVER . 'index.php?route=extension/payment/shoplemo&user_token=' . $this->session->data['user_token'];

        $data['cancel'] = HTTPS_SERVER . 'index.php?route=extension/payment&user_token=' . $this->session->data['user_token'];

        if (isset($this->request->post['payment_shoplemo_api_key']))
        {
            $data['payment_shoplemo_api_key'] = trim($this->request->post['payment_shoplemo_api_key']);
        }
        else
        {
            $data['payment_shoplemo_api_key'] = $this->config->get('payment_shoplemo_api_key');
        }

        if (isset($this->request->post['payment_shoplemo_secret_key']))
        {
            $data['payment_shoplemo_secret_key'] = trim($this->request->post['payment_shoplemo_secret_key']);
        }
        else
        {
            $data['payment_shoplemo_secret_key'] = $this->config->get('payment_shoplemo_secret_key');
        }

        if (isset($this->request->post['payment_shoplemo_status']))
        {
            $data['payment_shoplemo_status'] = $this->request->post['payment_shoplemo_status'];
        }
        else
        {
            $data['payment_shoplemo_status'] = $this->config->get('payment_shoplemo_status');
        }

        if ($this->language->get('code') == 'tr')
        {
            $data['language_arr'] = [0 => 'Otomatik', 1 => 'Türkçe', 2 => 'İngilizce'];
        }
        else
        {
            $data['language_arr'] = [0 => 'Automatic', 1 => 'Turkish', 2 => 'English'];
        }

        if (isset($this->request->post['payment_shoplemo_lang']))
        {
            $data['payment_shoplemo_lang'] = $this->request->post['payment_shoplemo_lang'];
        }
        else
        {
            $data['payment_shoplemo_lang'] = $this->config->get('payment_shoplemo_lang');
        }

        if (isset($this->request->post['payment_shoplemo_order_completed_id']))
        {
            $data['payment_shoplemo_order_completed_id'] = $this->request->post['payment_shoplemo_order_completed_id'];
        }
        else
        {
            $data['payment_shoplemo_order_completed_id'] = $this->config->get('payment_shoplemo_order_completed_id');
        }

        if (isset($this->request->post['payment_shoplemo_order_canceled_id']))
        {
            $data['payment_shoplemo_order_canceled_id'] = $this->request->post['payment_shoplemo_order_canceled_id'];
        }
        else
        {
            $data['payment_shoplemo_order_canceled_id'] = $this->config->get('payment_shoplemo_order_canceled_id');
        }

        $this->load->model('localisation/order_status');

        $data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

        if (!$this->config->get('payment_shoplemo_api_key') or !$this->config->get('payment_shoplemo_secret_key'))
        {
            $this->error['payment_shoplemo_merchant_general'] = 1;
        }

        $data['errors'] = $this->error;

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        $data['callback_url'] = $this->getSiteUrl() . 'index.php?route=extension/payment/shoplemo/callback';

        $this->response->setOutput($this->load->view('extension/payment/shoplemo', $data));
    }

    public function getSiteUrl()
    {
        if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443)
        {
            $siteUrl = HTTPS_SERVER;
        }
        else
        {
            $siteUrl = HTTP_SERVER;
        }

        return $siteUrl;
    }

    public function install()
    {
    }

    public function uninstall()
    {
    }

    protected function validate()
    {
        if (!$this->user->hasPermission('modify', 'extension/payment/shoplemo'))
        {
            $this->error['warning'] = 1;
        }

        if (!$this->request->post['payment_shoplemo_api_key'])
        {
            $this->error['payment_shoplemo_api_key'] = 1;
        }

        if (!$this->request->post['payment_shoplemo_secret_key'])
        {
            $this->error['payment_shoplemo_secret_key'] = 1;
        }

        if (!$this->request->post['payment_shoplemo_order_completed_id'])
        {
            $this->error['payment_shoplemo_order_completed_id'] = 1;
        }

        if (!$this->request->post['payment_shoplemo_order_canceled_id'])
        {
            $this->error['payment_shoplemo_order_canceled_id'] = 1;
        }

        if (!$this->error)
        {
            return true;
        }

        return false;
    }
}
