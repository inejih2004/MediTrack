<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateExpirationDatesTableAlertStatusAndTimestamps extends Migration
{
    public function up()
    {
        Schema::table('expiration_dates', function (Blueprint $table) {
            // Change alert_status to VARCHAR(255)
            $table->string('alert_status', 255)->change();
            // Add timestamps if not present
            if (!Schema::hasColumn('expiration_dates', 'created_at') || !Schema::hasColumn('expiration_dates', 'updated_at')) {
                $table->timestamps();
            }
        });
    }

    public function down()
    {
        Schema::table('expiration_dates', function (Blueprint $table) {
            // Revert alert_status to ENUM (adjust based on original schema)
            $table->enum('alert_status', ['active', 'near_expiration'])->change();
            // Remove timestamps if added
            if (Schema::hasColumn('expiration_dates', 'created_at') && Schema::hasColumn('expiration_dates', 'updated_at')) {
                $table->dropColumn(['created_at', 'updated_at']);
            }
        });
    }
}