<?php

use Illuminate\Database\Migrations\Migration;

class InsertEnableServiceOptionsImportableInPermissionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        (new PermissionsTableSeeder())->run(
            'IMPORTABLE.ENABLE_OPTIONS_SERVICES',
            'Habilitar opções de serviços massiva.',
            'WEB'
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::delete('delete from permissions where slug = ?', ['IMPORTABLE.ENABLE_OPTIONS_SERVICES']);
    }
}
