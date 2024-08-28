<?php

/**
 * This script demonstrates the usage of the ICICIBankCorporateAPI class.
 * It includes examples of different API calls such as CIBRegistration, DealerBalanceCheck, DealerCollection, and PaymentStatusInquiry.
 */

// Different Public and Private certificates should be used for encryption/decryption
// Public Key

$testCase = 'DealerCollection';

$objICICI = new ICICIBankCorporateAPI();
$objICICI->environment = 'Sandbox';

/**
 * Switch statement to handle different test cases.
 */
switch ($testCase) {
    /**
     * Test case for CIB Registration API.
     */
    case 'CIBRegistration':
        $payload = array(
            'AGGRNAME' => 'AGGRGB',
            'AGGRID' => 'DIP9869', // Any Random for testing a-z0-9
            'CORPID' => 'PRACHICIB1',
            'USERID' => 'USER3',
            'URN' => '123GB',
        );

        $objICICI->CIBRegistration($payload);

        echo '<pre>'; print_r($objICICI->payload);
        echo '<pre>'; print_r($objICICI->response);

        /*
        Response...
        Array
        (
            [Message] => User details are saved successfully and pending for self approval.
            [Response] => SUCCESS
            [CORP_ID] => PRACHICIB1
            [USER_ID] => USER3
            [AGGR_ID] => DIP9869
            [AGGR_NAME] => AGGRGB
            [URN] => 123GB
        )
            */
        break;

    /**
     * Test case for Dealer Balance Check API.
     */
    case 'DealerBalanceCheck':
        // Multiple balance checks are possible.
        // Single dealer balance check may not work depending on how many accounts are mapped to the user.
        $payload = array(
            'CORPID' => 'PRACHICIB1',
            'USERID' => 'USER3',
            'AGGRID' => 'OTOE0419',
            'AGGRNAME' => 'TEST',
        );

        $objICICI->DealerBalanceCheck($payload);
        echo '<pre>'; print_r($objICICI->payload);
        echo '<pre>'; print_r($objICICI->response);

        /*
        Response...
        Array
        (
            [Record] => Array
                (
                    [AccName] => dcd
                    [NickName] => smallletters
                    [AccNum] => ENCR2777
                    [Currency] => 
                    [Balance] => 0.00
                    [AvailableBalance] => 0.00
                )

            [Response] => Success
        )
            */
        break;

    /**
     * Test case for Dealer Collection API.
     */
    case 'DealerCollection':
        $payload = array(
            'CORPID' => 'DDB2023',
            'USERID' => 'USER1',
            'DEBITACCT' => '010205001198',
            'CREDITACCT' => '000705002276',
            'TXN_AMOUNT' => '1',
            'TXN_CURRENCY' => 'INR',
            'URN' => 'ICIC12607',
            'AGGR_ID' => 'AGGR11',
            'UNIQUE_ID' => $objICICI->generateRandomKey(50), // Any random AlphaNumeric up to 50 characters
            'AGGR_NAME' => 'TESTING',
        );

        $objICICI->DealerCollection($payload);
        echo '<pre>'; print_r($objICICI->payload);
        echo '<pre>'; print_r($objICICI->response);
        /*
        Response...
        Array
        (
            [REQID] => 497373
            [STATUS] => SUCCESS
            [UNIQUEID] => 1RKuvdkZGQJBxMnxonnHQvtHh7PoZLS445sZgBEzmaNV09yRTM
            [URN] => ICIC12607
            [RESPONSE] => SUCCESS
        )
            */
        break;

    /**
     * Test case for Payment Status Inquiry API.
     */
    case 'PaymentStatusInquiry':
        $payload = array(
            'AGGRID' => 'AGGR11',
            'CORPID' => 'DDB2023',
            'USERID' => 'USER1',
            'UNIQUEID' => 'TpoknbSpoVA', // Pass the same unique ID that was used for DealerCollection
            'URN' => 'ICIC12607',
        );

        $objICICI->PaymentStatusInquiry($payload);
        echo '<pre>'; print_r($objICICI->payload);
        echo '<pre>'; print_r($objICICI->response);
        /*
        Response...
        Array
        (
            [REQID] => 497374
            [STATUS] => SUCCESS
            [UNIQUEID] => 5jVxfsiFCqqo0DUPP9JR9Z4A62hh2oK5GI0KBBjg7hixd8O5Vc
            [URN] => ICIC12607
            [RESPONSE] => SUCCESS
        )
            */
        break;

    /**
     * Default case if no valid test case is specified.
     */
    default:
        // Code to handle invalid test case.
        break;
}

exit;
