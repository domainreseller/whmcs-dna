<?php
/**
 * Created by PhpStorm.
 * User: bunyaminakcay
 * Project name php-dna
 * 20.11.2022
 * Bünyamin AKÇAY <bunyamin@bunyam.in>
 */

/**
 * Class DomainNameAPI_PHPLibrary
 * Facade that routes API calls to the appropriate implementation (REST or SOAP)
 * based on the username format:
 *   - UUID username → DNARest (REST API)
 *   - Normal username → DNASoap (SOAP API)
 *
 * @package DomainNameApi
 * @version 3.0.1
 */

namespace DomainNameApi;

require_once __DIR__ . '/SharedApiConfigAndUtilsTrait.php';

use Exception;

class DomainNameAPI_PHPLibrary
{
    use SharedApiConfigAndUtilsTrait;

    /**
     * The underlying API client (DNARest or DNASoap)
     * @var object|null
     */
    private ?object $client = null;

    /**
     * DomainNameAPI_PHPLibrary constructor.
     *
     * @param string $userName API username for authentication (UUID for REST, regular for SOAP)
     * @param string $password API password/token for authentication
     * @param bool $testmode Enable test/OTE environment (SOAP only)
     * @throws Exception When connection fails
     */
    public function __construct($userName = "ownername", $password = "ownerpass", $testmode = false)
    {
        if ($this->looksLikeUuid($userName)) {
            require_once __DIR__ . '/DNARest.php';
            $this->client = new DNARest($userName, $password);
        } else {
            require_once __DIR__ . '/DNASoap.php';
            $this->client = new DNASoap($userName, $password, $testmode);
        }
    }

    /**
     * Check if a string looks like a UUID
     *
     * @param string $value Value to check
     * @return bool True if value matches UUID format
     */
    private function looksLikeUuid(string $value): bool
    {
        return (bool) preg_match(
            '/^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12}$/',
            $value
        );
    }

    /**
     * Magic method to delegate method calls to the underlying client.
     * Supports both PascalCase and camelCase method calls.
     *
     * @param string $name Method name
     * @param array $arguments Method arguments
     * @return mixed
     * @throws Exception
     */
    public function __call($name, $arguments)
    {
        // Convert PascalCase to camelCase
        $camelCaseName = lcfirst($name);

        if (method_exists($this->client, $camelCaseName)) {
            return call_user_func_array([$this->client, $camelCaseName], $arguments);
        }

        if (method_exists($this->client, $name)) {
            return call_user_func_array([$this->client, $name], $arguments);
        }

        throw new Exception("Method {$name} does not exist");
    }

    /**
     * Magic method to delegate property access to the underlying client.
     *
     * @param string $name Property name
     * @return mixed
     * @throws Exception
     */
    public function __get($name)
    {
        if (property_exists($this->client, $name)) {
            return $this->client->$name;
        }
        throw new Exception("Property {$name} does not exist");
    }

    /**
     * Magic method to delegate property setting to the underlying client.
     *
     * @param string $name Property name
     * @param mixed $value Property value
     * @throws Exception
     */
    public function __set($name, $value)
    {
        if (property_exists($this->client, $name)) {
            $this->client->$name = $value;
            return;
        }
        throw new Exception("Property {$name} does not exist");
    }

    /**
     * Magic method to support isset() checks on client properties.
     *
     * @param string $name Property name
     * @return bool
     */
    public function __isset($name)
    {
        return property_exists($this->client, $name) && isset($this->client->$name);
    }

    // Explicit delegations for IDE autocompletion and documentation

    /**
     * @see DNASoap::getResellerDetails()
     * @see DNARest::getResellerDetails()
     */
    public function getResellerDetails() { return $this->client->getResellerDetails(); }

    /**
     * @see DNASoap::getCurrentBalance()
     * @see DNARest::getCurrentBalance()
     */
    public function getCurrentBalance($currencyId = 'USD') { return $this->client->getCurrentBalance($currencyId); }

    /**
     * @see DNASoap::checkAvailability()
     * @see DNARest::checkAvailability()
     */
    public function checkAvailability($domains, $extensions, $period, $command) { return $this->client->checkAvailability($domains, $extensions, $period, $command); }

    /**
     * @see DNASoap::getList()
     * @see DNARest::getList()
     */
    public function getList($extra_parameters = []) { return $this->client->getList($extra_parameters); }

    /**
     * @see DNASoap::getTldList()
     * @see DNARest::getTldList()
     */
    public function getTldList($count = 20) { return $this->client->getTldList($count); }

    /**
     * @see DNASoap::getDetails()
     * @see DNARest::getDetails()
     */
    public function getDetails($domainName) { return $this->client->getDetails($domainName); }

    /**
     * @see DNASoap::modifyNameServer()
     * @see DNARest::modifyNameServer()
     */
    public function modifyNameServer($domainName, $nameServers) { return $this->client->modifyNameServer($domainName, $nameServers); }

    /**
     * @see DNASoap::enableTheftProtectionLock()
     * @see DNARest::enableTheftProtectionLock()
     */
    public function enableTheftProtectionLock($domainName) { return $this->client->enableTheftProtectionLock($domainName); }

    /**
     * @see DNASoap::disableTheftProtectionLock()
     * @see DNARest::disableTheftProtectionLock()
     */
    public function disableTheftProtectionLock($domainName) { return $this->client->disableTheftProtectionLock($domainName); }

    /**
     * @see DNASoap::addChildNameServer()
     * @see DNARest::addChildNameServer()
     */
    public function addChildNameServer($domainName, $nameServer, $ipAddress) { return $this->client->addChildNameServer($domainName, $nameServer, $ipAddress); }

    /**
     * @see DNASoap::deleteChildNameServer()
     * @see DNARest::deleteChildNameServer()
     */
    public function deleteChildNameServer($domainName, $nameServer) { return $this->client->deleteChildNameServer($domainName, $nameServer); }

    /**
     * @see DNASoap::modifyChildNameServer()
     * @see DNARest::modifyChildNameServer()
     */
    public function modifyChildNameServer($domainName, $nameServer, $ipAddress) { return $this->client->modifyChildNameServer($domainName, $nameServer, $ipAddress); }

    /**
     * @see DNASoap::getContacts()
     * @see DNARest::getContacts()
     */
    public function getContacts($domainName) { return $this->client->getContacts($domainName); }

    /**
     * @see DNASoap::saveContacts()
     * @see DNARest::saveContacts()
     */
    public function saveContacts($domainName, $contacts) { return $this->client->saveContacts($domainName, $contacts); }

    /**
     * @see DNASoap::transfer()
     * @see DNARest::transfer()
     */
    public function transfer($domainName, $eppCode, $period) { return $this->client->transfer($domainName, $eppCode, $period); }

    /**
     * @see DNASoap::cancelTransfer()
     * @see DNARest::cancelTransfer()
     */
    public function cancelTransfer($domainName) { return $this->client->cancelTransfer($domainName); }

    /**
     * @see DNASoap::approveTransfer()
     * @see DNARest::approveTransfer()
     */
    public function approveTransfer($domainName) { return $this->client->approveTransfer($domainName); }

    /**
     * @see DNASoap::rejectTransfer()
     * @see DNARest::rejectTransfer()
     */
    public function rejectTransfer($domainName) { return $this->client->rejectTransfer($domainName); }

    /**
     * @see DNASoap::renew()
     * @see DNARest::renew()
     */
    public function renew($domainName, $period) { return $this->client->renew($domainName, $period); }

    /**
     * @see DNASoap::registerWithContactInfo()
     * @see DNARest::registerWithContactInfo()
     */
    public function registerWithContactInfo($domainName, $period, $contacts, $nameServers = [], $eppLock = true, $privacyLock = false, $addionalAttributes = []) { return $this->client->registerWithContactInfo($domainName, $period, $contacts, $nameServers, $eppLock, $privacyLock, $addionalAttributes); }

    /**
     * @see DNASoap::modifyPrivacyProtectionStatus()
     * @see DNARest::modifyPrivacyProtectionStatus()
     */
    public function modifyPrivacyProtectionStatus($domainName, $status, $reason = 'Owner request') { return $this->client->modifyPrivacyProtectionStatus($domainName, $status, $reason); }

    /**
     * @see DNASoap::syncFromRegistry()
     * @see DNARest::syncFromRegistry()
     */
    public function syncFromRegistry($domainName) { return $this->client->syncFromRegistry($domainName); }

    /**
     * @see DNASoap::checkTransfer()
     * @see DNARest::checkTransfer()
     */
    public function checkTransfer($domainName, $authcode) { return $this->client->checkTransfer($domainName, $authcode); }

    // Getters and Setters

    public function getRequestData() { return $this->client->getRequestData(); }
    public function setRequestData($request) { $this->client->setRequestData($request); return $this; }
    public function getResponseData() { return $this->client->getResponseData(); }
    public function setResponseData($response) { $this->client->setResponseData($response); return $this; }
    public function getResponseHeaders() { return $this->client->getResponseHeaders(); }
    public function setResponseHeaders($headers) { $this->client->setResponseHeaders($headers); return $this; }
    public function getParsedResponseData() { return $this->client->getParsedResponseData(); }
    public function setParsedResponseData($response) { $this->client->setParsedResponseData($response); return $this; }
    public function getLastFunction() { return $this->client->getLastFunction(); }
    public function setLastFunction($function) { $this->client->setLastFunction($function); return $this; }
    public function getServiceUrl() { return $this->client->getServiceUrl(); }
    public function setServiceUrl($url) { $this->client->setServiceUrl($url); }

    // Utility methods

    public function validateContact($contact) { return $this->client->validateContact($contact); }
    public function isTrTLD($domain) { return $this->client->isTrTLD($domain); }
}
