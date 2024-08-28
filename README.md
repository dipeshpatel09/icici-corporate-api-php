# ICICI Bank Corporate API Integration

This repository provides a PHP implementation for integrating with the ICICI Bank Corporate API. It includes a core API class `ICICIBankCorporateAPI` for handling various ICICI Bank services such as CIB Registration, Dealer Balance Check, Dealer Collection, and Payment Status Inquiry. Additionally, a sample script is provided to demonstrate how to use the API class.

## Table of Contents

- [Installation](#installation)
- [Usage](#usage)
  - [Configuration](#configuration)
  - [Example Test Cases](#example-test-cases)
- [API Methods](#api-methods)
  - [CIB Registration](#cib-registration)
  - [Dealer Balance Check](#dealer-balance-check)
  - [Dealer Collection](#dealer-collection)
  - [Payment Status Inquiry](#payment-status-inquiry)
- [Contributing](#contributing)
- [License](#license)

## Installation

1. Clone this repository to your local environment:
   ```bash
   git clone https://github.com/krishna035/icici-bank-corporate-api-php.git
   ```

2. Ensure you have the necessary SSL certificates and public keys for ICICI Bank's Sandbox and Live environments.

3. Set up your server or local environment to run PHP scripts.

## Usage

### Configuration

Before using the API, you must configure your credentials and paths in the `ICICIBankCorporateAPI` class.

1. **Client ID and Client Secret**: Replace the placeholders with your actual Client ID and Client Secret for both Sandbox and Live environments.
   ```php
   $this->clientId = 'YOUR_SANDBOX_CLIENT_ID';
   $this->clientSecret = 'YOUR_SANDBOX_CLIENT_SECRET';
   ```

2. **Certificate Paths**: Replace the placeholder paths with the actual file paths where your certificates and public keys are stored.
   ```php
   $this->caCertPath = '/path/to/your/certificates/YOUR_SANDBOX_CERTIFICATE.pem';
   $this->publicKey = file_get_contents('/path/to/your/certificates/YOUR_SANDBOX_PUBLIC_KEY.cer');
   ```

### Example Test Cases

The `ICICICorporateAPITestCases.php` script provides example usage of the `ICICIBankCorporateAPI` class. It includes several test cases for common API requests.

1. **Running the Example Script**:
   - Open `ICICICorporateAPITestCases.php`.
   - Modify the `$testCase` variable to select the desired test case (e.g., `'CIBRegistration'`, `'DealerBalanceCheck'`, etc.).
   - Run the script on your server or local environment:
     ```bash
     php ICICICorporateAPITestCases.php
     ```
   - The script will output the request payload and the API response.

## API Methods

### CIB Registration

Registers a customer for Corporate Internet Banking (CIB).

- **Method**: `CIBRegistration($payload)`
- **Payload Parameters**:
  - `AGGRNAME`: Aggregator Name
  - `AGGRID`: Aggregator ID (random for testing)
  - `CORPID`: Corporate ID
  - `USERID`: User ID
  - `URN`: Unique Reference Number

### Dealer Balance Check

Checks the balance for a dealer.

- **Method**: `DealerBalanceCheck($payload)`
- **Payload Parameters**:
  - `CORPID`: Corporate ID
  - `USERID`: User ID
  - `AGGRID`: Aggregator ID
  - `AGGRNAME`: Aggregator Name

### Dealer Collection

Processes a dealer collection transaction.

- **Method**: `DealerCollection($payload)`
- **Payload Parameters**:
  - `CORPID`: Corporate ID
  - `USERID`: User ID
  - `DEBITACCT`: Debit Account Number
  - `CREDITACCT`: Credit Account Number
  - `TXN_AMOUNT`: Transaction Amount
  - `TXN_CURRENCY`: Transaction Currency (e.g., `INR`)
  - `URN`: Unique Reference Number
  - `AGGR_ID`: Aggregator ID
  - `UNIQUE_ID`: Unique Transaction ID (use the `generateRandomKey` method to create this)

### Payment Status Inquiry

Inquires about the status of a payment transaction.

- **Method**: `PaymentStatusInquiry($payload)`
- **Payload Parameters**:
  - `AGGRID`: Aggregator ID
  - `CORPID`: Corporate ID
  - `USERID`: User ID
  - `UNIQUEID`: Unique Transaction ID (same as used in `DealerCollection`)
  - `URN`: Unique Reference Number

## Contributing

We welcome contributions to improve this library! Please fork this repository and submit a pull request with your changes.