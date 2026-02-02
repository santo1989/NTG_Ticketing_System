<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class UpdateTicketsStatusEnum extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Update existing 'MIS Receive Issue' to 'Ticket Received' first
        DB::table('tickets')
            ->where('status', 'MIS Receive Issue')
            ->update(['status' => 'Ticket Received']);

        // SQL Server ENUM is already created with the correct values, so no need to modify
        // The original table creation already has all needed statuses
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Revert 'Ticket Received' back to 'MIS Receive Issue'
        DB::table('tickets')
            ->where('status', 'Ticket Received')
            ->update(['status' => 'MIS Receive Issue']);

        // No need to restore constraint for SQL Server
    }
}
