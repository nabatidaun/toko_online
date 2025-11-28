<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Midtrans extends BaseConfig
{
    /**
     * Midtrans Server Key
     * Get from .env file
     */
    public string $serverKey;
    
    /**
     * Midtrans Client Key
     * Get from .env file
     */
    public string $clientKey;
    
    /**
     * Environment: sandbox atau production
     */
    public string $environment;
    
    /**
     * Enable 3D Secure
     */
    public bool $is3ds = true;
    
    /**
     * Callback URLs
     */
    public string $notificationUrl = '';
    public string $finishUrl = '';
    public string $unfinishUrl = '';
    public string $errorUrl = '';

    public function __construct()
    {
        parent::__construct();
        
        // Load from environment variables
        $this->serverKey = getenv('midtrans.serverKey') ?: '';
        $this->clientKey = getenv('midtrans.clientKey') ?: '';
        $this->environment = getenv('midtrans.environment') ?: 'sandbox';
        
        // Set URL callbacks
        $baseUrl = base_url();
        $this->notificationUrl = $baseUrl . 'payment/notification';
        $this->finishUrl = $baseUrl . 'payment/finish';
        $this->unfinishUrl = $baseUrl . 'payment/unfinish';
        $this->errorUrl = $baseUrl . 'payment/error';
    }
}