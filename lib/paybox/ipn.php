<?php
    // error_reporting(E_ALL);
    file_put_contents('ipn-logs.txt', print_r($_REQUEST, true), FILE_APPEND);

    if ( !empty($_REQUEST['invoice_id']) && !empty($_REQUEST['auth_num']) && !empty($_REQUEST['error']) && !empty($_REQUEST['txn_id']) ) {
        require_once __DIR__ . '/../../../init.php';
		require_once __DIR__ . '/../../../includes/gatewayfunctions.php';
		require_once __DIR__ . '/../../../includes/invoicefunctions.php';
        $GATEWAY = getGatewayVariables("paybox");
        
        error_reporting(E_ALL);
        ini_set('display_errors', true);
        
        if ( ! headers_sent() ) {
            header('Content-Type: text/html');
        }
        
        if ( empty($GATEWAY['type']) ) {
            die('Module Not Activated');
        }
        
        // pending payment
        if( $_REQUEST['error']=='99999' ) {
            logTransaction('Paybox', $_REQUEST, 'Pending');
            logActivity("Paybox IPN - Invoice #{$_REQUEST['invoice_id']} - Pending payment");
            die( 'Pending payment logged' );
        }
        
        // failed payment
        if( $_REQUEST['error']!='00000' ) { 
            logTransaction('Paybox', $_REQUEST, 'Failed'. ' - Reason: ' . paybox_get_error_msg($_REQUEST['error']));
            logActivity("Paybox IPN - Invoice #{$_REQUEST['invoice_id']} - Failed payment - Reason: ".paybox_get_error_msg($_REQUEST['error']));
            die( 'Failed payment logged' );
        }
        
        # successful payment
        
        // ensure we didnt already processed this transaction
        checkCbTransID($_REQUEST['txn_id']);
        
		// On enlève le numéro aléatoire pour éviter erreur duplication
		$decoupe_id = explode("-", $_REQUEST['invoice_id']);
		
        $invoice_id = $decoupe_id[0];
        $txn_id     = $_REQUEST['txn_id'];
        $amount     = round($_REQUEST['amount']/100, 2);
        
        // instant payment
        if( empty($_POST['subscr_id']) ) {
            
            // ensure invoice exists
            checkCbInvoiceID($invoice_id, 'Paybox');
                    
            // get invoice record
            $result = select_query('tblinvoices', '', array('id' => $invoice_id));
            $data   = mysql_fetch_assoc($result);
            $userid = $data['userid'];
            $total  = $data['total'];
            $status = strtolower($data['status']);

            // check payment status is unpaid
            if ( $status !== 'unpaid' ) {
                logTransaction('Paybox', $_REQUEST, 'Already processed');
                logActivity("Paybox IPN - Invoice #{$_REQUEST['invoice_id']} - Payment already processed");
                die( 'Already processed' );
            }
            
            // check paid amount is correct
            if ( $amount < $total  ) {
                logTransaction('Paybox', $_REQUEST, 'Incorrect amount');
                logActivity("Paybox IPN - Invoice #{$_REQUEST['invoice_id']} - Received incorrect amount");
                die( 'Incorrect amount' );
            }
            
            // create invoice
            addInvoicePayment($invoice_id, $txn_id, $amount, 0, 'paybox');

            logTransaction('Paybox', $_REQUEST, 'Successful');
            logActivity("Paybox IPN - Invoice #{$_REQUEST['invoice_id']} - Successful payment");
            
            die('IPN Processed');
        }
        
        // subscription
        $subscr_id = $_POST['subscr_id'];
        
        // parse invoice ID
        if( preg_match('#^(\d+)IBS_2MONT#', trim($invoice_id), $m) ) {
            $invoice_id = (int)$m[1];
            logActivity("Paybox IPN - Invoice #$invoice_id - Parsed invoice number from {$_REQUEST['invoice_id']}");
        }
        
        // ensure invoice exits
        checkCbInvoiceID($invoice_id, 'Paybox');
        
        // get invoice record
        $result = select_query('tblinvoices', '', array('id' => $invoice_id));
        $data   = mysql_fetch_assoc($result);
        $userid = $data['userid'];
        $total  = $data['total'];
        $status = strtolower($data['status']);
        
        $recurrings = getRecurringBillingValues( $invoice_id );
        $firstpaymentamount = $recurrings['firstpaymentamount'];
        $recurringamount = $recurrings['recurringamount'];
        
        // first payment
        if( empty($_REQUEST['ETAT_PBX']) ) { 
            
            // check payment status is unpaid
            if ( $status !== 'unpaid' ) {
                logTransaction('Paybox', $_REQUEST, 'Already processed');
                logActivity("Paybox IPN - Invoice #$invoice_id - Subscription #$subscr_id - Payment already processed");
                die( 'Already processed' );
            }
            
            // check paid amount is correct
            if ( $amount < $firstpaymentamount  ) {
                logTransaction('Paybox', $_REQUEST, 'Incorrect amount');
                logActivity("Paybox IPN - Invoice #$invoice_id - Subscription #$subscr_id - Received incorrect amount");
                die( 'Incorrect amount' );
            }
            
            // create invoice
            addInvoicePayment($invoice_id, $txn_id, $amount, 0, 'paybox');
            logTransaction('Paybox', $_REQUEST, 'Successful');
            logActivity("Paybox IPN - Invoice #$invoice_id - Subscription #$subscr_id - Successful payment");

            // save subscription id
            $result = select_query("tblinvoiceitems", "", array("invoiceid" => $invoice_id, "type" => "Hosting"));
            $data   = mysql_fetch_array($result);
            $relid  = $data['relid'];
            update_query("tblhosting", array("subscriptionid" => $subscr_id), array("id" => $relid));
            
            logActivity("Paybox IPN - Invoice #$invoice_id - Subscription #$subscr_id - Subscription ID saved for hosting #$relid");
            
            die('IPN Processed'); 
        }
        
        // subsequent subscription payment
        if( $_REQUEST['ETAT_PBX']==='PBX_RECONDUCTION_ABT' ) {
            
            // check paid amount is correct
            if ( $amount < $recurringamount  ) {
                logTransaction('Paybox', $_REQUEST, 'Incorrect amount');
                logActivity("Paybox IPN - Invoice #$invoice_id - Subscription #$subscr_id - Received incorrect amount");
                die( 'IPN Processed' );
            }
            
            // create invoice
            addInvoicePayment($invoice_id, $txn_id, $amount, 0, 'paybox');
            logTransaction('Paybox', $_REQUEST, 'Successful');
            logActivity("Paybox IPN - Invoice #$invoice_id - Subscription #$subscr_id - Successfully payment");
            die('IPN Processed');
        }
        
        // cancel subscription
        update_query('tblhosting', array('subscriptionid' => ''), array('subscriptionid' => $subscr_id));
        logTransaction('Paybox', $_REQUEST, 'Subscription Cancelled');
        logActivity("Paybox IPN - Invoice #$invoice_id - Subscription #$subscr_id - Subscription cancelled");
        
        die('IPN processed!');
        
    }
    
    if ( ! function_exists('paybox_get_error_msg') ) {
        
        function paybox_get_error_msg($code) {
            $msg = "";
            switch ($code) {
                case '00000':
                    $msg = 'Opération réussie.';
                    break;
                case '00001':
                    $msg = 'La connexion au centre d\'autorisation a échoué. Vous pouvez dans ce cas là effectuer les redirections des internautes vers le FQDN' . ' tpeweb1.paybox.com.';
                    break;
                case '00002':
                    $msg = 'Une erreur de cohérence est survenue.';
                    break;
                case '00003':
                    $msg = 'Erreur Paybox.';
                    break;
                case '00004':
                    $msg = 'Numéro de porteur ou crytogramme visuel invalide.';
                    break;
                case '00006':
                    $msg = 'Accès refusé ou site/rang/identifiant incorrect.';
                    break;
                case '00008':
                    $msg = 'Date de fin de validité incorrecte.';
                    break;
                case '00009':
                    $msg = 'Erreur de création d\'un abonnement.';
                    break;
                case '00010':
                    $msg = 'Devise inconnue.';
                    break;
                case '00011':
                    $msg = 'Montant incorrect.';
                    break;
                case '00015':
                    $msg = 'Paiement déjà effectué';
                    break;
                case '00016':
                    $msg = 'Abonné déjà existant (inscription nouvel abonné). Valeur \'U\' de la variable PBX_RETOUR.';
                    break;
                case '00021':
                    $msg = 'Carte non autorisée.';
                    break;
                case '00029':
                    $msg = 'Carte non conforme. Code erreur renvoyé lors de la documentation de la variable « PBX_EMPREINTE ».';
                    break;
                case '00030':
                    $msg = 'Temps d\'attente > 15 mn par l\'internaute/acheteur au niveau de la page de paiements.';
                    break;
                case '00031':
                case '00032':
                    $msg = 'Réservé';
                    break;
                case '00033':
                    $msg = 'Code pays de l\'adresse IP du navigateur de l\'acheteur non autorisé.';
                    break;
                // Nouveaux codes : 11/2013 (v6.1)
                case '00040':
                    $msg = 'Opération sans authentification 3-DSecure, bloquée par le filtre';
                    break;
                case '99999':
                    $msg = 'Opération en attente de validation par l\'emmetteur du moyen de paiement.';
                    break;
                default:
                    if (substr($code_erreur, 0, 3) == '001')
                        $msg = 'Paiement refusé par le centre d\'autorisation. En cas d\'autorisation de la transaction par le centre d\'autorisation de la banque, le code erreur \'00100\' sera en fait remplacé directement par \'00000\'.';
                    else
                        $msg = 'Pas de message';
                    break;
            }
            return $msg;
        }
    }
    
