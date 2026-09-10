<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table) {
            if (! Schema::hasColumn('items', 'type')) {
                $table->string('type')->default('simple')->after('id');
            }

            if (! Schema::hasColumn('items', 'parent_id')) {
                $table->foreignId('parent_id')
                    ->nullable()
                    ->after('type')
                    ->constrained('items')
                    ->cascadeOnDelete();
            }
        });

        if (Schema::hasColumn('items', 'has_variants')) {
            DB::table('items')
                ->where('has_variants', true)
                ->update(['type' => 'variant']);
        }
    }

    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            if (Schema::hasColumn('items', 'parent_id')) {
                $table->dropConstrainedForeignId('parent_id');
            }

            if (Schema::hasColumn('items', 'type')) {
                $table->dropColumn('type');
            }
        });
    }
};
