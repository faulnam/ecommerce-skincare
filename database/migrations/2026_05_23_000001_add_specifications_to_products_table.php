<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->text('series')->nullable()->after('brand');
            $table->text('shape')->nullable()->after('series');
            $table->text('balance')->nullable()->after('shape');
            $table->text('hijab_weight')->nullable()->after('balance');
            $table->text('play_style')->nullable()->after('hijab_weight');
            $table->text('player_type')->nullable()->after('play_style');
            $table->text('core')->nullable()->after('player_type');
            $table->text('faces')->nullable()->after('core');
            $table->text('frame')->nullable()->after('faces');
            $table->text('surface')->nullable()->after('frame');
            $table->text('feel')->nullable()->after('surface');
            $table->text('power')->nullable()->after('feel');
            $table->text('control')->nullable()->after('power');
            $table->text('maneuverability')->nullable()->after('control');
            $table->text('comfort')->nullable()->after('maneuverability');
            $table->text('technology')->nullable()->after('comfort');
            $table->text('benefits')->nullable()->after('technology');
            $table->text('suitable_for')->nullable()->after('benefits');
            $table->text('collection')->nullable()->after('suitable_for');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'series',
                'shape',
                'balance',
                'hijab_weight',
                'play_style',
                'player_type',
                'core',
                'faces',
                'frame',
                'surface',
                'feel',
                'power',
                'control',
                'maneuverability',
                'comfort',
                'technology',
                'benefits',
                'suitable_for',
                'collection'
            ]);
        });
    }
};
