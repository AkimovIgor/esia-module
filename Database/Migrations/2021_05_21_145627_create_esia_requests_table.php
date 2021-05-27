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
                'url' => 'esia/prns',
                'methods' => 'get, post',
                'description' => 'Получить данные о физическом лице'
            ],
            [
                'url' => 'esia/prns/ctts',
                'methods' => 'get, post',
                'description' => 'Получить перечень контактов физического лица'
            ],
            [
                'url' => 'esia/prns/ctts/{contact_id}',
                'methods' => 'get, post',
                'description' => 'Получить сведения об отдельной записи в перечне контактов физического лица'
            ],
            [
                'url' => 'esia/prns/addrs',
                'methods' => 'get, post',
                'description' => 'Получить перечень адресов физического лица'
            ],
            [
                'url' => 'esia/prns/addrs/{address_id}',
                'methods' => 'get, post',
                'description' => 'Получить сведения об отдельной записи в перечне адресов физического лица'
            ],
            [
                'url' => 'esia/prns/docs',
                'methods' => 'get, post',
                'description' => 'Получить перечень документов физического лица'
            ],
            [
                'url' => 'esia/prns/docs/{doc_id}',
                'methods' => 'get, post',
                'description' => 'Получить сведения об отдельной записи в перечне документов физического лица'
            ],
            [
                'url' => 'esia/prns/orgs',
                'methods' => 'get, post',
                'description' => 'Получить перечень организаций, сотрудником которых является данное физическое лицо.
                Параметры:
                embed - позволяет получить все организации, доступ к которым не был предоставлен в scopes при помощи параметра (elements)'
            ],
            [
                'url' => 'esia/prns/kids',
                'methods' => 'get, post',
                'description' => 'Получить перечень записей о детях физического лица'
            ],
            [
                'url' => 'esia/prns/kids/{kid_id}',
                'methods' => 'get, post',
                'description' => 'Получить сведения об отдельной записи в перечне детей физического лица'
            ],
            [
                'url' => 'esia/prns/kids/{kid_id}/docs',
                'methods' => 'get',
                'description' => 'Получить перечень документов ребенка физического лица (временно недоступно)'
            ],
            [
                'url' => 'esia/prns/kids/{kid_id}/docs/{doc_id}',
                'methods' => 'get',
                'description' => 'Получить сведения об отдельной записи в перечне документов ребенка физического лица (временно недоступно)'
            ],
            [
                'url' => 'esia/prns/vhls',
                'methods' => 'get, post',
                'description' => 'Получить перечень транспортных средств'
            ],
            [
                'url' => 'esia/prns/vhls?embed=(vehicles.elements)',
                'methods' => 'get, post',
                'description' => 'Получить перечень транспортных средств с данными о каждом транспортном средстве'
            ],
            [
                'url' => 'esia/prns/vhls/{vehicle_id}',
                'methods' => 'get, post',
                'description' => 'Получить транспортное средство пользователя'
            ],
            [
                'url' => 'esia/categories/prns',
                'methods' => 'get',
                'description' => 'Получить категории самозанятого пользователя'
            ],
            // Organizations
            [
                'url' => 'esia/roles',
                'methods' => 'get',
                'description' => 'Получение списка организаций'
            ],
            [
                'url' => 'esia/orgs/{org_id}',
                'methods' => 'get',
                'description' => 'Получить данные об организации с идентификатором'
            ],
            [
                'url' => 'esia/orgs/{org_id}/brhs',
                'methods' => 'get, post',
                'description' => 'Получить перечень филиалов организации'
            ],
            [
                'url' => 'esia/orgs/{org_id}/brhs/{branch_id}',
                'methods' => 'get, post',
                'description' => 'Получить сведения о филиале организации'
            ],
            [
                'url' => 'esia/orgs/{org_id}/ctts',
                'methods' => 'get, post',
                'description' => 'Получить перечень контактов организации'
            ],
            [
                'url' => 'esia/orgs/{org_id}/ctts/{contact_id}',
                'methods' => 'get, post',
                'description' => 'Получить сведения об отдельной записи в перечне контактов организации'
            ],
            [
                'url' => 'esia/orgs/{org_id}/addrs',
                'methods' => 'get, post',
                'description' => 'Получить перечень адресов организации'
            ],
            [
                'url' => 'esia/orgs/{org_id}/addrs/{address_id}',
                'methods' => 'get, post',
                'description' => 'Получить сведения об отдельной записи в перечне адресов организации'
            ],
            [
                'url' => 'esia/orgs/{org_id}/vhls',
                'methods' => 'get, post',
                'description' => 'Получить перечень транспортных средств, которыми владеет данная организация'
            ],
            [
                'url' => 'esia/orgs/{org_id}/vhls?embed=(vehicles)',
                'methods' => 'get, post',
                'description' => 'Получить перечень транспортных средств, которыми владеет данная организация, содержащий информацию о каждом транспортном средстве'
            ],
            [
                'url' => 'esia/orgs/{org_id}/vhls/{vehicle_id}',
                'methods' => 'get, post',
                'description' => 'Получить транспортное средство организации'
            ],
            [
                'url' => 'esia/categories/orgs/{org_id}',
                'methods' => 'get, post',
                'description' => 'Получить данные о категориях организации'
            ],
            [
                'url' => 'esia/orgs/{org_id}/emps?embed=(elements)',
                'methods' => 'get, post',
                'description' => 'Получить перечень сотрудников организации'
            ],
            [
                'url' => 'esia/orgs/{org_id}/emps/{employee_id}',
                'methods' => 'get, post',
                'description' => 'Получить данные о сотруднике организации с идентификатором'
            ],
            [
                'url' => 'esia/orgs/{org_id}/grps?embed=(elements)',
                'methods' => 'get, post',
                'description' => 'Получить перечень групп организации'
            ],
            [
                'url' => 'esia/orgs/{org_id}/grps/{group_mnemonic}',
                'methods' => 'get, post',
                'description' => 'Получить данные о группе организации с мнемоникой'
            ],
            [
                'url' => 'esia/orgs/{org_id}/grps/{group_mnemonic}/grps',
                'methods' => 'get, post',
                'description' => 'Получить перечень групп, членом которых является данный сотрудник'
            ],
            [
                'url' => 'esia/person',
                'methods' => 'get',
                'description' => 'Получить данные о физическом лице'
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
