<?php



namespace Stripe;

/**
 * Class Stripe
 *
 * @package Stripe
 */
class Stripe
{
    // @var string The Stripe API key to be used for requests.
    public static $apiKey;

    // @var string The Stripe client_id to be used for Connect requests.
    public static $clientId;

    // @var string The base URL for the Stripe API.
    public static $apiBase = 'https://api.stripe.com';

    // @var string The base URL for the OAuth API.
    public static $connectBase = 'https://connect.stripe.com';

    // @var string The base URL for the Stripe API uploads endpoint.
    public static $apiUploadBase = 'https://uploads.stripe.com';

    // @var string|null The version of the Stripe API to use for requests.
    public static $apiVersion = null;

    // @var string|null The account ID for connected accounts requests.
    public static $accountId = null;

    // @var boolean Defaults to true.
    public static $verifySslCerts = true;

    // @var array The application's information (name, version, URL)
    public static $appInfo = null;

    // @var Util\LoggerInterface|null The logger to which the library will
    //   produce messages.
    public static $logger = null;

    const VERSION = '5.5.0';

    /**
     * @return string The API key used for requests.
     */
    public static function getApiKey()
    {
        return self::$apiKey;
    }

    /**
     * @return string The client_id used for Connect requests.
     */
    public static function getClientId()
    {
        return self::$clientId;
    }

    /**
     * @return Util\LoggerInterface The logger to which the library will
     *   produce messages.
     */
    public static function getLogger()
    {
        if (self::$logger == null) {
            return new Util\DefaultLogger();
        }
        return self::$logger;
    }

    /**
     * @param Util\LoggerInterface $logger The logger to which the library
     *   will produce messages.
     */
    public static function setLogger($logger)
    {
        self::$logger = $logger;
    }

    /**
     * Sets the API key to be used for requests.
     *
     * @param string $apiKey
     */
    public static function setApiKey($apiKey)
    {
        self::$apiKey = $apiKey;
    }

    /**
     * Sets the client_id to be used for Connect requests.
     *
     * @param string $clientId
     */
    public static function setClientId($clientId)
    {
        self::$clientId = $clientId;
    }

    /**
     * @return string The API version used for requests. null if we're using the
     *    latest version.
     */
    public static function getApiVersion()
    {
        return self::$apiVersion;
    }

    /**
     * @param string $apiVersion The API version to use for requests.
     */
    public static function setApiVersion($apiVersion)
    {
        self::$apiVersion = $apiVersion;
    }

    /**
     * @return boolean
     */
    public static function getVerifySslCerts()
    {
        return self::$verifySslCerts;
    }

    /**
     * @param boolean $verify
     */
    public static function setVerifySslCerts($verify)
    {
        self::$verifySslCerts = $verify;
    }

    /**
     * @return string | null The Stripe account ID for connected account
     *   requests.
     */
    public static function getAccountId()
    {
        return self::$accountId;
    }

    /**
     * @param string $accountId The Stripe account ID to set for connected
     *   account requests.
     */
    public static function setAccountId($accountId)
    {
        self::$accountId = $accountId;
    }

    /**
     * @return array | null The application's information
     */
    public static function getAppInfo()
    {
        return self::$appInfo;
    }

    /**
     * @param string $appName The application's name
     * @param string $appVersion The application's version
     * @param string $appUrl The application's URL
     */
    public static function setAppInfo($appName, $appVersion = null, $appUrl = null)
    {
        if (self::$appInfo === null) {
            self::$appInfo = array();
        }
        self::$appInfo['name'] = $appName;
        self::$appInfo['version'] = $appVersion;
        self::$appInfo['url'] = $appUrl;
    }
}



// Utilities
require(__DIR__.'/Util/AutoPagingIterator.php');
require(__DIR__.'/Util/LoggerInterface.php');
require(__DIR__.'/Util/DefaultLogger.php');
require(__DIR__.'/Util/RequestOptions.php');
require(__DIR__.'/Util/Set.php');
require(__DIR__.'/Util/Util.php');

// HttpClient
require(__DIR__.'/HttpClient/ClientInterface.php');
require(__DIR__.'/HttpClient/CurlClient.php');

// Errors
require(__DIR__.'/Error/Base.php');
require(__DIR__.'/Error/Api.php');
require(__DIR__.'/Error/ApiConnection.php');
require(__DIR__.'/Error/Authentication.php');
require(__DIR__.'/Error/Card.php');
require(__DIR__.'/Error/InvalidRequest.php');
require(__DIR__.'/Error/Permission.php');
require(__DIR__.'/Error/RateLimit.php');
require(__DIR__.'/Error/SignatureVerification.php');

// OAuth errors
require(__DIR__.'/Error/OAuth/OAuthBase.php');
require(__DIR__.'/Error/OAuth/InvalidClient.php');
require(__DIR__.'/Error/OAuth/InvalidGrant.php');
require(__DIR__.'/Error/OAuth/InvalidRequest.php');
require(__DIR__.'/Error/OAuth/InvalidScope.php');
require(__DIR__.'/Error/OAuth/UnsupportedGrantType.php');
require(__DIR__.'/Error/OAuth/UnsupportedResponseType.php');

// Plumbing
require(__DIR__.'/ApiResponse.php');
require(__DIR__.'/JsonSerializable.php');
require(__DIR__.'/StripeObject.php');
require(__DIR__.'/ApiRequestor.php');
require(__DIR__.'/ApiResource.php');
require(__DIR__.'/SingletonApiResource.php');
require(__DIR__.'/AttachedObject.php');
require(__DIR__.'/ExternalAccount.php');

// Stripe API Resources
require(__DIR__.'/Account.php');
require(__DIR__.'/AlipayAccount.php');
require(__DIR__.'/ApplePayDomain.php');
require(__DIR__.'/ApplicationFee.php');
require(__DIR__.'/ApplicationFeeRefund.php');
require(__DIR__.'/Balance.php');
require(__DIR__.'/BalanceTransaction.php');
require(__DIR__.'/BankAccount.php');
require(__DIR__.'/BitcoinReceiver.php');
require(__DIR__.'/BitcoinTransaction.php');
require(__DIR__.'/Card.php');
require(__DIR__.'/Charge.php');
require(__DIR__.'/Collection.php');
require(__DIR__.'/CountrySpec.php');
require(__DIR__.'/Coupon.php');
require(__DIR__.'/Customer.php');
require(__DIR__.'/Dispute.php');
require(__DIR__.'/EphemeralKey.php');
require(__DIR__.'/Event.php');
require(__DIR__.'/FileUpload.php');
require(__DIR__.'/Invoice.php');
require(__DIR__.'/InvoiceItem.php');
require(__DIR__.'/LoginLink.php');
require(__DIR__.'/Order.php');
require(__DIR__.'/OrderReturn.php');
require(__DIR__.'/Payout.php');
require(__DIR__.'/Plan.php');
require(__DIR__.'/Product.php');
require(__DIR__.'/Recipient.php');
require(__DIR__.'/RecipientTransfer.php');
require(__DIR__.'/Refund.php');
require(__DIR__.'/SKU.php');
require(__DIR__.'/Source.php');
require(__DIR__.'/SourceTransaction.php');
require(__DIR__.'/Subscription.php');
require(__DIR__.'/SubscriptionItem.php');
require(__DIR__.'/ThreeDSecure.php');
require(__DIR__.'/Token.php');
require(__DIR__.'/Transfer.php');
require(__DIR__.'/TransferReversal.php');

// OAuth
require(__DIR__.'/OAuth.php');

// Webhooks
require(__DIR__.'/Webhook.php');
require(__DIR__.'/WebhookSignature.php');


