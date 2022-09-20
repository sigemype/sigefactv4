<?php

use App\Models\Tenant\Item;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantAddTextFilterToItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('items', function (Blueprint $table) {
            $table->longText('text_filter')->nullable()->after('description');
        });

        DB::statement('create fulltext index items_text_filter_fulltext on items(text_filter);');

        $items = Item::query()->with('category', 'brand')->get();
        foreach ($items as $item) {
            $item->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropIndex('items_text_filter_fulltext');
            $table->dropColumn('text_filter');
        });
    }
}
