<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreateTableRefund extends Migration
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
            'invoice_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                // Match tb_invoice.id type (NOT unsigned)
            ],
            'amount' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'reason' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'status' => [
                'type'    => 'ENUM',
                'constraint' => ['pending', 'approved', 'rejected', 'completed'],
                'default' => 'pending',
            ],
            'midtrans_refund_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'admin_notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'TIMESTAMP',
                'default' => new RawSql('CURRENT_TIMESTAMP'),
            ],
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('invoice_id');
        $this->forge->addKey('status');
        // Foreign key commented - add manually after ensuring tb_invoice exists
        // $this->forge->addForeignKey('invoice_id', 'tb_invoice', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('tb_refund');
    }

    public function down()
    {
        $this->forge->dropTable('tb_refund');
    }
}


?>