<?php

namespace App\Models;

use CodeIgniter\Model;

class Model_review extends Model
{
    protected $table = 'tb_review';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'product_id',
        'user_id',
        'invoice_id',
        'rating',
        'comment',
        'verified_purchase',
        'helpful_count'
    ];
    protected $useTimestamps = false;
    
    /**
     * Get reviews by product ID
     */
    public function getReviewsByProduct($product_id, $sort = 'newest')
    {
        $builder = $this->db->table($this->table);
        $builder->select('tb_review.*, tb_user.nama as user_name, tb_invoice.id as invoice_id');
        $builder->join('tb_user', 'tb_user.id = tb_review.user_id', 'left');
        $builder->join('tb_invoice', 'tb_invoice.id = tb_review.invoice_id', 'left');
        $builder->where('tb_review.product_id', $product_id);
        
        switch ($sort) {
            case 'rating_high':
                $builder->orderBy('tb_review.rating', 'DESC');
                break;
            case 'rating_low':
                $builder->orderBy('tb_review.rating', 'ASC');
                break;
            case 'helpful':
                $builder->orderBy('tb_review.helpful_count', 'DESC');
                break;
            default: // newest
                $builder->orderBy('tb_review.created_at', 'DESC');
        }
        
        return $builder->get()->getResultArray();
    }
    
    /**
     * Get average rating for product
     */
    public function getAverageRating($product_id)
    {
        $builder = $this->db->table($this->table);
        $builder->selectAvg('rating', 'avg_rating');
        $builder->selectCount('id', 'total_reviews');
        $builder->where('product_id', $product_id);
        $result = $builder->get()->getRowArray();
        
        return [
            'average' => round($result['avg_rating'] ?? 0, 1),
            'total' => $result['total_reviews'] ?? 0
        ];
    }
    
    /**
     * Get rating distribution
     */
    public function getRatingDistribution($product_id)
    {
        $distribution = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];
        
        $builder = $this->db->table($this->table);
        $builder->select('rating, COUNT(*) as count');
        $builder->where('product_id', $product_id);
        $builder->groupBy('rating');
        $results = $builder->get()->getResultArray();
        
        foreach ($results as $row) {
            $distribution[$row['rating']] = $row['count'];
        }
        
        return $distribution;
    }
    
    /**
     * Check if user already reviewed this product
     */
    public function hasUserReviewed($user_id, $product_id)
    {
        return $this->where('user_id', $user_id)
                    ->where('product_id', $product_id)
                    ->countAllResults() > 0;
    }
    
    /**
     * Check if user can review (purchased and completed)
     */
    public function canUserReview($user_id, $product_id, $invoice_id)
    {
        // Check if already reviewed
        if ($this->hasUserReviewed($user_id, $product_id)) {
            return false;
        }
        
        // Check if product in completed order
        $db = \Config\Database::connect();
        $builder = $db->table('tb_pesanan');
        $builder->join('tb_invoice', 'tb_invoice.id = tb_pesanan.id_invoice');
        $builder->where('tb_pesanan.id_brg', $product_id);
        $builder->where('tb_pesanan.id_invoice', $invoice_id);
        $builder->where('tb_invoice.status', 'selesai');
        
        return $builder->countAllResults() > 0;
    }
}