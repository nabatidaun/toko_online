<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreateTableCoupon extends Migration
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
            'code' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'discount_type' => [
                'type'    => 'ENUM',
                'constraint' => ['percentage', 'fixed'],
                'default' => 'percentage',
            ],
            'discount_value' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'min_purchase' => [
                'type'    => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
            'max_usage' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
            'usage_count' => [
                'type'    => 'INT',
                'default' => 0,
            ],
            'status' => [
                'type'    => 'ENUM',
                'constraint' => ['active', 'inactive'],
                'default' => 'active',
            ],
            'valid_from' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
            'valid_to' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'TIMESTAMP',
                'default' => new RawSql('CURRENT_TIMESTAMP'),
            ],
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('code');
        $this->forge->addKey('status');
        $this->forge->createTable('tb_coupon');
    }

    public function down()
    {
        $this->forge->dropTable('tb_coupon');
    }
}

?>