<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreateTableWishlist extends Migration
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
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'product_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                // Match tb_barang.id_brg type (not unsigned)
            ],
            'created_at' => [
                'type' => 'TIMESTAMP',
                'default' => new RawSql('CURRENT_TIMESTAMP'),
            ],
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('user_id', 'tb_user', 'id', 'CASCADE', 'CASCADE');
        // Foreign key for existing table - add manually if needed
        // $this->forge->addForeignKey('product_id', 'tb_barang', 'id_brg', 'CASCADE', 'CASCADE');
        $this->forge->addUniqueKey(['user_id', 'product_id']);
        $this->forge->createTable('tb_wishlist');
    }

    public function down()
    {
        $this->forge->dropTable('tb_wishlist');
    }
}


?>