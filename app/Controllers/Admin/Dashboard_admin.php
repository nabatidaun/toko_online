<?php

namespace App\Controllers\Admin;
use App\Controllers\BaseController;
use App\Models\Model_analytics;
use App\Models\Model_invoice;

class Dashboard_admin extends BaseController
{
    public function index()
    {
        $analyticsModel = new Model_analytics();
        $invoiceModel = new Model_invoice();
        
        // Ambil semua data analytics
        $data = [
            'title' => 'Dashboard Admin',
            
            // Revenue Data
            'total_revenue' => $analyticsModel->getTotalRevenue(),
            'monthly_revenue' => $analyticsModel->getMonthlyRevenue(),
            'today_revenue' => $analyticsModel->getTodayRevenue(),
            'monthly_growth' => $analyticsModel->getMonthlyGrowth(),
            
            // Order Data
            'total_orders' => $analyticsModel->getTotalOrders(),
            'monthly_orders' => $analyticsModel->getMonthlyOrders(),
            'today_orders' => $analyticsModel->getTodayOrders(),
            
            // Product Data
            'total_products' => $analyticsModel->getTotalProducts(),
            'low_stock_products' => $analyticsModel->getLowStockProducts(),
            'out_of_stock_products' => $analyticsModel->getOutOfStockProducts(),
            
            // Status Data
            'order_status' => $invoiceModel->countByStatus(),
            
            // Chart Data
            'chart_7days' => $analyticsModel->getLast7DaysSales(),
            'chart_monthly' => $analyticsModel->getMonthlySales(),
            
            // Top Products
            'top_products' => $analyticsModel->getTopProducts(5),
            
            // Category Sales
            'category_sales' => $analyticsModel->getCategorySales(),
        ];

        return view('templates_admin/header', $data)
            . view('templates_admin/sidebar')
            . view('admin/dashboard', $data)
            . view('templates_admin/footer');
    }
}