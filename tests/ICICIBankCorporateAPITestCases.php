<?php

namespace Krishna035\ICICIBankCorporateAPI\Tests;

use Krishna035\ICICIBankCorporateAPI\ICICIBankCorporateAPI;

/**
 * Class ICICIBankCorporateAPITestCases
 *
 * A test suite for demonstrating and testing the functionality of the ICICIBankCorporateAPI class.
 * Includes test cases for CIB Registration, Dealer Balance Check, Dealer Collection, and Payment Status Inquiry APIs.
 */
class ICICIBankCorporateAPITestCases
{

    /**
     * Instance of ICICIBankCorporateAPI used for testing.
     *
     * @var ICICIBankCorporateAPI
     */
    private ICICIBankCorporateAPI $objICICI;

    /**
     * Constructor.
     *
     * Initializes the ICICIBankCorporateAPI instance and sets the environment to Sandbox.
     */
    public function __construct()
    {
        $this->objICICI = new ICICIBankCorporateAPI();
        $this->objICICI->environment = 'Sandbox'; // Default environment
    }

    /**
     * Runs the specified test case.
     *
     * @param string $testCase The name of the test case to run.
     * @return void
     */
    public function runTest(string $testCase)
    {
        switch ($testCase) {
            case 'CIBRegistration':
                $this->testCIBRegistration();
                break;

            case 'DealerBalanceCheck':
                $this->testDealerBalanceCheck();
                break;

            case 'DealerCollection':
                $this->testDealerCollection();
                break;

            case 'PaymentStatusInquiry':
                $this->testPaymentStatusInquiry();
                break;

            default:
                echo "Invalid test case: {$testCase}" . PHP_EOL;
                break;
        }
    }

    /**
     * Test case for CIB Registration API.
     *
     * Demonstrates how to register a customer for Corporate Internet Banking (CIB).
     *
     * @return void
     */
    private function testCIBRegistration()
    {
        $payload = [
            'AGGRNAME' => 'AGGRGB',
            'AGGRID' => 'DIP9869',
            'CORPID' => 'PRACHICIB1',
            'USERID' => 'USER3',
            'URN' => '123GB',
        ];

        $this->objICICI->CIBRegistration($payload);

        echo '<pre>', print_r($this->objICICI->requestPayload, true), '</pre>';
        echo '<pre>', print_r($this->objICICI->apiResponse, true), '</pre>';
    }

    /**
     * Test case for Dealer Balance Check API.
     *
     * Demonstrates how to check the balance for a dealer.
     *
     * @return void
     */
    private function testDealerBalanceCheck()
    {
        $payload = [
            'CORPID' => 'PRACHICIB1',
            'USERID' => 'USER3',
            'AGGRID' => 'OTOE0419',
            'AGGRNAME' => 'TEST',
        ];

        $this->objICICI->DealerBalanceCheck($payload);

        echo '<pre>', print_r($this->objICICI->requestPayload, true), '</pre>';
        echo '<pre>', print_r($this->objICICI->apiResponse, true), '</pre>';
    }

    /**
     * Test case for Dealer Collection API.
     *
     * Demonstrates how to process a dealer collection transaction.
     *
     * @return void
     */
    private function testDealerCollection()
    {
        $payload = [
            'CORPID' => 'DDB2023',
            'USERID' => 'USER1',
            'DEBITACCT' => '010205001198',
            'CREDITACCT' => '000705002276',
            'TXN_AMOUNT' => '1',
            'TXN_CURRENCY' => 'INR',
            'URN' => 'ICIC12607',
            'AGGR_ID' => 'AGGR11',
            'UNIQUE_ID' => $this->objICICI->generateRandomKey(50),
            'AGGR_NAME' => 'TESTING',
        ];

        $this->objICICI->DealerCollection($payload);

        echo '<pre>', print_r($this->objICICI->requestPayload, true), '</pre>';
        echo '<pre>', print_r($this->objICICI->apiResponse, true), '</pre>';
    }

    /**
     * Test case for Payment Status Inquiry API.
     *
     * Demonstrates how to inquire about the status of a payment transaction.
     *
     * @return void
     */
    private function testPaymentStatusInquiry()
    {
        $payload = [
            'AGGRID' => 'AGGR11',
            'CORPID' => 'DDB2023',
            'USERID' => 'USER1',
            'UNIQUEID' => 'TpoknbSpoVA',
            'URN' => 'ICIC12607',
        ];

        $this->objICICI->PaymentStatusInquiry($payload);

        echo '<pre>', print_r($this->objICICI->requestPayload, true), '</pre>';
        echo '<pre>', print_r($this->objICICI->apiResponse, true), '</pre>';
    }
}

// Example execution
$testCase = 'DealerCollection'; // Set this to the desired test case
$testRunner = new ICICIBankCorporateAPITestCases();
$testRunner->runTest($testCase);

