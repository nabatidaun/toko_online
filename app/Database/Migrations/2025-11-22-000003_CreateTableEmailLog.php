<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreateTableEmailLog extends Migration
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
                'unsigned'   => true,
                'null'       => true,
            ],
            'recipient_email' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'subject' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'status' => [
                'type'    => 'ENUM',
                'constraint' => ['pending', 'sent', 'failed'],
                'default' => 'pending',
            ],
            'error_message' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'sent_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'TIMESTAMP',
                'default' => new RawSql('CURRENT_TIMESTAMP'),
            ],
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('status');
        $this->forge->addKey('created_at');
        // Commented: Foreign key optional - bisa ditambah manual nanti
        // $this->forge->addForeignKey('invoice_id', 'tb_invoice', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('tb_email_log');
    }

    public function down()
    {
        $this->forge->dropTable('tb_email_log');
    }
}


?>