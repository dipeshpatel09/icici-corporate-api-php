<?php

/**
 * Class ICICIBankCorporateAPI
 *
 * Handles ICICI Bank Corporate API integration including encryption, payload handling, and API requests.
 */
class ICICIBankCorporateAPI {
    private $clientId = '';
    private $clientSecret = '';
    private $encryptionMethod = '';
    private $caCertPath = '';
    private $publicKey = '';
    private $initializationVector = '';
    private $ivLength = '';
    private $secretKey = '';
    public $requestPayload = array();
    public $apiResponse = array();
	public $debug = false;
    public $debugLogData = '';

	/**
     * ICICIBankCorporateAPI constructor.
     *
     * Initializes the API client, sets environment-specific variables, and prepares encryption parameters.
     */
	public function __construct() {
        $this->requestPayload = array();
        $this->apiResponse = array();
		$this->debug = true;
        $this->debugLogData = '';
		$this->APIEndPoint = '';
        $this->environment = 'Sandbox'; // Set to 'Live' for production server

        $this->encryptionMethod = 'AES-256-CBC';

		if ($this->environment == 'Sandbox') {
            $this->caCertPath = '/etc/ssl/certs/YOUR_SANDBOX_CERTIFICATE.pem';
            $this->publicKey = file_get_contents('/etc/ssl/certs/YOUR_SANDBOX_PUBLIC_KEY.cer');
            $this->clientId = 'YOUR_SANDBOX_CLIENT_ID';
            $this->clientSecret = 'YOUR_SANDBOX_CLIENT_SECRET';
		}

		if ($this->environment == 'Live') {
            $this->caCertPath = '/etc/ssl/certs/YOUR_LIVE_CERTIFICATE.pem';
            $this->publicKey = file_get_contents('/etc/ssl/certs/YOUR_LIVE_PUBLIC_KEY.cer');
            $this->clientId = 'YOUR_LIVE_CLIENT_ID';
            $this->clientSecret = 'YOUR_LIVE_CLIENT_SECRET';
		}

        $this->initializationVector = $this->generateRandomKey(16);
        $this->ivLength = openssl_cipher_iv_length($this->encryptionMethod);
        $this->secretKey = $this->generateRandomKey(32);

        $this->debugLogData .= chr(13) . 'Start ' . date('d-M-Y h:i:s') . chr(13);
        $this->debugLogData .= chr(13) . 'API mode ' . $this->environment . chr(13);
	}

	/**
     * ICICIBankCorporateAPI destructor.
     *
     * Handles the logging of debug data and rotates log files if they exceed the specified size.
     */
	public function __destruct() {
        $this->debugLogData .= chr(13) . chr(13) . 'End ' . date('d-M-Y h:i:s') . chr(13);
        $this->debugLogData .= chr(13) . '===================================================================================' . chr(13);
        $GLOBALS['log']->fatal($this->debugLogData);

		if ($this->debug) {
            $logFilePath = 'debug.log';
            if ($this->getFileSize($logFilePath, 'MB') > 2) {
                @rename($logFilePath, $logFilePath . '.bk');
			}
            $fp = fopen($logFilePath, 'a+');
            fwrite($fp, $this->debugLogData);
			fclose($fp);
		}
	}

	/**
     * Get the size of a file in the specified unit.
     *
     * @param string $file Path to the file.
     * @param string $type Size unit ('KB', 'MB', 'GB').
     * @return float File size in the specified unit.
     */
    public function getFileSize($file, $type) {
		switch ($type) {
			case 'KB':
                $fileSize = filesize($file) * .0009765625;
				break;
			case 'MB':
                $fileSize = (filesize($file) * .0009765625) * .0009765625;
				break;
			case 'GB':
                $fileSize = ((filesize($file) * .0009765625) * .0009765625) * .0009765625;
				break;
		}
        if ($fileSize <= 0) {
            return 0;
		} else {
            return round($fileSize, 2);
		}
	}

	/**
     * Generate a random key of the specified length.
     *
     * @param int $length Length of the key to generate.
     * @return string Randomly generated key.
     */
    public function generateRandomKey($length) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
        $randomKey = '';

		for ($i = 0; $i < $length; $i++) {
            $randomKey .= $characters[random_int(0, $charactersLength - 1)];
		}

        return $randomKey;
	}

	/**
     * Encrypts a plaintext string using the predefined encryption method and secret key.
     *
     * @param string $plainText The plaintext to be encrypted.
     * @return string Base64-encoded ciphertext.
     */
    private function encrypt($plainText) {
        $iv = openssl_random_pseudo_bytes($this->ivLength);
        $cipherText = openssl_encrypt($plainText, $this->encryptionMethod, $this->secretKey, OPENSSL_RAW_DATA, $this->initializationVector);

        return base64_encode($this->initializationVector . $cipherText);
	}

	/**
     * Decrypts an encrypted string using the predefined encryption method and secret key.
     *
     * @param string $encrypted The Base64-encoded ciphertext to be decrypted.
     * @return string The decrypted plaintext.
     */
	private function decrypt($encrypted) {
		$data = base64_decode($encrypted);
        $iv = substr($data, 0, $this->ivLength);
        $cipherText = substr($data, $this->ivLength);

        return openssl_decrypt($cipherText, $this->encryptionMethod, $this->secretKey, OPENSSL_RAW_DATA, $this->initializationVector);
	}

	/**
     * Encrypts the session key using the ICICI Bank public key.
     *
     * @return string Base64-encoded encrypted session key.
     */
	private function getSessionKey() {
        openssl_public_encrypt($this->secretKey, $encryptedData, $this->publicKey);

		return base64_encode($encryptedData);
	}

	/**
     * Recursively encrypts a nested array payload.
     *
     * @param array $nestedArray The nested array to encrypt.
     * @return array The encrypted array.
     */
    private function encryptPayload($nestedArray) {
		$encryptedArray = array();
		foreach ($nestedArray as $key => $value) {
			if (is_array($value)) {
                $encryptedArray[$key] = $this->encryptPayload($value);
			} else {
				$encryptedArray[$key] = $this->encrypt($value);
			}
		}

		return $encryptedArray;
	}

	/**
     * Sets and encrypts the API request payload.
     *
     * @param array $data The data to be set as the payload.
     */
    private function setRequestPayload($data) {
        $this->debugLogData .= chr(13) . 'Payload Data....' . chr(13);
        $this->debugLogData .= var_export($data, true);

        $this->requestPayload = $this->encryptPayload($data);
	}

	/**
     * Decrypts the encrypted API response data.
     *
     * @param string $encrypted The Base64-encoded encrypted response data.
     * @return string The decrypted response data.
     */
    private function decryptResponseData($encrypted) {
		$data = base64_decode($encrypted);
        $iv = substr($data, 0, $this->ivLength);
        $cipherText = substr($data, $this->ivLength);

        return openssl_decrypt($cipherText, $this->encryptionMethod, $this->secretKey, OPENSSL_RAW_DATA, $iv);
	}

	/**
     * Registers a customer for Corporate Internet Banking (CIB) using the provided payload.
     *
     * @param array $payload The data to be sent in the API request.
     * @return array The API response.
     */
	public function CIBRegistration($payload) {
        $this->debugLogData .= chr(13) . __METHOD__ . chr(13);

		if ($this->environment == 'Sandbox') {
			$this->APIEndPoint = 'https://uat-onprem-dmz-hybrid.icicibank.com/apibanking/live/corpapi/v2/cib/Registration';
		}
		if ($this->environment == 'Live') {
			$this->APIEndPoint = 'https://igateway.icicibank.com/apibanking/live/corpapi/v2/cib/Registration';
		}

        $this->setRequestPayload($payload);
        $this->executeRequest();

        return $this->apiResponse;
	}

	/**
     * Checks the balance of a dealer using the provided payload.
     *
     * @param array $payload The data to be sent in the API request.
     * @return array The API response.
     */
	public function DealerBalanceCheck($payload) {
        $this->debugLogData .= chr(13) . __METHOD__ . chr(13);

		if ($this->environment == 'Sandbox') {
			$this->APIEndPoint = 'https://uat-onprem-dmz-hybrid.icicibank.com/apibanking/live/corpapi/v2/cib/DealerBalanceCheck';
		}
		if ($this->environment == 'Live') {
			$this->APIEndPoint = 'https://igateway.icicibank.com/apibanking/live/corpapi/v2/cib/DealerBalanceCheck';
		}

        $this->setRequestPayload($payload);
        $this->executeRequest();

        return $this->apiResponse;
	}

	/**
     * Inquires about the status of a payment using the provided payload.
     *
     * @param array $payload The data to be sent in the API request.
     * @return array The API response.
     */
	public function PaymentStatusInquiry($payload) {
        $this->debugLogData .= chr(13) . __METHOD__ . chr(13);

		if ($this->environment == 'Sandbox') {
            $this->APIEndPoint = 'https://uat-onprem-dmz-hybrid.icicibank.com/apibanking/live/corpapi/v2/cib/transaction/status';
		}
		if ($this->environment == 'Live') {
			$this->APIEndPoint = 'https://igateway.icicibank.com/apibanking/live/corpapi/v2/cib/transaction/status';
		}

        $this->setRequestPayload($payload);
        $this->executeRequest();

        return $this->apiResponse;
	}

	/**
     * Processes a dealer collection using the provided payload.
     *
     * @param array $payload The data to be sent in the API request.
     * @return array The API response.
     */
	public function DealerCollection($payload) {
        $this->debugLogData .= chr(13) . __METHOD__ . chr(13);

		if ($this->environment == 'Sandbox') {
			$this->APIEndPoint = 'https://uat-onprem-dmz-hybrid.icicibank.com/apibanking/live/corpapi/v2/cib/DealerCollection';
		}
		if ($this->environment == 'Live') {
			$this->APIEndPoint = 'https://igateway.icicibank.com/apibanking/live/corpapi/v2/cib/DealerCollection';
		}

        $this->setRequestPayload($payload);
        $this->executeRequest();

        return $this->apiResponse;
	}

	/**
     * Recursively decrypts a nested array response.
     *
     * @param array $nestedArray The nested array to decrypt.
     * @return array The decrypted array.
     */
	private function decryptResponse($nestedArray) {
		$decryptArray = array();
		foreach ($nestedArray as $key => $value) {
			if (is_array($value)) {
				$decryptArray[$key] = $this->decryptResponse($value);
			} else {
                $decryptArray[$key] = $this->decryptResponseData($value);
			}
		}

		return $decryptArray;
	}

	/**
     * Executes the API request using cURL and processes the response.
     *
     * @return string The raw API response.
     */
    private function executeRequest() {
		if (empty($this->APIEndPoint)) {
            $this->debugLogData .= chr(13) . 'API end point not found' . chr(13);
		}

        if (empty($this->clientId)) {
            $this->debugLogData .= chr(13) . 'Client ID not found' . chr(13);

			return false;
		}

		$ch = curl_init();
        curl_setopt($ch, CURLOPT_CAINFO, $this->caCertPath);
		curl_setopt($ch, CURLOPT_URL, $this->APIEndPoint);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($this->requestPayload));

		$headers = array();
		$headers[] = 'Content-Type: application/json';
        $headers[] = 'X-IBM-Client-Id: ' . $this->clientId;
        $headers[] = 'X-IBM-Client-Secret: ' . $this->clientSecret;
		$headers[] = 'X-Session-Key: ' . $this->getSessionKey();

		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		$result = curl_exec($ch);

		if ($this->debug) {
            $this->debugLogData .= chr(13) . chr(13) . var_export($result, true);
		}

        $this->apiResponse = $this->decryptResponse(json_decode($result, true));

        $this->debugLogData .= chr(13) . chr(13) . var_export($this->apiResponse, true);

		if (curl_errno($ch)) {
            $this->debugLogData .= chr(13) . 'Error:' . curl_error($ch);
		}
		curl_close($ch);

		return $result;
	}
}