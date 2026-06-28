<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('visites', function (Blueprint $table) {
            $table->text('compte_rendu')->nullable()->after('recommandation');
        });
    }

    public function down(): void
    {
        Schema::table('visites', function (Blueprint $table) {
            $table->dropColumn(['compte_rendu']);
        });
    }
};
