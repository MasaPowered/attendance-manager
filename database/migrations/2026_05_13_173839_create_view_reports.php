<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("DROP VIEW IF EXISTS report_view"); 
        DB::statement("
            CREATE VIEW report_view AS
            SELECT 
                A1.arriveid, A1.leaveid, A1.user_id, A1.date, A2.name, 
                A3.shift_status, A1.arrivalcheck, A1.leavecheck, 
                A1.arrivaltime, A1.leavetime, A1.latetime, 
                A1.startreport, A1.endreport
            FROM (
                SELECT A.id AS arriveid, B.id AS leaveid, A.user_id, A.date, A.arrivalcheck, B.leavecheck, A.arrivaltime, B.leavetime, A.latetime, A.report AS startreport, B.report AS endreport
                FROM start_report_table AS A LEFT OUTER JOIN end_report_table AS B ON A.user_id = B.user_id AND A.date = B.date
                UNION
                SELECT B.id AS arriveid, A.id AS leaveid, A.user_id, A.date, B.arrivalcheck, A.leavecheck, B.arrivaltime, A.leavetime, B.latetime, B.report AS startreport, A.report AS endreport
                FROM end_report_table AS A LEFT OUTER JOIN start_report_table AS B ON A.user_id = B.user_id AND A.date = B.date
                WHERE B.date IS NULL
            ) AS A1
            LEFT OUTER JOIN user_table AS A2 ON A1.user_id = A2.user_id
            LEFT OUTER JOIN shift_table AS A3 ON A1.user_id = A3.user_id AND A1.date = A3.date
            
            UNION ALL
            
            SELECT 
                NULL AS arriveid, NULL AS leaveid, A3.user_id, A3.date, A2.name, 
                A3.shift_status, NULL AS arrivalcheck, NULL AS leavecheck, 
                NULL AS arrivaltime, NULL AS leavetime, NULL AS latetime, 
                NULL AS startreport, NULL AS endreport
            FROM shift_table AS A3
            LEFT OUTER JOIN user_table AS A2 ON A3.user_id = A2.user_id
            WHERE NOT EXISTS (
                SELECT 1 FROM start_report_table AS S WHERE S.user_id = A3.user_id AND S.date = A3.date
                UNION
                SELECT 1 FROM end_report_table AS E WHERE E.user_id = A3.user_id AND E.date = A3.date
            )
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS report_view");
    }
};
