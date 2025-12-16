<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use App\Models\Model_barang;
use App\Models\Model_invoice;
use App\Models\Model_kategori;
use App\Models\Model_analytics;
use App\Models\Model_review;
use App\Models\Model_user;

abstract class BaseController extends Controller
{
    protected $request;
    protected $helpers = ['url', 'form', 'custom', 'payment']; // Tambah custom helper
    
    protected $barangModel;
    protected $invoiceModel;
    protected $kategoriModel;
    protected $analyticsModel;
    protected $reviewModel;
    protected $userModel;
    protected $db;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        // Preload models
        $this->barangModel = new Model_barang();
        $this->invoiceModel = new Model_invoice();
        $this->kategoriModel = new Model_kategori();
        $this->analyticsModel = new Model_analytics();
        $this->reviewModel = new Model_review();
        $this->userModel = new Model_user();
        
        // Autoload Database
        $this->db = \Config\Database::connect();
    }
}