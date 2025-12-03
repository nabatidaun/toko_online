<?php

namespace App\Controllers;

use App\Models\Model_review;
use App\Models\Model_barang;
use App\Models\Model_invoice;

class Review extends BaseController
{
    protected $reviewModel;
    protected $barangModel;
    protected $invoiceModel;
    
    public function __construct()
    {
        $this->reviewModel = new Model_review();
        $this->barangModel = new Model_barang();
        $this->invoiceModel = new Model_invoice();
    }
    
    /**
     * Submit review
     */
    public function submit()
    {
        // TODO: Implement authentication check
        // For now, we'll use session user_id (you need to add this after implementing auth)
        $user_id = session()->get('user_id') ?? 1; // Temporary
        
        $product_id = $this->request->getPost('product_id');
        $invoice_id = $this->request->getPost('invoice_id');
        $rating = $this->request->getPost('rating');
        $comment = $this->request->getPost('comment');
        
        // Validation
        $validation = \Config\Services::validation();
        $validation->setRules([
            'product_id' => 'required|integer',
            'invoice_id' => 'required|integer',
            'rating' => 'required|integer|greater_than[0]|less_than[6]',
            'comment' => 'required|min_length[10]|max_length[500]'
        ]);
        
        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Validasi gagal: ' . implode(', ', $validation->getErrors()));
        }
        
        // Check if user can review
        if (!$this->reviewModel->canUserReview($user_id, $product_id, $invoice_id)) {
            return redirect()->back()
                ->with('error', 'Anda tidak dapat memberikan review untuk produk ini.');
        }
        
        // Save review
        $data = [
            'product_id' => $product_id,
            'user_id' => $user_id,
            'invoice_id' => $invoice_id,
            'rating' => $rating,
            'comment' => $comment,
            'verified_purchase' => 1
        ];
        
        if ($this->reviewModel->insert($data)) {
            session()->setFlashdata('success', '✅ Review berhasil ditambahkan! Terima kasih atas feedback Anda.');
        } else {
            session()->setFlashdata('error', '❌ Gagal menambahkan review. Silakan coba lagi.');
        }
        
        return redirect()->to(base_url('pesanan/detail/' . $invoice_id));
    }
    
    /**
     * Mark review as helpful
     */
    public function helpful($review_id)
    {
        $review = $this->reviewModel->find($review_id);
        
        if ($review) {
            $this->reviewModel->update($review_id, [
                'helpful_count' => $review['helpful_count'] + 1
            ]);
            
            return $this->response->setJSON([
                'success' => true,
                'count' => $review['helpful_count'] + 1
            ]);
        }
        
        return $this->response->setJSON(['success' => false]);
    }
}
