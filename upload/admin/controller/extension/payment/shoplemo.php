<?php

class ControllerExtensionPaymentShoplemo extends Controller
{
    private $validationErrors = [];

    public function index()
    {
        ini_set('display_errors', 0);
        error_reporting(0);
        $this->load->language('extension/payment/shoplemo');
        $this->load->model('setting/setting');
        $this->document->setTitle($this->language->get('heading_title'));

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm())
        {
            $this->model_setting_setting->editSetting('payment_shoplemo', [
                'payment_shoplemo_api_key' => trim($this->request->post['payment_shoplemo_api_key']),
                'payment_shoplemo_secret_key' => trim($this->request->post['payment_shoplemo_secret_key']),
                'payment_shoplemo_order_completed_id' => trim($this->request->post['payment_shoplemo_order_completed_id']),
                'payment_shoplemo_order_canceled_id' => trim($this->request->post['payment_shoplemo_order_canceled_id']),
                'payment_shoplemo_status' => trim($this->request->post['payment_shoplemo_status']),
                'payment_shoplemo_lang' => trim($this->request->post['payment_shoplemo_lang']),
                'payment_shoplemo_minimum_cart_total' => trim($this->request->post['payment_shoplemo_minimum_cart_total']),
                'payment_shoplemo_sort_order' => trim($this->request->post['payment_shoplemo_sort_order']),
                'payment_shoplemo_geo_zone_id' => trim($this->request->post['payment_shoplemo_geo_zone_id']),
            ]);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('extension/payment/shoplemo', 'user_token=' . $this->session->data['user_token'], true));
        }

        $shoplemoLang = new stdClass();

        $shoplemoLang->title = $this->language->get('heading_title');

        // Integration tab
        $shoplemoLang->integrationSettingsTitle = $this->language->get('integrationSettingsTitle');
        $shoplemoLang->integrationSettingsTitleDesc = $this->language->get('integrationSettingsTitleDesc');
        $shoplemoLang->integrationKey = $this->language->get('integrationKey');
        $shoplemoLang->integrationSecret = $this->language->get('integationSecret');
        $shoplemoLang->integrationCallbackUrl = $this->language->get('integrationCallbackUrl');

        // Order info tab
        $shoplemoLang->orderSettingsTitle = $this->language->get('orderSettingsTitle');
        $shoplemoLang->onPaymentApproved = $this->language->get('onPaymentApproved');
        $shoplemoLang->onPaymentApprovedDesc = $this->language->get('onPaymentApprovedDesc');
        $shoplemoLang->onPaymentCanceled = $this->language->get('onPaymentCanceled');
        $shoplemoLang->onPaymentCanceledDesc = $this->language->get('onPaymentCanceledDesc');

        $shoplemoLang->onPaymentCallbackSelect = $this->language->get('required_field_onPaymentCallbacks');

        // Other settings
        $shoplemoLang->otherSettingsTitle = $this->language->get('otherSettingsTitle');
        $shoplemoLang->extensionStatus = $this->language->get('extensionStatus');
        $shoplemoLang->extensionActive = $this->language->get('extensionActive');
        $shoplemoLang->extensionInactive = $this->language->get('extensionInactive');
        $shoplemoLang->extensionLang = $this->language->get('extensionLanguage');

        $shoplemoLang->minimumCartTotal = $this->language->get('minimumCartTotal');
        $shoplemoLang->paymentOptionSortOrder = $this->language->get('paymentOptionSortOrder');
        $shoplemoLang->paymentOptionSortOrderDesc = $this->language->get('paymentOptionSortOrderDesc');
        $shoplemoLang->paymentGeoZoneId = $this->language->get('paymentGeoZoneId');
        $shoplemoLang->paymentGeoZoneIdDesc = $this->language->get('paymentGeoZoneIdDesc');
        $shoplemoLang->paymentGeoZoneNull = $this->language->get('paymentGeoZoneNull');

        $shoplemoLang->error_messages = [
            'user_not_authorized' => $this->language->get('user_not_authorized'),
            'required_field_api_key' => $this->language->get('required_field_api_key'),
            'required_field_api_secret' => $this->language->get('required_field_api_secret'),
            'required_field_onPaymentApproved' => $this->language->get('required_field_onPaymentApproved'),
            'required_field_onPaymentCanceled' => $this->language->get('required_field_onPaymentCanceled'),
            'required_field_api_key_or_secret' => $this->language->get('required_field_api_key_or_secret'),
            'required_field_minimum_cart_total' => $this->language->get('required_field_minimum_cart_total'),
        ];

        $data['shoplemoLang'] = $shoplemoLang;

        $data['breadcrumbs'][] = [
            'text' => $this->language->get('text_shoplemo_breadcrumbs'),
            'href' => $this->getSiteUrl() . 'index.php?route=extension/payment/payment/shoplemo&user_token=' . $this->session->data['user_token'],
            'separator' => ' :: ',
        ];

        $data['formActionUrl'] = $this->getSiteUrl() . 'index.php?route=extension/payment/shoplemo&user_token=' . $this->session->data['user_token'];
        $data['formCancelUrl'] = $this->getSiteUrl() . 'index.php?route=marketplace/extension&user_token=' . $this->session->data['user_token'];

        $data['payment_shoplemo_api_key'] = $this->config->get('payment_shoplemo_api_key');
        $data['payment_shoplemo_secret_key'] = $this->config->get('payment_shoplemo_secret_key');
        $data['payment_shoplemo_status'] = $this->config->get('payment_shoplemo_status');
        $data['payment_shoplemo_lang'] = $this->config->get('payment_shoplemo_lang');
        $data['payment_shoplemo_order_completed_id'] = $this->config->get('payment_shoplemo_order_completed_id');
        $data['payment_shoplemo_order_canceled_id'] = $this->config->get('payment_shoplemo_order_canceled_id');
        $data['payment_shoplemo_minimum_cart_total'] = $this->config->get('payment_shoplemo_minimum_cart_total');
        $data['payment_shoplemo_sort_order'] = $this->config->get('payment_shoplemo_sort_order');
        $data['payment_shoplemo_geo_zone_id'] = $this->config->get('payment_shoplemo_geo_zone_id');

        if ($data['payment_shoplemo_lang'] == 1)
        {
            $data['availableLanguages'] = [0 => 'Otomatik', 1 => 'Türkçe', 2 => 'İngilizce'];
        }
        else
        {
            $data['availableLanguages'] = [0 => 'Auto', 1 => 'Turkish', 2 => 'English'];
        }

        $this->load->model('localisation/order_status');
        $this->load->model('localisation/geo_zone');

        if (!$this->config->get('payment_shoplemo_api_key') or !$this->config->get('payment_shoplemo_secret_key'))
        {
            $this->validationErrors['required_field_api_key_or_secret'] = 1;
        }

        $data['errors'] = $this->validationErrors;

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $data['callback_url'] = $this->getSiteUrl(false) . 'index.php?route=extension/payment/shoplemo/callback';
        $data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
        $data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

        $this->response->setOutput($this->load->view('extension/payment/shoplemo', $data));
    }

    public function getSiteUrl($admin = true)
    {
        if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443)
        {
            $siteUrl = ($admin) ? HTTPS_SERVER : HTTPS_CATALOG;
        }
        else
        {
            $siteUrl = ($admin) ? HTTP_SERVER : HTTP_CATALOG;
        }

        return $siteUrl;
    }

    public function install()
    {
    }

    public function uninstall()
    {
    }

    protected function validateForm()
    {
        if (!$this->user->hasPermission('modify', 'extension/payment/shoplemo'))
        {
            $this->validationErrors['user_not_authorized'] = 1;
        }

        if (!$this->request->post['payment_shoplemo_api_key'])
        {
            $this->validationErrors['required_field_api_key'] = 1;
        }

        if (!$this->request->post['payment_shoplemo_secret_key'])
        {
            $this->validationErrors['required_field_api_secret'] = 1;
        }

        if (!$this->request->post['payment_shoplemo_order_completed_id'])
        {
            $this->validationErrors['required_field_onPaymentApproved'] = 1;
        }

        if (!$this->request->post['payment_shoplemo_order_canceled_id'])
        {
            $this->validationErrors['required_field_onPaymentCanceled'] = 1;
        }

        if (!$this->request->post['payment_shoplemo_minimum_cart_total'] || !is_numeric($this->request->post['payment_shoplemo_minimum_cart_total']))
        {
            $this->validationErrors['required_field_minimum_cart_total'] = 1;
        }

        if (empty($this->validationErrors))
        {
            return true;
        }

        return false;
    }
}
