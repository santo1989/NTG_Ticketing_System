<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTicketReviewsTable extends Migration
{
    public function up()
    {
        Schema::create('ticket_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('tickets')->onDelete('cascade');
            $table->foreignId('client_id')->constrained('users')->onDelete('no action');
            $table->enum('rating', ['Satisfied', 'Dissatisfied']);
            $table->text('reason')->nullable(); // Mandatory if dissatisfied
            $table->text('feedback')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('ticket_reviews');
    }
}
