<?php

include (dirname(dirname(dirname(dirname(dirname(dirname(__FILE__))))))) . '/config.rbs.php';
include 'rbs-lib/rbs-discount.php';

if (!class_exists('msPaymentInterface')) {
    require_once dirname(dirname(dirname(__FILE__))) . '/model/minishop2/mspaymenthandler.class.php';
}
define('RBS_CACERT_FILE',  realpath(dirname(__FILE__)) . '/rbs-lib/cacert.cer');

class RBS extends msPaymentHandler implements msPaymentInterface
{

    /**
     * Версия модуля
     *
     * @var string
     */
    const module_version = "1.4.2";
    /**
     * CMS
     *
     * @var string
     */
    const cms_name = 'Modx Revolution';

    /**
     * Путь файлу сертификата
     *
     * @var string
     */
    const cacert_file = RBS_CACERT_FILE;

    /**
     * Путь файлу сертификата
     *
     * @var string
     */
    const enable_cacert = RBS_ENABLE_CACERT;

    /**
     * ЛОГИН МЕРЧАНТА
     *
     * @var string
     */
    const merchant_login = RBS_MERCHANT_LOGIN;
    /**
     * ПАРОЛЬ МЕРЧАНТА
     *
     * @var string
     */
    const merchant_password = RBS_MERCHANT_PASSWORD;
    /**
     * СТРАНИЦА ВОЗВРАТА ПОСЛЕ УСПЕШНОЙ ОПЛАТЫ
     *
     * @var string
     */
    const success_url = RBS_SUCCESS_URL;
    /**
     * Тестовый режим
     *
     * @var string
     */
    const test_mode = RBS_TEST_MODE;
    /**
     * Включен режим обработки коллбека
     *
     * @var string
     */
    const callback_mode = RBS_ENABLE_CALLBACK;

    /**
     * URL шлюза для тестового режима
     *
     * @var string
     */
    const error_url = RBS_ERROR_URL;
    /**
     * URL шлюза для тестового режима
     *
     * @var string
     */
    const test_url = RBS_TEST_URL;
    /**
     * URL шлюза для боевого режима
     *
     * @var string
     */
    const prod_url = RBS_PROD_URL;
    /**
     * Альтернативный URL шлюза для боевого режима
     *
     * @var string
     */

    const prod_url_alt = RBS_PROD_URL_ALT;
    /**
     * Альтернативный URL шлюза для боевого режима
     *
     * @var string
     */
    const prod_url_alt_prefix = RBS_PROD_URL_ALT_PREFIX;
    /**
     * НДС
     *
     *
     * @var integer
     */
    const vat_rate = RBS_VAT_RATE;

    /**
     * НДС
     *
     *
     * @var integer
     */
    const tax_system = RBS_TAX_SYSTEM;

    /**
     * Скидки
     *
     *
     * @var boolean
     */
    const discount_enable = RBS_DISCOUNT_ENABLED;
    /**
     * Логирование
     *
     * @var boolean
     */

    const logging = RBS_LOGGING;

    const ffd_version = RBS_FFD_VERSION;
    const ffd_payment_method = RBS_FFD_PAYMENT_METHOD;
    const ffd_payment_method_delivery = RBS_FFD_PAYMENT_METHOD_DELIVERY;
    const ffd_payment_object = RBS_FFD_PAYMENT_OBJECT;
    const ffd_measurement_name = RBS_MEASUREMENT_NAME;
    const ffd_measurement_code = RBS_MEASUREMENT_CODE;

    public $config;
    public $modx;

    function __construct(xPDOObject $object, $config = array())
    {
        $this->modx = &$object->xpdo;

        $siteUrl = $this->modx->getOption('site_url');
        $assetsUrl = $this->modx->getOption('minishop2.assets_url', $config, $this->modx->getOption('assets_url') . 'components/minishop2/');

        $gateway_prod_url = $this->getGateProdUrl(self::merchant_login,self::prod_url_alt_prefix,self::prod_url,self::prod_url_alt);

        $this->config = array_merge(array(
            'gatewayUrl' => RBS_TEST_MODE ? self::test_url : $gateway_prod_url,
            'gate_url_test' => self::test_url,
            'gate_url_prod' => $gateway_prod_url,
            'returnUrl' => $siteUrl . substr($assetsUrl, 1) . 'payment/rbs.php',
            'callbackUrl' => $siteUrl . substr($assetsUrl, 1) . 'payment/rbs.php?isCallback=1',
            'callback_mode' => self::callback_mode,
            'userName' => self::merchant_login,
            'password' => self::merchant_password,
//            'currency' => self::currency,
            'jsonParams' => array(
                'CMS' => self::cms_name, 
                'Module-Version' => self::module_version
            ),
            'logging' => self::logging,
            'ffd_version' => self::ffd_version,
            'paymentMethod' => self::ffd_payment_method,
            'paymentObject' => self::ffd_payment_object,
            'tax_type' => self::vat_rate,
        ), $config);

    }


    /**
     * ФОРМИРОВАНИЕ ЗАКАЗА
     *
     * Метод register.do
     *
     * @param mixed[] Заказ
     * @return mixed[]
     */
    public function send(msOrder $order)
    {
        $id = $order->get('id');

        if (RBS_TWO_STAGE === true) {
            $method = 'registerPreAuth.do';
        } else {
            $method = 'register.do';
        }

        $data = array(
            'orderNumber' => $order->get('num') . '#' . $id . '#' . time(),
            'amount' => round($order->get('cost') * 100),
            'description' => ("Оплата заказа - " . $id),
            'userName' => $this->config['userName'],
            'password' => $this->config['password'],
            'returnUrl' => $this->config['returnUrl'],
//            'currency' => $this->config['currency'],
        );

        
        
        
        if (RBS_SEND_ORDER === true) {

            
            $order_billing_phone = preg_replace('/\D+/', '', $_POST['phone']);
            $order_billing_email = $_POST['email'];
            $items = array();
            $itemsCnt = 1;

            $products = $order->getMany('Products');
            $i = 0;

            foreach ($products as $val) {
                // here is minishop2
                $tax_type = 0;

                /** @var msProduct $product */
                $name = $val->get('name');
                if (empty($name) && $product = $val->getOne('Product')) {
                    $name = $product->get('pagetitle');
                }
                $price = $val->get('price') * 100;
                $count = $val->get('count');

                $item['positionId'] = $itemsCnt++;
                $item['name'] = $name;
                $item['quantity'] = array(
                    'value' => $val->get('count'),
                    'measure' => $this->config['ffd_version'] == "v1.2" ? self::ffd_measurement_code : self::ffd_measurement_name
                );
                $item['itemAmount'] = $price * $count;
                $item['itemCode'] = $val->get('id');
                $item['tax'] = array(
                    'taxType' => $this->config['tax_type']
                );
                $item['itemPrice'] = str_replace(',', '.', $price);
                $i++;

                    $item['itemAttributes'] = [
                        'attributes' => [
                            [
                                'name' => 'paymentMethod',
                                'value' => $this->config['paymentMethod']
                            ],
                            [
                                'name' => 'paymentObject',
                                'value' => $this->config['paymentObject']
                            ],
                        ]
                    ];
                $items[] = $item;
            }

            $delivery_cost = $order->get('delivery_cost');
            if($delivery_cost > 0) {
                $delivery_info = $order->getOne('Delivery')->toArray();
                $item_delivery = [
                    'positionId' => $itemsCnt++,
                    'name' => $delivery_info['name'],
                    'quantity' => array(
                        'value' => 1,
                        'measure' => $this->config['ffd_version'] == "v1.2" ? self::ffd_measurement_code : self::ffd_measurement_name
                    ),
                    'itemAmount' => round($delivery_cost * 100),
                    'itemCode' => $delivery_info['id'] . "_DELIVERY",
                    'itemPrice' => round($delivery_cost * 100),
                    'tax' => array(
                        'taxType' => $this->config['tax_type'],
                    )
                ];

                    $item_delivery['itemAttributes'] = [
                        'attributes' => [
                            [
                                'name' => 'paymentMethod',
                                'value' => self::ffd_payment_method_delivery,
                            ],
                            [
                                'name' => 'paymentObject',
                                'value' => 4
                            ],
                        ]
                    ];
                array_push($items, $item_delivery);
            }

            // DISCOUNT CALCULATE
            if(self::discount_enable) {
                $DiscountHelper = new rbsDiscount();
                $discount = $DiscountHelper->discoverDiscount($data['amount'],$items);
                if($discount > 0) { 
                    $DiscountHelper->setOrderDiscount($discount);
                    $recalculatedPositions = $DiscountHelper->normalizeItems($items);
                    $recalculatedAmount = $DiscountHelper->getResultAmount();
                    $items = $recalculatedPositions;
                }
            }

            /* Создание и заполнение массива данных заказа для фискализации */
            $order_bundle = array(
                'orderCreationDate' => time(),
                'customerDetails' => array(
                    'phone' => $order_billing_phone,
                ),
                'cartItems' => array('items' => $items)
            );
            if($order_billing_email) {
                $order_bundle['customerDetails']['email'] = $order_billing_email;
                $this->config['jsonParams']['email'] = $_POST['email'];
            }
            /* Заполнение массива данных для запроса c фискализацией */
            $data['orderBundle'] = json_encode($order_bundle);
            $data['jsonParams'] = json_encode($this->config['jsonParams']);
            $data['taxSystem'] = self::tax_system;


        }
        if($this->isCallbackMode()) {
            $this->updateCallback(array(
                'login' => $this->config['userName'],
                'password' => $this->config['password'],
                'test_mode' => self::test_mode,
                'callback_http_method' => 'GET',
                'callbacks_enabled' => true,
                'callback_addresses' => $this->config['callbackUrl'],
                'callback_operations' => 'approved,deposited,declinedByTimeout'
            ));
        }
        $response = $this->gateway($method, $data);

        if($response['errorCode'] > 0) {
            return $this->success('', array('redirect' => $this->config['returnUrl'] . '?error=1&code=' . $response['errorCode'] . '&message=' . $response['errorMessage']));
        }
        return $this->success('', array('redirect' => $response['formUrl']));
    }

    /**
     * ПЕРЕДАЧА ДАННЫХ В ШЛЮЗ
     *
     *
     * @param string - Название метода
     * @param array [] - Данные
     * @return mixed[]
     */
    public function gateway($method, $data)
    {
        $ca_info = self::cacert_file;

        $curl_opt = array(
            CURLOPT_URL => $this->config['gatewayUrl'] . $method,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($data),
            CURLOPT_HTTPHEADER => array('CMS:' . self::cms_name, 'Module-Version: ' . self::module_version),
            CURLOPT_SSLVERSION => 6
        );
        
        $ssl_verify_peer = false;
        if (self::enable_cacert === true && file_exists($ca_info)) {
            $ssl_verify_peer = true;
            $curl_opt[CURLOPT_CAINFO] = $ca_info;
        }


        $curl_opt[CURLOPT_SSL_VERIFYPEER] = $ssl_verify_peer;

        $curl = curl_init();
        curl_setopt_array($curl, $curl_opt);

        $response = curl_exec($curl);
        if($response) {
            $response = json_decode($response, true);
        }

        if ($response === false) {
            $response = array(
                'errorCode' => 999,
                'errorMessage' => curl_error($curl),
            );
        }
        curl_close($curl);

        if ($this->config['logging']) {
            $message = "\n----============----\n";
            $message .= 'RBS: METHOD ' . $method  . "\n" . 'REQUEST_DATA:' . "\n" .  print_r($data, 1)  . "\n" .  'REQUEST_RESPONSE:' . "\n" .  print_r(json_encode($response), 1);
            $message .= "\n";
            $this->modx->log(modX::LOG_LEVEL_ERROR, $message);
        }
        return $response;
    }

    /**
     * ПОЛУЧЕНИЕ ДАННЫХ О СТАТУСЕ ЗАКАЗА
     *
     * @param string id заказа в шлюзе
     */
    public function receiver($orderId)
    {
        if(!$orderId) {
            return false;
        }
        if(isset($_REQUEST['isCallback'])) {
            if($_REQUEST['operation'] != 'deposited' && $_REQUEST['operation'] != 'approved') {
                return true;
            }
        }

        $data = [
            'orderId' => $orderId,
            'userName' => $this->config['userName'],
            'password' => $this->config['password'],
        ];
        if(!isset($_REQUEST['isCallback']) && $this->isCallbackMode()) {
            $this->config['logging'] = false;
        }
        $response = $this->gateway('getOrderStatusExtended.do', $data);

        if (($response['errorCode'] == 0) && (($response['orderStatus'] == 1) || ($response['orderStatus'] == 2))) {
            $ms2 = $this->modx->getService('miniShop2');
            if(isset($_REQUEST['isCallback']) || !$this->isCallbackMode()) {
                $ms2->changeOrderStatus(explode('#', $response['orderNumber'])[1], 2);
            }
            $redirectUrl = self::success_url;
        } else {
            $redirectUrl = self::error_url;
        }

        if(isset($_REQUEST['isCallback'])) {
            return json_encode(array('success' => true));
        } else {
            $this->modx->sendRedirect($redirectUrl);    
        }
        
    }

    public function returnMain()
    {
        $siteUrl = $this->modx->getOption('site_url');
        $this->modx->sendRedirect($siteUrl);
    }

    /**
     * BASE METHOD, DONT USE
     */
    public function receive(msOrder $order)
    {
    }
    private function getGateProdUrl($login,$prefix,$old_prod_url,$new_prod_url) {
        if(strlen($new_prod_url) > 0 && substr($login, 0, strlen($prefix)) == $prefix) {
            return $new_prod_url;
        }
        return $old_prod_url;
    }

    private function updateCallback($data) {
        if(!isset($data['login']) && !isset($data['password'])) {
            return false;
        }

        $data['name'] = str_replace('-api', "", $data['login']);

        $replace_url_part = ['/rest', '/payment'];
        
        if($data['test_mode'] == 1) {
            $gate_url = str_replace($replace_url_part, '', $this->config['gate_url_test']);
            if(substr($gate_url, -1) != '/') { $gate_url .= '/'; }
            if(strripos($gate_url, 'web.rbsuat.com')) {
                $gate_url .= 'mportal/mvc/public/merchant/';
            } else {
                $gate_url .= 'mportal-uat/mvc/public/merchant/';
            }
        } else {
            $gate_url = str_replace($replace_url_part, '', $this->config['gate_url_prod']);
            if(substr($gate_url, -1) != '/') { $gate_url .= '/'; }
            $gate_url .= 'mportal/mvc/public/merchant/';
        } 

        $gate_url .= 'update/' . $data['name'];
        $headers = array(
            'Content-Type:application/json',
            'Authorization: Basic ' . base64_encode($data['login'] . ":" . $data['password']),
        );

        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_VERBOSE => true,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_URL => $gate_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data),
        ));
        $response = curl_exec($ch);
        curl_close($ch);
        if ($this->config['logging']) {
            $message = "\n----============----\n";
            $message .= 'RBS: METHOD callbackUpdate;' . "\n" . 'URL:' . $gate_url . "\n" . 'REQUEST_DATA: ' . "\n" .  print_r($data, 1)  . "\n" .  'REQUEST_RESPONSE:' . "\n" .  print_r($response, 1);
            $message .= "\n";
            $this->modx->log(modX::LOG_LEVEL_ERROR, $message);
        }
    }
    public function isCallbackMode() {
        return $this->config['callback_mode'];
    }

    public function returnGateRequestLog($return_type, $request_data) {
        $message = "\n----============----\n";
        $message .= 'RBS: ' . $return_type . "\n" . 'DATA: ' . "\n" . print_r($request_data, 1);
        $message .= "\n";
        $this->modx->log(modX::LOG_LEVEL_ERROR, $message);
    }
}

