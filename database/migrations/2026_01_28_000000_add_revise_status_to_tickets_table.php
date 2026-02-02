<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddReviseStatusToTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * NOTE: This migration updates the `status` column to include the new 'Revise' value.
     * If your DB driver doesn't support ENUM, adjust accordingly.
     *
     * @return void
     */
    public function up()
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE `tickets` MODIFY `status` ENUM('Pending','Ticket Received','MIS Receive Issue','Send to Logic','Complete','Revise') NOT NULL DEFAULT 'Pending'");
            return;
        }

        if ($driver === 'sqlsrv') {
            // Find any existing CHECK constraint on tickets.status and drop it
            $row = DB::selectOne("SELECT tc.CONSTRAINT_NAME as name FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS tc JOIN INFORMATION_SCHEMA.CONSTRAINT_COLUMN_USAGE ccu ON tc.CONSTRAINT_NAME = ccu.CONSTRAINT_NAME WHERE tc.CONSTRAINT_TYPE = 'CHECK' AND tc.TABLE_NAME = 'tickets' AND ccu.COLUMN_NAME = 'status'");
            if ($row && isset($row->name)) {
                $constraintName = $row->name;
                DB::statement("ALTER TABLE [tickets] DROP CONSTRAINT [" . $constraintName . "]");
            }

            // Add a new CHECK constraint that includes the 'Revise' value
            DB::statement("ALTER TABLE [tickets] ADD CONSTRAINT CK_tickets_status CHECK ([status] IN ('Pending','Ticket Received','MIS Receive Issue','Send to Logic','Complete','Revise'))");
            return;
        }

        // Other drivers: no-op
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE `tickets` MODIFY `status` ENUM('Pending','Ticket Received','MIS Receive Issue','Send to Logic','Complete') NOT NULL DEFAULT 'Pending'");
            return;
        }

        if ($driver === 'sqlsrv') {
            // Drop existing CHECK constraint on tickets.status
            $row = DB::selectOne("SELECT tc.CONSTRAINT_NAME as name FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS tc JOIN INFORMATION_SCHEMA.CONSTRAINT_COLUMN_USAGE ccu ON tc.CONSTRAINT_NAME = ccu.CONSTRAINT_NAME WHERE tc.CONSTRAINT_TYPE = 'CHECK' AND tc.TABLE_NAME = 'tickets' AND ccu.COLUMN_NAME = 'status'");
            if ($row && isset($row->name)) {
                $constraintName = $row->name;
                DB::statement("ALTER TABLE [tickets] DROP CONSTRAINT [" . $constraintName . "]");
            }

            // Recreate CHECK constraint without 'Revise'
            DB::statement("ALTER TABLE [tickets] ADD CONSTRAINT CK_tickets_status CHECK ([status] IN ('Pending','Ticket Received','MIS Receive Issue','Send to Logic','Complete'))");
            return;
        }

        // Other drivers: no-op
    }
}
