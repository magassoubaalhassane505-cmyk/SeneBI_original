<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('recoltes', function (Blueprint $table) {
            if (! Schema::hasColumn('recoltes', 'culture')) {
                $table->string('culture')->nullable()->after('prix_unitaire');
            }
            if (! Schema::hasColumn('recoltes', 'saison')) {
                $table->string('saison')->nullable()->after('culture');
            }
            if (! Schema::hasColumn('recoltes', 'couts_totaux')) {
                $table->decimal('couts_totaux', 14, 2)->default(0)->after('saison');
            }
            if (! Schema::hasColumn('recoltes', 'benefice_net')) {
                $table->decimal('benefice_net', 14, 2)->default(0)->after('couts_totaux');
            }
            if (! Schema::hasColumn('recoltes', 'revenu_total')) {
                $table->decimal('revenu_total', 14, 2)->default(0)->after('benefice_net');
            }
        });
    }

    public function down(): void
    {
        Schema::table('recoltes', function (Blueprint $table) {
            $table->dropColumn(['culture', 'saison', 'couts_totaux', 'benefice_net', 'revenu_total']);
        });
    }
};
