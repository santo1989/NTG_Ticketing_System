<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTicketsTable extends Migration
{
    public function up()
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number')->unique();
            $table->foreignId('client_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('support_user_id')->nullable()->constrained('users')->onDelete('no action');
            $table->enum('support_type', ['ERP Support', 'IT Support', 'Programmer Support']);
            $table->string('subject');
            $table->text('description');
            $table->string('file_path')->nullable();
            $table->enum('status', ['Pending', 'Ticket Received', 'MIS Receive Issue', 'Send to Logic', 'Complete'])->default('Pending');
            $table->text('remarks')->nullable();
            $table->timestamp('solving_time')->nullable();
            $table->timestamp('received_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tickets');
    }
}
