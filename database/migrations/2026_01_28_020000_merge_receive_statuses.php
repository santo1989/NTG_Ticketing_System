<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class MergeReceiveStatuses extends Migration
{
    public function up()
    {
        $driver = DB::getDriverName();

        // Normalize existing rows to 'Receive'
        DB::table('tickets')
            ->whereIn('status', ['Ticket Received', 'MIS Receive Issue'])
            ->update(['status' => 'Receive']);

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE `tickets` MODIFY `status` ENUM('Pending','Receive','Send to Logic','Complete','Revise') NOT NULL DEFAULT 'Pending'");
            return;
        }

        if ($driver === 'sqlsrv') {
            // Drop existing CHECK constraint on status if present
            $row = DB::selectOne("SELECT tc.CONSTRAINT_NAME as name FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS tc JOIN INFORMATION_SCHEMA.CONSTRAINT_COLUMN_USAGE ccu ON tc.CONSTRAINT_NAME = ccu.CONSTRAINT_NAME WHERE tc.CONSTRAINT_TYPE = 'CHECK' AND tc.TABLE_NAME = 'tickets' AND ccu.COLUMN_NAME = 'status'");
            if ($row && isset($row->name)) {
                $constraintName = $row->name;
                DB::statement("ALTER TABLE [tickets] DROP CONSTRAINT [" . $constraintName . "]");
            }

            DB::statement("ALTER TABLE [tickets] ADD CONSTRAINT CK_tickets_status CHECK ([status] IN ('Pending','Receive','Send to Logic','Complete','Revise'))");
            return;
        }

        // other drivers: no-op
    }

    public function down()
    {
        $driver = DB::getDriverName();

        // Cannot reliably revert data to original values, skip data revert
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE `tickets` MODIFY `status` ENUM('Pending','Ticket Received','MIS Receive Issue','Send to Logic','Complete','Revise') NOT NULL DEFAULT 'Pending'");
            return;
        }

        if ($driver === 'sqlsrv') {
            $row = DB::selectOne("SELECT tc.CONSTRAINT_NAME as name FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS tc JOIN INFORMATION_SCHEMA.CONSTRAINT_COLUMN_USAGE ccu ON tc.CONSTRAINT_NAME = ccu.CONSTRAINT_NAME WHERE tc.CONSTRAINT_TYPE = 'CHECK' AND tc.TABLE_NAME = 'tickets' AND ccu.COLUMN_NAME = 'status'");
            if ($row && isset($row->name)) {
                $constraintName = $row->name;
                DB::statement("ALTER TABLE [tickets] DROP CONSTRAINT [" . $constraintName . "]");
            }

            DB::statement("ALTER TABLE [tickets] ADD CONSTRAINT CK_tickets_status CHECK ([status] IN ('Pending','Ticket Received','MIS Receive Issue','Send to Logic','Complete','Revise'))");
            return;
        }
    }
}
