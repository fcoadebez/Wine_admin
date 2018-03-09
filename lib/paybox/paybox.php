<?php

    // for insights see https://github.com/swoelffel/woocommerce-paybox-gateway/blob/master/woocommerce_paybox_gateway.class.php

    if ( ! function_exists('paybox_config') ) {
        /**
         * Provide configuration parameters
         * 
         * @return array
         */
        function paybox_config() {
            $configarray = array(
                'FriendlyName' => array( 'Type' => "System", 'Value' => "PayBox" ),
                'environment'  => array( 'FriendlyName' => paybox_lang('paybox_conf_environment'), 'Type' => "dropdown", 'Options' => "test,production" ),
                'site_number'  => array( 'FriendlyName' => paybox_lang('paybox_conf_site_number'), 'Type' => "text", 'Value' => "", 'Size' => 20 ),
                'rank_number'  => array( 'FriendlyName' => paybox_lang('paybox_conf_rank_number'), 'Type' => "text", 'Value' => "", 'Size' => 20 ),
                'merchant_id'  => array( 'FriendlyName' => paybox_lang('paybox_conf_merchant_id'), 'Type' => "text", 'Value' => "", 'Size' => 20 ),
                'secret_key'   => array( 'FriendlyName' => paybox_lang('paybox_conf_merchant_secret'), 'Type' => "textarea", 'Rows' => 5 ),
            );
            return $configarray;
        }
    }
    
    if ( ! function_exists('paybox_refund') ) {
        /**
         * Perform refund transaction
         * 
         * @param array $params
         * @return array
         */
        function paybox_refund( $params ) {
            
            $invoiceid  = $params['invoiceid'];
            
            $url        = paybox_get_service_url('unsubscribe', 'production');
            $postdata   = array();
            $postdata['VERSION']       = '001';
            $postdata['TYPE']          = '001';
            $postdata['SITE']          = paybox_get_conf('site_number');
            $postdata['MACH']          = str_pad(paybox_get_conf('rank_number'), 3, '0', STR_PAD_LEFT);
            $postdata['IDENTIFIANT']   = paybox_get_conf('merchant_id');
            
            $result = full_query("SELECT tblhosting.subscriptionid subscr_id FROM tblinvoiceitems INNER JOIN tblhosting ON tblhosting.id=tblinvoiceitems.relid WHERE tblinvoiceitems.invoiceid=".(int)$invoiceid." AND tblinvoiceitems.type='hosting'");
            $item   = mysql_fetch_assoc($result);
            if ( empty($item['subscr_id']) ) {
                $postdata['REFERENCE'] = $invoiceid;
            } else {
                $postdata['ABONNEMENT']  = $item['subscr_id'];
            }
            $postdata['PBX_HASH'] = 'SHA512';
            $postdata['TIME'] = date('c');
	
            // computing HMAC
            $msg = '';
            foreach( $postdata as $k => $v) {
                $msg .= ($msg ? '&' : '') . "{$k}={$v}";
            }
            $postdata['HMAC'] = strtoupper(hash_hmac('sha512', $msg, pack("H*", paybox_get_conf('secret_key'))));

            $result = curlCall( $url, $postdata );
            parse_str($result, $params);

            if( empty($params['ACQ']) ) {
                return array( 'status' => 'error', 'rawdata' => $params );
            }
            if ( $params['ACQ'] == 'NO' ) {
                return array( 'status' => 'error', 'rawdata' => $params );
            }
            if ( $params['ACQ'] == 'OK' ) {
                if ( empty($item['subscr_id']) ) {
                    update_query('tblhosting', array('subscriptionid' => ''), array('subscriptionid' => $item['subscr_id']));    
                }
                return array( 'status' => 'success', 'rawdata' => $params );
            }
        }
    }   
    
    if ( ! function_exists('paybox_link') ) {
        /**
         * Provide payment form to redirect customer to gateway website
         * 
         * @return string
         */
		 

		 
        function paybox_link( $params ) {
            global $_LANG;
            
		 $transactionID = time() * rand (1,99);
		 $transactionID = substr($transactionID, -4);
			
            if ( ! paybox_get_conf('secret_key') ) {
                return paybox_lang('error_missing_secret_key');
            }
            if ( ! paybox_get_conf('site_number') ) {
                return paybox_lang('error_missing_site_number');
            }
            if ( ! paybox_get_conf('rank_number') ) {
                return paybox_lang('error_missing_rank_number');
            }
            if ( ! paybox_get_conf('merchant_id') ) {
                return paybox_lang('error_missing_merchant_id');
            }
            
            $invoiceid = $params['invoiceid'];
            
            $recurrings = getRecurringBillingValues( $invoiceid );
            $primaryserviceid = $recurrings['primaryserviceid'];
            $firstpaymentamount = $recurrings['firstpaymentamount'];
            $firstcycleperiod = $recurrings['firstcycleperiod'];
            $firstcycleunits = strtoupper( substr( $recurrings['firstcycleunits'], 0, 1 ) );
            $recurringamount = $recurrings['recurringamount'];
            $recurringcycleperiod = $recurrings['recurringcycleperiod'];
            $recurringcycleunits = strtoupper( substr( $recurrings['recurringcycleunits'], 0, 1 ) );
            
            $subnotpossible = false;

            if (!$recurrings) {
                $subnotpossible = true;
            }
            
            if ($recurrings['overdue']) {
                $subnotpossible = true;
            }

            if ($params['forceonetime']) {
                $subnotpossible = true;
            }
            
            if ($recurringamount <= 0) {
                $subnotpossible = true;
            }
            
            if (90 < $firstcycleperiod && $firstcycleunits == "D") {
                $subnotpossible = true;
            }

            if (24 < $firstcycleperiod && $firstcycleunits == "M") {
                $subnotpossible = true;
            }

            if (5 < $firstcycleperiod && $firstcycleunits == "Y") {
                $subnotpossible = true;
            }
            
            if ( $firstcycleunits == 'Y' ) {
                $recurringcycleperiod *= 12;
            }

            $amount         = $params['amount']*100;
            $currency       = paybox_currency_num( $params['currency'] );
            
            $environment    = paybox_get_conf('environment');
            $form_action    = paybox_get_service_url('classic', 'production');
            
            $secret_key     = paybox_get_conf('secret_key');
            $server         = parse_url($form_action, PHP_URL_HOST);
            
            $PBX_RETOUR     = 'amount:M;invoice_id:R;auth_num:A;error:E;country:I;txn_id:S;txn_date:W;txn_time:Q;subscr_id:B;card_exp:D;payment_type:P;sign:K';
            $PBX_TIME       = date('c');
            $PBX_RUF1       = 'POST';
            
            $msg            = "PBX_SITE="           . paybox_get_conf('site_number');
            $msg            .= "&PBX_RANG="         . paybox_get_conf('rank_number');
            $msg            .= "&PBX_IDENTIFIANT="  . paybox_get_conf('merchant_id');            
            
            if (!$subnotpossible) {
                				
                $PBX_CMD        = $params['invoiceid'].'-'.$transactionID;
                $PBX_CMD        .= 'IBS_2MONT'          . str_pad($recurringamount*100, 10, '0', STR_PAD_LEFT);
                $PBX_CMD        .= 'IBS_NBPAIE00';
                $PBX_CMD        .= 'IBS_FREQ'           . str_pad($recurringcycleperiod, 2, '0', STR_PAD_LEFT);
                $PBX_CMD        .= 'IBS_QUAND00';
                $PBX_CMD        .= 'IBS_DELAIS000';
                        
                $msg            .= "&PBX_TOTAL="        . ($recurringamount*100);
                $msg            .= "&PBX_DEVISE="       . $currency;
                $msg            .= "&PBX_CMD="          . $PBX_CMD;
                $msg            .= "&PBX_PORTEUR="      . $params['clientdetails']['email'];
                $msg            .= "&PBX_RETOUR="       . $PBX_RETOUR;
                $msg            .= "&PBX_REPONDRE_A="   . $params['systemurl'] . 'modules/gateways/paybox/ipn.php';
                $msg            .= "&PBX_EFFECTUE="     . $params['returnurl'] . '&paymentsuccess=true';
                $msg            .= "&PBX_REFUSE="       . $params['returnurl'] . '&paymentfailed=true';
                $msg            .= "&PBX_ANNULE="       . $params['returnurl'] . '&paymentfailed=true';
                $msg            .= "&PBX_ATTENTE="      . $params['returnurl'] . '&paymentsuccess=true';
                $msg            .= "&PBX_RUF1="         . $PBX_RUF1;
                $msg            .= "&PBX_HASH=SHA512";
                $msg            .= "&PBX_TIME="         . $PBX_TIME;

                $hmac           = strtoupper(hash_hmac('sha512', $msg, pack("H*", $secret_key)));

                $code           = '<table><tr><td>';
                $code           .= '<form action="' . $form_action . '" method="POST" name="paymentfrm">';
                $code           .= '<input type="hidden" name="PBX_SITE" value="'           . paybox_get_conf('site_number') . '">';
                $code           .= '<input type="hidden" name="PBX_RANG" value="'           . paybox_get_conf('rank_number') . '">';
                $code           .= '<input type="hidden" name="PBX_IDENTIFIANT" value="'    . paybox_get_conf('merchant_id') . '">';
                $code           .= '<input type="hidden" name="PBX_TOTAL" value="'          . ($recurringamount*100) . '">';
                $code           .= '<input type="hidden" name="PBX_DEVISE" value="'         . $currency . '">';
                $code           .= '<input type="hidden" name="PBX_CMD" value="'            . $PBX_CMD . '">';
                $code           .= '<input type="hidden" name="PBX_PORTEUR" value="'        . $params['clientdetails']['email'] . '">';
                $code           .= '<input type="hidden" name="PBX_RETOUR" value="'         . $PBX_RETOUR . '">';
                $code           .= '<input type="hidden" name="PBX_REPONDRE_A" value="'     . $params['systemurl'] . 'modules/gateways/paybox/ipn.php">';
                $code           .= '<input type="hidden" name="PBX_EFFECTUE" value="'       . $params['returnurl'] . '&paymentsuccess=true">';
                $code           .= '<input type="hidden" name="PBX_REFUSE" value="'         . $params['returnurl'] . '&paymentfailed=true">';
                $code           .= '<input type="hidden" name="PBX_ANNULE" value="'         . $params['returnurl'] . '&paymentfailed=true">';
                $code           .= '<input type="hidden" name="PBX_ATTENTE" value="'        . $params['returnurl'] . '&paymentsuccess=true">';
                $code           .= '<input type="hidden" name="PBX_RUF1" value="'           . $PBX_RUF1 . '">';
                $code           .= '<input type="hidden" name="PBX_HASH" value="SHA512">';
                $code           .= '<input type="hidden" name="PBX_TIME" value="'           . $PBX_TIME . '">';
                $code           .= '<input type="hidden" name="PBX_HMAC" value="'           . $hmac . '">';
                $code           .= '<input type="submit" value="'                           . $_LANG['invoicespaynow'] . '">';
                $code           .= '</form>';
                $code           .= '</td></tr></table>';
                
            } else {
                
                $msg            .= "&PBX_TOTAL="        . $amount;
                $msg            .= "&PBX_DEVISE="       . $currency;
                $msg            .= "&PBX_CMD="          . $params['invoiceid'].'-'.$transactionID;;
                $msg            .= "&PBX_PORTEUR="      . $params['clientdetails']['email'];
                $msg            .= "&PBX_RETOUR="       . $PBX_RETOUR;
                $msg            .= "&PBX_REPONDRE_A="   . $params['systemurl'] . 'modules/gateways/paybox/ipn.php';
                $msg            .= "&PBX_EFFECTUE="     . $params['returnurl'] . '&paymentsuccess=true';
                $msg            .= "&PBX_REFUSE="       . $params['returnurl'] . '&paymentfailed=true';
                $msg            .= "&PBX_ANNULE="       . $params['returnurl'] . '&paymentfailed=true';
                $msg            .= "&PBX_ATTENTE="      . $params['returnurl'] . '&paymentsuccess=true';
                $msg            .= "&PBX_RUF1="         . $PBX_RUF1;
                $msg            .= "&PBX_HASH=SHA512";
                $msg            .= "&PBX_TIME="         . $PBX_TIME;

                $hmac           = strtoupper(hash_hmac('sha512', $msg, pack("H*", $secret_key)));

                $code           = '<table><tr><td>';
                $code           .= '<form action="' . $form_action . '" method="POST" name="paymentfrm">';
                $code           .= '<input type="hidden" name="PBX_SITE" value="'           . paybox_get_conf('site_number') . '">';
                $code           .= '<input type="hidden" name="PBX_RANG" value="'           . paybox_get_conf('rank_number') . '">';
                $code           .= '<input type="hidden" name="PBX_IDENTIFIANT" value="'    . paybox_get_conf('merchant_id') . '">';
                $code           .= '<input type="hidden" name="PBX_TOTAL" value="'          . $amount . '">';
                $code           .= '<input type="hidden" name="PBX_DEVISE" value="'         . $currency . '">';
                $code           .= '<input type="hidden" name="PBX_CMD" value="'            . $params['invoiceid'].'-'.$transactionID. '">';
                $code           .= '<input type="hidden" name="PBX_PORTEUR" value="'        . $params['clientdetails']['email'] . '">';
                $code           .= '<input type="hidden" name="PBX_RETOUR" value="'         . $PBX_RETOUR . '">';
                $code           .= '<input type="hidden" name="PBX_REPONDRE_A" value="'     . $params['systemurl'] . 'modules/gateways/paybox/ipn.php">';
                $code           .= '<input type="hidden" name="PBX_EFFECTUE" value="'       . $params['returnurl'] . '&paymentsuccess=true">';
                $code           .= '<input type="hidden" name="PBX_REFUSE" value="'         . $params['returnurl'] . '&paymentfailed=true">';
                $code           .= '<input type="hidden" name="PBX_ANNULE" value="'         . $params['returnurl'] . '&paymentfailed=true">';
                $code           .= '<input type="hidden" name="PBX_ATTENTE" value="'        . $params['returnurl'] . '&paymentsuccess=true">';
                $code           .= '<input type="hidden" name="PBX_RUF1" value="'           . $PBX_RUF1 . '">';
                $code           .= '<input type="hidden" name="PBX_HASH" value="SHA512">';
                $code           .= '<input type="hidden" name="PBX_TIME" value="'           . $PBX_TIME . '">';
                $code           .= '<input type="hidden" name="PBX_HMAC" value="'           . $hmac . '">';
                $code           .= '<input type="submit" value="'                           . $_LANG['invoicespaynow'] . '">';
                $code           .= '</form>';
                $code           .= '</td></tr></table>';
                
            }
            
            return $code;
        }
    }
    
    if ( ! function_exists('paybox_get_conf') ) {
        function paybox_get_conf( $name ) {
            global $whmcs;
            static $_conf = array();
            if( empty($_conf) ) {
                $_conf = getGatewayVariables("paybox");
            }
            $name = trim($name);
            if( isset($_conf[$name]) ) {
                return $_conf[$name];
            }
            $_conf[$name] = get_query_val('tblpaymentgateways', 'value', array( 'gateway' => "paybox", 'setting' => $name ), '', '', 1);
            return $_conf[$name];
        }
    }
    
    if ( ! function_exists('paybox_lang') ) {
        /**
         * Get current admin translation for given language key
         * 
         * @staticvar array $_LANG
         * @param string $key
         * @return null|string
         */
        function paybox_lang( $key ) {
            static $_LANG = array();
            if( empty($_LANG) ) {
                $lang = !empty($_SESSION['adminlang']) ? $_SESSION['adminlang'] : 'english';
                if( is_readable(dirname(__FILE__).'/paybox/lang/' . $lang . '.php') ) {
                    require_once dirname(__FILE__).'/paybox/lang/' . $lang . '.php';
                } elseif( is_readable(dirname(__FILE__).'/paybox/lang/english.php') ) {
                    require_once dirname(__FILE__).'/paybox/lang/english.php';
                }
            }
            if( isset( $_LANG[ $key ] ) ) {
                return $_LANG[ $key ];
            }
        }
    }
    
    if ( ! function_exists('paybox_get_service_url') ) {
        /**
         * 
         * @param string $type
         * @param string $plateform
         * @return string
         */
        function paybox_get_service_url( $type, $plateform ) {
            $urls = array(
                'classic'       => array( 
                                    'test'       => "https://preprod-tpeweb.paybox.com/cgi/MYchoix_pagepaiement.cgi", 
                                    'production' => "https://tpeweb.paybox.com/cgi/MYchoix_pagepaiement.cgi", 
                                    'fallback'   => "https://tpeweb1.paybox.com/cgi/MYchoix_pagepaiement.cgi" 
                                ),
                'light'         => array( 
                                    'test'       => "https://preprod-tpeweb.paybox.com/cgi/MYframepagepaiement_ip.cgi", 
                                    'production' => "https://tpeweb.paybox.com/cgi/MYframepagepaiement_ip.cgi", 
                                    'fallback'   => "https://tpeweb1.paybox.com/cgi/MYframepagepaiement_ip.cgi" 
                                ),
                'mobile'        => array( 
                                    'test'       => "https://preprod-tpeweb.paybox.com/cgi/ChoixPaiementMobile.cgi", 
                                    'production' => "https://tpeweb.paybox.com/cgi/ChoixPaiementMobile.cgi", 
                                    'fallback'   => "https://tpeweb1.paybox.com/cgi/ChoixPaiementMobile.cgi" 
                                ),
                'unsubscribe'   => array( 
                                    'test'       => "https://preprod-tpeweb.paybox.com/cgi-bin/ResAbon.cgi", 
                                    'production' => "https://tpeweb.paybox.com/cgi-bin/ResAbon.cgi", 
                                    'fallback'   => "https://tpeweb1.paybox.com/cgi-bin/ResAbon.cgi" 
                                ),
            );
            if ( !empty( $urls[ $type ][ $plateform ] ) ) {
                return $urls[ $type ][ $plateform ];
            }
        }
    }
    
    if ( ! function_exists('paybox_currency_num') ) {
        /**
         * Provide ISO 4217 currency code number
         * 
         * @param string $code
         * @return int
         */
        function paybox_currency_num( $code ) {
            $_currencies = array (
                'AED' => '784',
                'AFN' => '971',
                'ALL' => '008',
                'AMD' => '051',
                'ANG' => '532',
                'AOA' => '973',
                'ARS' => '032',
                'AUD' => '036',
                'AWG' => '533',
                'AZN' => '944',
                'BAM' => '977',
                'BBD' => '052',
                'BDT' => '050',
                'BGN' => '975',
                'BHD' => '048',
                'BIF' => '108',
                'BMD' => '060',
                'BND' => '096',
                'BOB' => '068',
                'BOV' => '984',
                'BRL' => '986',
                'BSD' => '044',
                'BTN' => '064',
                'BWP' => '072',
                'BYR' => '974',
                'BZD' => '084',
                'CAD' => '124',
                'CDF' => '976',
                'CHE' => '947',
                'CHF' => '756',
                'CHW' => '948',
                'CLF' => '990',
                'CLP' => '152',
                'CNY' => '156',
                'COP' => '170',
                'COU' => '970',
                'CRC' => '188',
                'CUC' => '931',
                'CUP' => '192',
                'CVE' => '132',
                'CZK' => '203',
                'DJF' => '262',
                'DKK' => '208',
                'DOP' => '214',
                'DZD' => '012',
                'EGP' => '818',
                'ERN' => '232',
                'ETB' => '230',
                'EUR' => '978',
                'FJD' => '242',
                'FKP' => '238',
                'GBP' => '826',
                'GEL' => '981',
                'GHS' => '936',
                'GIP' => '292',
                'GMD' => '270',
                'GNF' => '324',
                'GTQ' => '320',
                'GYD' => '328',
                'HKD' => '344',
                'HNL' => '340',
                'HRK' => '191',
                'HTG' => '332',
                'HUF' => '348',
                'IDR' => '360',
                'ILS' => '376',
                'INR' => '356',
                'IQD' => '368',
                'IRR' => '364',
                'ISK' => '352',
                'JMD' => '388',
                'JOD' => '400',
                'JPY' => '392',
                'KES' => '404',
                'KGS' => '417',
                'KHR' => '116',
                'KMF' => '174',
                'KPW' => '408',
                'KRW' => '410',
                'KWD' => '414',
                'KYD' => '136',
                'KZT' => '398',
                'LAK' => '418',
                'LBP' => '422',
                'LKR' => '144',
                'LRD' => '430',
                'LSL' => '426',
                'LTL' => '440',
                'LYD' => '434',
                'MAD' => '504',
                'MDL' => '498',
                'MGA' => '969',
                'MKD' => '807',
                'MMK' => '104',
                'MNT' => '496',
                'MOP' => '446',
                'MRO' => '478',
                'MUR' => '480',
                'MVR' => '462',
                'MWK' => '454',
                'MXN' => '484',
                'MXV' => '979',
                'MYR' => '458',
                'MZN' => '943',
                'NAD' => '516',
                'NGN' => '566',
                'NIO' => '558',
                'NOK' => '578',
                'NPR' => '524',
                'NZD' => '554',
                'OMR' => '512',
                'PAB' => '590',
                'PEN' => '604',
                'PGK' => '598',
                'PHP' => '608',
                'PKR' => '586',
                'PLN' => '985',
                'PYG' => '600',
                'QAR' => '634',
                'RON' => '946',
                'RSD' => '941',
                'RUB' => '643',
                'RWF' => '646',
                'SAR' => '682',
                'SBD' => '090',
                'SCR' => '690',
                'SDG' => '938',
                'SEK' => '752',
                'SGD' => '702',
                'SHP' => '654',
                'SLL' => '694',
                'SOS' => '706',
                'SRD' => '968',
                'SSP' => '728',
                'STD' => '678',
                'SYP' => '760',
                'SZL' => '748',
                'THB' => '764',
                'TJS' => '972',
                'TMT' => '934',
                'TND' => '788',
                'TOP' => '776',
                'TRY' => '949',
                'TTD' => '780',
                'TWD' => '901',
                'TZS' => '834',
                'UAH' => '980',
                'UGX' => '800',
                'USD' => '840',
                'USN' => '997',
                'USS' => '998',
                'UYI' => '940',
                'UYU' => '858',
                'UZS' => '860',
                'VEF' => '937',
                'VND' => '704',
                'VUV' => '548',
                'WST' => '882',
                'XAF' => '950',
                'XAG' => '961',
                'XAU' => '959',
                'XBA' => '955',
                'XBB' => '956',
                'XBC' => '957',
                'XBD' => '958',
                'XCD' => '951',
                'XDR' => '960',
                'XOF' => '952',
                'XPD' => '964',
                'XPF' => '953',
                'XPT' => '962',
                'XSU' => '994',
                'XTS' => '963',
                'XUA' => '965',
                'XXX' => '999',
                'YER' => '886',
                'ZAR' => '710',
                'ZMW' => '967',
                'ZWD' => '932',
            );
            return ( !empty($_currencies[ $code ]) ? $_currencies[ $code ] : 840 );
        }
    }
    
    

