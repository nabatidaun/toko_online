<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreateTableReview extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'product_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                // Match tb_barang.id_brg type (not unsigned)
            ],
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'invoice_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
            'rating' => [
                'type'       => 'INT',
                'constraint' => 1,
            ],
            'comment' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'verified_purchase' => [
                'type'    => 'BOOLEAN',
                'default' => false,
            ],
            'helpful_count' => [
                'type'    => 'INT',
                'default' => 0,
            ],
            'created_at' => [
                'type' => 'TIMESTAMP',
                'default' => new RawSql('CURRENT_TIMESTAMP'),
            ],
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('product_id');
        $this->forge->addKey('rating');
        $this->forge->addForeignKey('product_id', 'tb_barang', 'id_brg', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('user_id', 'tb_user', 'id', 'CASCADE', 'CASCADE');
        // Foreign key for existing table - add manually if needed
        // $this->forge->addForeignKey('invoice_id', 'tb_invoice', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('tb_review');
    }

    public function down()
    {
        $this->forge->dropTable('tb_review');
    }
}


?>