<?php
/*
 * Plugin Name: WooCommerce KuveytTurk Sanal POS Ödeme Sistemi
 * Plugin URI: https://murat.cesmecioglu.net/kuveytpos
 * Description: WooCommerce için KuveytTürk Sanal POS Ödeme Sistemi
 * Author: Murat "MrT" Çeşmecioğlu
 * Author URI: http://murat.cesmecioglu.net
 * Version: 1.2 Free
*/
add_action('plugins_loaded', 'kuveytpos_init_gateway_class', 0);
function kuveytpos_init_gateway_class() {
    if (!class_exists('WC_Payment_Gateway')) return;
    function kuveytpos_add_gateway_class($methods) {
        $methods[] = 'WC_KuveytPos_Gateway';
        return $methods;
    }
    add_filter('woocommerce_payment_gateways', 'kuveytpos_add_gateway_class');
    class WC_KuveytPos_Gateway extends WC_Payment_Gateway {
        function __construct() {
            $this->id = 'kvtposodeme';
            $this->method_title = 'KuveytTürk Sanal POS';
            $this->method_description = 'KuveytTürk Sanal POS Sistemi';
            $this->has_fields = false;
            $this->init_form_fields();
            $this->init_settings();
            $this->title = 'Kredi Kartı İle Ödeme';
            $this->description = 'Kredi kartınızla güvenle ödeme yapın.';
            $this->merchant_id = $this->settings['merchant_id'];
            $this->store_id = $this->settings['store_id'];
            $this->api_user = $this->settings['api_user'];
            $this->api_pass = $this->settings['api_pass'];
            $this->url_paygate = 'https://boa.kuveytturk.com.tr/sanalposservice/Home/ThreeDModelPayGate';
            $this->url_provisiongate = 'https://boa.kuveytturk.com.tr/sanalposservice/Home/ThreeDModelProvisionGate';
            $this->hata_donus = $this->settings['hata_donus'];
            $this->force_redirect = $this->settings['force_redirect'];
            add_action('woocommerce_api_' . $this->id, array(&$this, 'posodeme_callback'));
            add_action('woocommerce_update_options_payment_gateways_' . $this->id, array(&$this, 'process_admin_options'));
            add_action('woocommerce_receipt_' . $this->id, array($this, 'receipt_page'));
        }
        function admin_options() {
            echo '<h3>KuveytTürk Sanal Pos</h3>';
            echo '<p>KuveytTürk Sanal Pos ile ödeme alabilirsiniz.</p>';
            echo '<table class="form-table">';
            $this->generate_settings_html();
            echo '</table>';
            echo '<div><hr><small><a href="https://murat.cesmecioglu.net">Murat Çeşmecioğlu</a><br><sub>KuveytTürk Sanal POS WooCommerce Ödeme Modülü</sub></small></div>';
        }
        function init_form_fields() {
            $this->form_fields = array('enabled' => array('title' => 'Aktif / Pasif', 'type' => 'checkbox', 'label' => 'KuveytTürk Sanal Pos Modülünü Aktive Edin.', 'default' => 'no'), 'merchant_id' => array('title' => 'Müşteri No', 'type' => 'text', 'description' => 'Bankanın verdiği müşteri numarası'), 'store_id' => array('title' => 'Mağaza No', 'type' => 'text', 'description' => 'Bankanın verdiği mağaza kodu'), 'api_user' => array('title' => 'API Kullanıcı Adı', 'type' => 'text', 'description' => 'Bankanın verdiği API kullanıcı adı'), 'api_pass' => array('title' => 'API Kullanıcı Şifresi', 'type' => 'text', 'description' => 'Bankanın verdiği API kullanıcı şifresi'), 'hata_donus' => array('title' => 'Hata Dönüş Sayfası', 'type' => 'select', 'options' => array('sepet' => 'Sepet', 'direkt' => 'Ödeme - Fatura Bilgileri Yok', 'odeme' => 'Ödeme - Fatura Bilgileri Var')), 'force_redirect' => array('title' => 'Banka Yönlendirme', 'type' => 'checkbox', 'label' => 'Banka yönlendirmesini zorla', 'description' => 'Bankanın 3D ödeme sayfasına otomatik yönlendirme yapılmadığı durumlarda aktifleştirin', 'default' => 'no'),);
        }
        function payment_fields() {
            echo $this->generate_kuveytpos_form($order);
        }
        function generate_kuveytpos_form($order_id) {
            return '
      <div class="payment_box payment_method_' . $this->id . '">
      <p class="form-row form-row-wide" id="cc_isim" data-priority="">
        <label for="cc_isim" class="">Kart Üzerindeki İsim <abbr class="required" title="gerekli">*</abbr></label>
        <span class="woocommerce-input-wrapper">
          <input type="text" class="input-text " name="cc_isim" id="cc_isim" placeholder="" value="" required>
        </span>
      </p>
      
      <p class="form-row form-row-wide" id="cc_numara" data-priority="">
        <label for="cc_numara" class="">Kart Numarası <abbr class="required" title="gerekli">*</abbr></label>
        <span class="woocommerce-input-wrapper">
          <input type="text" class="input-text " name="cc_numara" id="cc_numara" placeholder="" value="" pattern="\d*" maxlength="19" required>
        </span>
      </p>
      
      <p class="form-row form-row-wide" id="cc_skt" data-priority="">
        <label for="cc_skt" class="" style="width:100%; clear:both">Son Kullanma Tarihi<abbr class="required" title="gerekli">*</abbr></label>
        <span class="woocommerce-input-wrapper">
          <select required name="cc_sktay" id="cc_sktay" class="select" style="height:40px; float:left; margin: 0 10px 0 0; width: 100px; -webkit-appearance: menulist;" data-allow_clear="true" data-placeholder="Ay" tabindex="-1" aria-hidden="true">
            <option value="">Ay</option>
            <option value="01">1</option>
            <option value="02">2</option>
            <option value="03">3</option>
            <option value="04">4</option>
            <option value="05">5</option>
            <option value="06">6</option>
            <option value="07">7</option>
            <option value="08">8</option>
            <option value="09">9</option>
            <option value="10">10</option>
            <option value="11">11</option>
            <option value="12">12</option>
          </select>
          
          <select required name="cc_sktyil" id="cc_sktyil" class="select" style="height:40px; float:left; width: 100px; -webkit-appearance: menulist;" data-allow_clear="true" data-placeholder="Yıl" tabindex="-1" aria-hidden="true">
            <option value="">Yıl</option>
            <option value="2020">2020</option>
            <option value="2021">2021</option>
            <option value="2022">2022</option>
            <option value="2023">2023</option>
            <option value="2024">2024</option>
            <option value="2025">2025</option>
            <option value="2026">2026</option>
            <option value="2027">2027</option>
            <option value="2028">2028</option>
            <option value="2029">2029</option>
            <option value="2030">2030</option>
            <option value="2031">2031</option>
            <option value="2032">2032</option>
            <option value="2033">2033</option>
            <option value="2034">2034</option>
            <option value="2035">2035</option>
          </select>
        </span>
      </p>
      
      <p class="form-row form-row-wide" id="cc_cvv" data-priority="">
        <label for="cc_cvv" class="">CVV2<abbr class="required" title="gerekli">*</abbr></label>
        <span class="woocommerce-input-wrapper">
          <input type="text" class="input-text " name="cc_cvv" id="cc_cvv" pattern="[0-9]{3,4}" maxlength="4" placeholder="" value="" required>
        </span>
      </p>
      
      </div>
      ';
        }
        function process_payment($order_id) {
            global $woocommerce;
            $order = new WC_Order($order_id);
            $redirect_url = add_query_arg('wc-api', $this->id, get_site_url());
            $hata = 0;
            $Name = $this->get_data('cc_isim');
            $CardNumber = $this->get_data('cc_numara');
            $CardCVV2 = $this->get_data('cc_cvv');
            $CardExpireDateMonth = $this->get_data('cc_sktay');
            $CardExpireDateYear = $this->get_data('cc_sktyil');
            if (empty($CardNumber) || strlen($CardNumber) < 2) {
                wc_add_notice("Kredi kartı üzerindeki isim hatalı", 'error');
                $hata = 1;
            }
            $CardNumber = str_replace(array(' ', '-'), '', $CardNumber);
            if (empty($CardNumber) || !ctype_digit($CardNumber) || !$this->luhn_check($CardNumber)) {
                wc_add_notice("Kredi kartı numarası hatalı", 'error');
                $hata = 1;
            }
            if (empty($CardCVV2) || !ctype_digit($CardCVV2)) {
                wc_add_notice("CVV numarası hatalı", 'error');
                $hata = 1;
            }
            $currentYear = date('Y');
            if (!ctype_digit($CardExpireDateMonth) || !ctype_digit($CardExpireDateYear) || $CardExpireDateMonth > 12 || $CardExpireDateMonth < 1 || $CardExpireDateYear < $currentYear || $CardExpireDateYear > $currentYear + 16) {
                wc_add_notice("Kartın son kullanma tarihi hatalı", 'error');
                $hata = 1;
            }
            else {
                $CardExpireDateMonth = str_pad($CardExpireDateMonth, 2, '0', STR_PAD_LEFT);
                $CardExpireDateYear = substr($CardExpireDateYear, -2);
            }
            $MerchantOrderId = $order_id;
            $toplami = $order->get_total();
            $tlfiyat = number_format($toplami, 2, "", "");
            $Amount = intval($tlfiyat);
            $order->add_order_note("POS Tutar: " . $Amount);
            $OkUrl = $redirect_url;
            $FailUrl = $redirect_url;
            $CustomerId = $this->merchant_id;
            $MerchantId = $this->store_id;
            $UserName = $this->api_user;
            $Password = $this->api_pass;
            $HashedPassword = base64_encode(sha1($Password, "ISO-8859-9"));
            $HashData = base64_encode(sha1($MerchantId . $MerchantOrderId . $Amount . $OkUrl . $FailUrl . $UserName . $HashedPassword, "ISO-8859-9"));
            $xml = '<KuveytTurkVPosMessage xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema">' . '<APIVersion>1.0.0</APIVersion>' . '<OkUrl>' . $OkUrl . '</OkUrl>' . '<FailUrl>' . $FailUrl . '</FailUrl>' . '<HashData>' . $HashData . '</HashData>' . '<MerchantId>' . $MerchantId . '</MerchantId>' . '<CustomerId>' . $CustomerId . '</CustomerId>' . '<UserName>' . $UserName . '</UserName>' . '<CardNumber>' . $CardNumber . '</CardNumber>' . '<CardExpireDateYear>' . $CardExpireDateYear . '</CardExpireDateYear>' . '<CardExpireDateMonth>' . $CardExpireDateMonth . '</CardExpireDateMonth>' . '<CardCVV2>' . $CardCVV2 . '</CardCVV2>' . '<CardHolderName>' . $Name . '</CardHolderName>' . '<CardType>MasterCard</CardType>' . '<BatchID>0</BatchID>' . '<TransactionType>Sale</TransactionType>' . '<InstallmentCount>0</InstallmentCount>' . '<Amount>' . $Amount . '</Amount>' . '<DisplayAmount>' . $Amount . '</DisplayAmount>' . '<CurrencyCode>0949</CurrencyCode>' . '<MerchantOrderId>' . $MerchantOrderId . '</MerchantOrderId>' . '<TransactionSecurity>3</TransactionSecurity>' . '</KuveytTurkVPosMessage>';
            session_start();
            $_SESSION['curlxml'] = $xml;
            if ($hata == 0) {
                return array('result' => 'success', 'redirect' => $order->get_checkout_payment_url(true));
            }
            elseif ($hata == 1) {
                return array('result' => 'failure', 'redirect' => '');
            }
        }
        function receipt_page($order) {
            echo '<p>Siparişiniz için teşekkür ederiz.</p>';
            ob_start();
            session_start();
            global $woocommerce;
            $order = new WC_Order($order_id);
            $curlxml = $_SESSION['curlxml'];
            try {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/xml', 'Content-length: ' . strlen($curlxml)));
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_HEADER, false);
                curl_setopt($ch, CURLOPT_URL, $this->url_paygate);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $curlxml);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $data = curl_exec($ch);
                curl_close($ch);
            }
            catch(Exception $e) {
                echo 'Curl Hatası: ', $e->getMessage(), "\n";
            }
            echo ($data);
            echo "<p>Ödeme için bankaya yönlendiriliyorsunuz</p>";
            $force_redirect = $this->force_redirect;
            if ($force_redirect == "yes") {
                echo '<script language="Javascript">                
              <!--
              function OnLoadEvent() {
                  document.downloadForm.submit();
              }
              //-->
              </script>';
            }
            error_reporting(E_ALL);
            ini_set("display_errors", 1);
        }
        function posodeme_callback() {
            header('HTTP/1.1 200 OK');
            global $woocommerce;
            $AuthenticationResponse = $_POST["AuthenticationResponse"];
            $RequestContent = urldecode($AuthenticationResponse);
            $donus_sayfasi = $this->hata_donus;
            $xxml = simplexml_load_string($RequestContent) or die("Hata: Banka dönüş hatası");
            if ($xxml->ResponseCode == '00') {
                $order = new WC_Order(intval($xxml->VPosMessage->MerchantOrderId));
                $toplami = $order->get_total();
                $tlfiyat = number_format($toplami, 2, "", "");
                $Amount = intval($tlfiyat);
                $MerchantOrderId = $xxml->VPosMessage->MerchantOrderId;
                $MD = $xxml->MD;
                $Type = "Sale";
                $CustomerId = $this->merchant_id;
                $MerchantId = $this->store_id;
                $UserName = $this->api_user;
                $Password = $this->api_pass;
                $HashedPassword = base64_encode(sha1($Password, "ISO-8859-9"));
                $HashData = base64_encode(sha1($MerchantId . $MerchantOrderId . $Amount . $UserName . $HashedPassword, "ISO-8859-9"));
                $xml = '<KuveytTurkVPosMessage xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema">
          <APIVersion>1.0.0</APIVersion>
          <HashData>' . $HashData . '</HashData>
          <MerchantId>' . $MerchantId . '</MerchantId>
          <CustomerId>' . $CustomerId . '</CustomerId>
          <UserName>' . $UserName . '</UserName>
          <TransactionType>Sale</TransactionType>
          <InstallmentCount>0</InstallmentCount>
          <CurrencyCode>0949</CurrencyCode>
          <Amount>' . $Amount . '</Amount>
          <MerchantOrderId>' . $MerchantOrderId . '</MerchantOrderId>
          <TransactionSecurity>3</TransactionSecurity>
          <KuveytTurkVPosAdditionalData>
          <AdditionalData>
            <Key>MD</Key>
            <Data>' . $MD . '</Data>
          </AdditionalData>
        </KuveytTurkVPosAdditionalData>
        </KuveytTurkVPosMessage>';
                try {
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/xml', 'Content-length: ' . strlen($xml)));
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_HEADER, false);
                    curl_setopt($ch, CURLOPT_URL, $this->url_provisiongate);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    $data = curl_exec($ch);
                    curl_close($ch);
                }
                catch(Exception $e) {
                    echo 'Curl Hatası: ', $e->getMessage(), "\n";
                }
                $otorizecevap = simplexml_load_string($data);
                error_reporting(E_ALL);
                ini_set("display_errors", 1);
                if ($otorizecevap->ResponseCode == '00') {
                    $order->payment_complete();
                    $woocommerce->cart->empty_cart();
                    wp_safe_redirect($order->get_checkout_order_received_url());
                    exit;
                }
                else {
                    wc_add_notice('Ödeme Sırasında Banka Hata Mesajı: ' . $otorizecevap->ResponseMessage, 'error');
                    if ($donus_sayfasi == 'sepet') {
                        wp_safe_redirect($woocommerce->cart->get_cart_url());
                    }
                    elseif ($donus_sayfasi == 'direkt') {
                        wp_safe_redirect($order->get_checkout_payment_url(false));
                    }
                    elseif ($donus_sayfasi == 'odeme') {
                        wp_safe_redirect($woocommerce->cart->get_checkout_url());
                    }
                }
            }
            else {
                $order = new WC_Order(intval($xxml->MerchantOrderId));
                wc_add_notice('Banka Kart Doğrulaması Hata Mesaji: ' . $xxml->ResponseMessage, 'error');
                if ($donus_sayfasi == 'sepet') {
                    wp_safe_redirect($woocommerce->cart->get_cart_url());
                }
                elseif ($donus_sayfasi == 'direkt') {
                    wp_safe_redirect($order->get_checkout_payment_url(false));
                }
                elseif ($donus_sayfasi == 'odeme') {
                    wp_safe_redirect($woocommerce->cart->get_checkout_url());
                }
            }
            exit;
        }
        function get_data($name) {
            if (isset($_POST[$name])) {
                return sanitize_text_field($_POST[$name]);
            }
            return null;
        }
        function luhn_check($number) {
            $number = preg_replace('/\D/', '', $number);
            $number_length = strlen($number);
            $parity = $number_length % 2;
            $total = 0;
            for ($i = 0;$i < $number_length;$i++) {
                $digit = $number[$i];
                if ($i % 2 == $parity) {
                    $digit *= 2;
                    if ($digit > 9) {
                        $digit -= 9;
                    }
                }
                $total += $digit;
            }
            return ($total % 10 == 0) ? true : false;
        }
    }
}

