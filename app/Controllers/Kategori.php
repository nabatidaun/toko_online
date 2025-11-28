<?php
namespace App\Controllers;
use App\Models\Model_kategori;

class kategori extends BaseController{
    protected $Model_kategori;

    public function poster()
    {
        $data['poster'] = $this->kategoriModel->data_poster();

        return view('templates/header')
            . view('templates/sidebar')
            . view('poster', $data)
            . view('templates/footer');
    }

    public function pamflet()
    {
        $data['pamflet'] = $this->kategoriModel->data_pamflet();

        return view('templates/header')
            . view('templates/sidebar')
            . view('pamflet', $data)
            . view('templates/footer');
    }
    
}