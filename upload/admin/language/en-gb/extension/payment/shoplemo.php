<?php

// Heading
$_['heading_title'] = 'Shoplemo Payment Module';

// Text
$_['text_payment'] = 'Credit Card (Secure Shopping)';
$_['text_success'] = 'Shoplemo Module Settings Updated!';
$_['text_edit'] = 'Edit Shoplemo Module Settings';
$_['text_shoplemo'] = '<a href="https://www.shoplemo.com/" target="_blank"><img src="view/image/payment/shoplemo.png" alt="shoplemo" title="shoplemo"/></a>';

// Breadcrumbs
$_['text_shoplemo_breadcrumbs'] = 'Shoplemo';

// Integration tab
$_['integrationSettingsTitle'] = 'Integration API Information';
$_['integrationSettingsTitleDesc'] = '(You can see from the INFORMATION tab of the store panel)';
$_['integrationKey'] = 'API Key';
$_['integationSecret'] = 'Secret Key';
$_['integrationCallbackUrl'] = 'Callback URL (You may put this is url to your Shoplemo Store Settings)';

// Order info tab
$_['orderSettingsTitle'] = 'Order Settings';
$_['onPaymentApproved'] = 'When payment is Completed';
$_['onPaymentApprovedDesc'] = 'What status will be assigned to the order when a payment is completed?';
$_['onPaymentCanceled'] = 'When payment is Canceled';
$_['onPaymentCanceledDesc'] = 'What status will be assigned to the order when a payment is canceled?';

// Other settings
$_['otherSettingsTitle'] = 'Module Settings';
$_['extensionStatus'] = 'Module Status';
$_['extensionActive'] = 'Active';
$_['extensionInactive'] = 'Inactive';
$_['extensionLanguage'] = 'Module Language';

$_['minimumCartTotal'] = 'Minimum Cart Total';
$_['paymentOptionSortOrder'] = 'Payment Option Sorting Order';
$_['paymentOptionSortOrderDesc'] = 'Ödeme seçeneğinin sıralama önceliğini bu ayar ile değiştirebilirsiniz.';
$_['paymentGeoZoneId'] = 'Payment Geo Zone Id';
$_['paymentGeoZoneIdDesc'] = '<strong>(Optional)</strong> - Associate the payment method with a Geo Zone';
$_['paymentGeoZoneNull'] = '-NULL-';

// Error messages
$_['required_field_onPaymentCallbacks'] = 'Please make a selection for this required field.';

$_['user_not_authorized'] = 'You are not authorized to access this module!';
$_['required_field_api_key'] = '<strong>' . $_['integrationKey'] . '</strong> field is required!';
$_['required_field_api_secret'] = '<strong>' . $_['integationSecret'] . '</strong> field is required!';
$_['required_field_onPaymentApproved'] = 'You have to choose what status will be assigned when the <strong>Payment Approved!</strong>';
$_['required_field_onPaymentCanceled'] = 'You have to choose what status will be assigned when the <strong>Payment Dont Approved!</strong>';
$_['required_field_api_key_or_secret'] = 'You have to enter <strong>API Key</strong>,<strong>API Secret</strong> informations.<br/>You can get this information from <strong>INFO</strong> section in the Shoplemo Panel.';
$_['required_field_minimum_cart_total'] = '<strong>' . $_['minimumCartTotal'] . '</strong> field is required and must be an integer!';
