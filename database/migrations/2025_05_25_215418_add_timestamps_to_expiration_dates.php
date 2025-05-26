<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTimestampsToExpirationDates extends Migration
{
    public function up()
    {
        Schema::table('expiration_dates', function (Blueprint $table) {
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::table('expiration_dates', function (Blueprint $table) {
            $table->dropColumn(['created_at', 'updated_at']);
        });
    }
}