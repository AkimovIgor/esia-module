<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEsiaRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('esia_requests', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned();
            $table->string('url');
            $table->string('methods');
            $table->string('description')->nullable();
            $table->timestamps();
        });

        $data = [
            [
                'url' => 'esia/login',
                'methods' => 'get',
                'description' => 'Параметры: scopes - области доступа к госуслугам, намример: scopes=id_doc,fullname'
            ],
            [
                'url' => 'esia/prns/{collection_name?}/{collection_entity_id?}/{oid?}',
                'methods' => 'get, post',
                'description' => 'Параметры:
                collection_name -
                collection_entity_id -
                oid -'
            ],
            [
                'url' => 'esia/person',
                'methods' => 'get',
                'description' => 'Получение информации о пользователе'
            ],
            [
                'url' => 'esia/roles',
                'methods' => 'get',
                'description' => 'Получение списка организаций'
            ],
        ];

        \Illuminate\Support\Facades\DB::table('esia_requests')->insert($data);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('esia_requests');
    }
}
