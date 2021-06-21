<?php

use Daynnnnn\StatamicDatabase\Blueprints\BlueprintModel;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateBlueprintsNamespaceToNullWhereEmpty extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $blueprints = BlueprintModel::where('namespace', '')->get()->map(function($blueprint) {
            $blueprint->namespace = null;
            $blueprint->save();
            return $blueprint;
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $blueprints = BlueprintModel::where('namespace', null)->get()->map(function($blueprint) {
            $blueprint->namespace = '';
            $blueprint->save();
            return $blueprint;
        });
    }
}
