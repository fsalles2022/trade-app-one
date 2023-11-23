<?php

namespace TradeAppOne\Tests\Helpers\Migrations;

use Discount\Enumerators\DiscountStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Terms\Enums\StatusUserTermsEnum;
use Terms\Enums\TypeTermsEnum;
use TradeAppOne\Domain\Components\Helpers\ConstantHelper;

class SqliteMigration extends Migration
{
    public function up()
    {
        $this->networks();
        $this->hierarchies();
        $this->pointsOfSale();
        $this->roles();
        $this->users();
        $this->passwordResets();
        $this->userVerificationsTable();
        $this->pointsOfSale_users();
        $this->permissions();
        $this->role_permissions();
        $this->pointsOfSale_hierarchies();
        $this->hierarchies_users();
        $this->userThirdPartyRegistrations();
        $this->importSales();
        $this->goals();
        $this->tokens();
        $this->thirdPartiesAccess();
        $this->devices();
        $this->devices_outsourced();
        $this->devices_network();
        $this->questions();
        $this->quizzes();
        $this->questions_quizzes();
        $this->evaluations();
        $this->deviceTier();
        $this->discounts();
        $this->pointsOfSaleDiscounts();
        $this->devices_discounts();
        $this->discountProducts();
        $this->imeiChangeHistory();
        $this->importHistory();
        $this->goalsTypes();
        $this->network_goalsTypes();
        $this->charts();
        $this->chartRoles();
        $this->handbooks();
        $this->jobs();

        $this->operators();
        $this->operatorsUsers();
        $this->channels();
        $this->networks_channels();
        $this->users_channels();

        $this->services();
        $this->serviceOptions();
        $this->availableServices();
        $this->servicesServiceOptions();
        $this->products();

        $this->ceaGiftCards();
        $this->integrations();
        $this->availableRedirects();
        $this->routes();
        $this->whitelists();
        $this->integrations_routes();
        $this->accessLogs();
        $this->evaluationsBonus();
        $this->generaliProduct();
        $this->viaVarejoCoupons();
        $this->userAuthAlternates();
        $this->recommendations();

        $this->bulletins();
        $this->bulletinsRoles();
        $this->bulletinsUsers();
        $this->bulletinsPointsOfSales();

        $this->terms();
        $this->userTerm();
    }

    public function networks()
    {
        Schema::create('networks', function (Blueprint $table) {
            $table->increments('id');
            $table->string('slug')->unique();
            $table->string('label')->nullable()->default(null);
            $table->string('cnpj')->unique();
            $table->string('tradingName')->nullable()->default(null);
            $table->string('companyName')->nullable()->default(null);
            $table->string('telephone')->nullable()->default(null);
            $table->string('state')->nullable()->default(null);
            $table->string('city')->nullable()->default(null);
            $table->string('zipCode')->nullable()->default(null);
            $table->string('local')->nullable()->default(null);
            $table->string('neighborhood')->nullable()->default(null);
            $table->integer('number')->nullable()->default(null);
            $table->string('complement')->nullable()->default(null);
            $table->string('channel')->nullable()->default(null);


            $table->json('preferences')->nullable()->default(null);
            $table->json('availableServices')->nullable()->default(null);
            $table->json('responsiblePersons')->nullable()->default(null);

            $table->softDeletes('deletedAt');

            $table->timestamp('createdAt')->default(now());
            $table->timestamp('updatedAt')->default(now());
        });
    }

    public function hierarchies()
    {
        Schema::create('hierarchies', function (Blueprint $table) {
            $table->increments('id');
            $table->string('slug')->nullable(true);
            $table->string('label');
            $table->string('sequence')->nullable();
            $table->unsignedInteger('parent')->nullable()->null();

            $table->timestamp('createdAt')->default(now());
            $table->timestamp('updatedAt')->default(now());
            $table->unique('slug');

            $table->softDeletes('deletedAt');

            $table->unsignedInteger('networkId')->nullable(true);
            $table->foreign('parent')->references('id')->on('hierarchies');
            $table->foreign('networkId')->references('id')->on('networks')->onDelete('cascade');
        });
    }

    public function pointsOfSale()
    {
        Schema::create('pointsOfSale', function (Blueprint $table) {
            $table->increments('id');
            $table->string('slug')->nullable();
            $table->string('label')->nullable()->default(null);
            $table->string('cnpj')->unique();
            $table->string('tradingName')->nullable()->default(null);
            $table->string('telephone')->nullable()->default(null);
            $table->string('state')->nullable()->default(null);
            $table->string('city')->nullable()->default(null);
            $table->string('zipCode')->nullable()->default(null);
            $table->string('local')->nullable()->default(null);
            $table->string('neighborhood')->nullable()->default(null);
            $table->string('companyName')->nullable()->default(null);
            $table->string('areaCode')->nullable()->default(null);
            $table->string('number')->nullable()->default(null);
            $table->string('complement')->nullable()->default(null);
            $table->float('latitude', 10, 7)->nullable()->default(0.0);
            $table->float('longitude', 11, 7)->nullable()->default(0.0);

            $table->json('providerIdentifiers')->nullable()->default(null);
            $table->json('availableServices')->nullable()->default(null);

            $table->timestamp('createdAt')->default(now());
            $table->timestamp('updatedAt')->default(now());
            $table->softDeletes('deletedAt');

            $table->unsignedInteger('hierarchyId')->nullable(true);
            $table->unsignedInteger('networkId');

            $table->unique(['slug', 'networkId']);
            $table->foreign('hierarchyId')->references('id')->on('hierarchies')->onDelete('cascade');
            $table->foreign('networkId')->references('id')->on('networks')->onDelete('cascade');
        });
    }

    public function roles()
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('slug')->unique();
            $table->integer('level')->nullable()->default(1000);
            $table->json('dashboardPermissions')->nullable();
            $table->json('permissions')->nullable()->default(null);
            $table->integer('parent')->nullable()->default(null);
            $table->string('sequence')->nullable();

            $table->timestamp('createdAt')->default(now());
            $table->timestamp('updatedAt')->default(now());
            $table->softDeletes('deletedAt');

            $table->unsignedInteger('networkId')->nullable()->default(null);

            $table->foreign('networkId')->references('id')->on('networks')->onDelete('cascade');
            $table->foreign('parent')->references('id')->on('roles')->onDelete('restrict');
        });
    }

    public function users()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('firstName');
            $table->string('lastName');
            $table->string('email');
            $table->string('cpf')->unique();
            $table->string('areaCode')->nullable()->default(null);
            $table->string('activationStatusCode')->default('NONVERIFIED');
            $table->string('password');
            $table->date('birthday')->default(now());
            $table->timestamp('lastSignin')->default(now());
            $table->integer('signinAttempts')->default(0);
            $table->rememberToken();
            $table->string('activeToken')->nullable();
            $table->json('integrationCredentials')->nullable()->default(null);

            $table->timestamp('createdAt')->default(now());
            $table->timestamp('updatedAt')->default(now());

            $table->softDeletes('deletedAt');

            $table->unsignedInteger('roleId');
            $table->foreign('roleId')->references('id')->on('roles')->onDelete('cascade');
        });
    }

    public function passwordResets()
    {
        Schema::create('passwordResets', function (Blueprint $table) {
            $table->increments('id');
            $table->string('status')->nullable(false)->default('WAITING');
            $table->timestamp('createdAt')->default(now());
            $table->timestamp('updatedAt')->default(now());

            $table->softDeletes('deletedAt');

            $table->unsignedInteger('userId');
            $table->foreign('userId')->references('id')->on('users')->onDelete('cascade');

            $table->unsignedInteger('managerId')->nullable(true);
            $table->foreign('managerId')->references('id')->on('users')->onDelete('cascade');

            $table->unsignedInteger('pointsOfSaleId');
            $table->foreign('pointsOfSaleId')->references('id')->on('pointsOfSale');
        });
    }

    public function userVerificationsTable()
    {
        Schema::create('userVerifications', function (Blueprint $table) {
            $table->increments('id');
            $table->string('verificationCode')->nullable(false);

            $table->timestamp('createdAt')->default(now());
            $table->timestamp('updatedAt')->default(now());

            $table->softDeletes('deletedAt');

            $table->unsignedInteger('userId');
            $table->foreign('userId')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function pointsOfSale_users()
    {
        Schema::create('pointsOfSale_users', function (Blueprint $table) {
            $table->increments('id');

            $table->timestamp('createdAt')->default(now());
            $table->timestamp('updatedAt')->default(now());

            $table->softDeletes('deletedAt');

            $table->unsignedInteger('pointsOfSaleId');
            $table->unsignedInteger('userId');

            $table->foreign('pointsOfSaleId')->references('id')->on('pointsOfSale')->onDelete('cascade');
            $table->foreign('userId')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function permissions()
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('slug')->unique();
            $table->string('label');
            $table->string('client');

            $table->timestamp('createdAt')->default(now());
            $table->timestamp('updatedAt')->default(now());

            $table->softDeletes('deletedAt');
        });
    }

    public function role_permissions()
    {
        Schema::create('role_permissions', function (Blueprint $table) {
            $table->increments('id');

            $table->timestamp('createdAt')->default(now());
            $table->timestamp('updatedAt')->default(now());

            $table->softDeletes('deletedAt');

            $table->unsignedInteger('roleId');
            $table->unsignedInteger('permissionsId');

            $table->foreign('roleId')->references('id')->on('roles')->onDelete('cascade');
            $table->foreign('permissionsId')->references('id')->on('permissions')->onDelete('cascade');
        });
    }

    public function pointsOfSale_hierarchies()
    {
        Schema::create('pointsOfSale_hierarchies', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('pointOfSaleId');
            $table->unsignedInteger('hierarchyId');

            $table->foreign('pointOfSaleId')->references('id')->on('pointsOfSale')->onDelete('cascade');
            $table->foreign('hierarchyId')->references('id')->on('hierarchies')->onDelete('cascade');

            $table->unique(['pointOfSaleId', 'hierarchyId']);
        });
    }

    public function hierarchies_users()
    {
        Schema::create('hierarchies_users', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('hierarchyId');
            $table->unsignedInteger('userId');

            $table->timestamp('createdAt')->default(now());
            $table->timestamp('updatedAt')->default(now());

            $table->softDeletes('deletedAt');

            $table->foreign('hierarchyId')->references('id')->on('hierarchies')->onDelete('cascade');
            $table->foreign('userId')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function userThirdPartyRegistrations()
    {
        Schema::create('userThirdPartyRegistrations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('operator')->nullable(false);
            $table->boolean('done')->default(false);
            $table->string('log')->nullable(true);

            $table->unsignedInteger('userId');
            $table->unsignedInteger('pointOfSaleId')->nullable(true);

            $table->timestamp('createdAt')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updatedAt')->default(DB::raw('CURRENT_TIMESTAMP'));

            $table->softDeletes('deletedAt');

            $table->foreign('userId')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('pointOfSaleId', 'foreign_currentpointofsaleid')
                ->references('id')
                ->on('pointsOfSale')
                ->onDelete('cascade');
        });
    }

    public function importSales()
    {
        Schema::create('importSales', function (Blueprint $table) {
            $table->increments('id');
            $table->string('source')->nullable();
            $table->string('pointofsale_id')->nullable();
            $table->string('saletransaction');
            $table->string('pointofsale_cnpj')->nullable();
            $table->string('pointofsale_slug')->nullable();
            $table->string('pointofsale_label')->nullable();
            $table->string('pointofsale_network_slug')->nullable();
            $table->string('pointofsale_network_label')->nullable();
            $table->string('pointofsale_hierarchy')->nullable();
            $table->string('pointofsale_hierarchy_label')->nullable();
            $table->string('pointofsale_state')->nullable();
            $table->string('pointofsale_areaCode')->nullable();
            $table->string('user_id')->nullable();
            $table->string('user_cpf')->nullable();
            $table->string('user_role')->nullable();
            $table->string('user_firstname')->nullable();
            $table->string('service_servicetransaction')->unique();
            $table->string('service_operator')->nullable();
            $table->string('service_sector')->nullable();
            $table->string('service_operation')->nullable();
            $table->string('service_mode')->nullable();
            $table->string('service_product')->nullable();
            $table->string('service_label')->nullable();
            $table->string('service_iccid')->nullable();
            $table->string('service_msisdn')->nullable();
            $table->string('service_portednumber')->nullable();
            $table->string('service_dueDate')->nullable();
            $table->string('service_statusthirdparty')->nullable();
            $table->string('service_status')->nullable();
            $table->string('service_invoicetype')->nullable();
            $table->string('service_operator_pid');
            $table->string('service_operator_sid')->nullable();
            $table->string('service_customer_cpf')->nullable();
            $table->string('service_customer_firstname')->nullable();
            $table->string('service_customer_lastname')->nullable();
            $table->string('service_customer_birthday')->nullable();
            $table->string('service_customer_gender')->nullable();
            $table->string('service_customer_filiation')->nullable();
            $table->string('service_customer_email')->nullable();
            $table->string('service_customer_mainPhone')->nullable();
            $table->string('service_customer_secondaryphone')->nullable();
            $table->string('service_customer_zipcode')->nullable();
            $table->string('service_customer_local')->nullable();
            $table->string('service_customer_number')->nullable();
            $table->string('service_customer_neighborhood')->nullable();
            $table->string('service_customer_state')->nullable();
            $table->string('service_customer_city')->nullable();

            $table->timestamp('updatedAt')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('createdAt')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->softDeletes('deletedAt');
            $table->unique(['service_operator', 'service_operator_pid', 'service_operator_sid'], 'operator_pid_sid');
        });
    }

    public function goals()
    {
        Schema::create('goals', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('pointOfSaleId');
            $table->integer('year')->nullable(false);
            $table->integer('month')->nullable(false);
            $table->integer('goal')->nullable(false);
            $table->unsignedInteger('goalTypeId')->nullable();

            $table->timestamp('createdAt')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updatedAt')->default(DB::raw('CURRENT_TIMESTAMP'));

            $table->softDeletes('deletedAt');

            $table->foreign('goalTypeId')->references('id')->on('goalsTypes')->onDelete('cascade');
            $table->foreign('pointOfSaleId')->references('id')->on('pointsOfSale')->onDelete('cascade');
        });
    }

    public function tokens()
    {
        Schema::create('tokens', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('accessKey')->unique();
            $table->unsignedInteger('userId')->unique();

            $table->foreign('userId')->references('id')->on('users')->onDelete('cascade');

            $table->timestamp('createdAt')->default(now());
            $table->timestamp('updatedAt')->default(now());
            $table->softDeletes('deletedAt');
        });
    }

    public function thirdPartiesAccess()
    {
        Schema::create('thirdPartiesAccess', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('slug')->unique();
            $table->unsignedInteger('userId')->unique();

            $table->foreign('userId')->references('id')->on('users')->onDelete('cascade');

            $table->timestamp('createdAt')->default(now());
            $table->timestamp('updatedAt')->default(now());
            $table->softDeletes('deletedAt');
        });
    }


    private function devices()
    {
        Schema::create('devices', function (Blueprint $table) {
            $table->increments('id');
            $table->string('label');
            $table->string('model');
            $table->string('brand');
            $table->string('color');
            $table->string('storage');
            $table->string('imageFront');
            $table->string('imageBehind');
            $table->string('type');
            $table->string('material')->nullable();
            $table->string('caseSize')->nullable();

            $table->softDeletes('deletedAt');

            $table->timestamp('createdAt')->default(now());
            $table->timestamp('updatedAt')->default(now());
        });
    }

    private function devices_outsourced()
    {
        Schema::create('devices_outsourced', function (Blueprint $table) {
            $table->increments('id');
            $table->string('sku');
            $table->string('model');
            $table->double('price', 8, 2);
            $table->string('label')->nullable()->default(null);
            $table->string('brand')->nullable()->default(null);
            $table->string('color')->nullable()->default(null);
            $table->string('storage')->nullable()->default(null);
            $table->unsignedInteger('networkId');

            $table->unique(['sku', 'networkId']);

            $table->softDeletes('deletedAt');
            $table->timestamp('createdAt')->default(now());
            $table->timestamp('updatedAt')->default(now());

            $table->foreign('networkId')->references('id')->on('networks')->onDelete('cascade');
        });
    }

    private function devices_network()
    {
        Schema::create('devices_network', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('deviceId');
            $table->foreign('deviceId')->references('id')->on('devices')->onDelete('cascade');
            $table->unsignedInteger('networkId');
            $table->foreign('networkId')->references('id')->on('networks')->onDelete('cascade');

            $table->timestamp('createdAt')->default(now());
            $table->timestamp('updatedAt')->default(now());
            $table->softDeletes('deletedAt');
            $table->string('sku')->nullable();
            $table->unique(['deviceId', 'networkId']);
            $table->tinyInteger('isPreSale')->default(0);
        });

    }

    private function questions()
    {

        Schema::create('questions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('question');
            $table->string('weight');
            $table->string('order');
            $table->boolean('blocker');
            $table->unsignedInteger('networkId');
            $table->foreign('networkId')->references('id')->on('networks')->onDelete('cascade');

            $table->timestamp('createdAt')->default(now());
            $table->timestamp('updatedAt')->default(now());
            $table->softDeletes('deletedAt');
            $table->string('description', 90)->nullable();
        });


    }

    private function quizzes()
    {
        Schema::create('quizzes', function (Blueprint $table) {
            $table->increments('id');

            $table->timestamp('createdAt')->default(now());
            $table->timestamp('updatedAt')->default(now());
            $table->softDeletes('deletedAt');
        });

    }

    private function questions_quizzes()
    {
        Schema::create('questions_quizzes', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('questionId');
            $table->foreign('questionId')->references('id')->on('questions')->onDelete('cascade');
            $table->unsignedInteger('quizId');
            $table->foreign('quizId')->references('id')->on('quizzes')->onDelete('cascade');

            $table->timestamp('createdAt')->default(now());
            $table->timestamp('updatedAt')->default(now());
            $table->softDeletes('deletedAt');
        });
    }

    private function evaluations()
    {
        Schema::create('evaluations', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('quizId');
            $table->foreign('quizId')->references('id')->on('quizzes')->onDelete('cascade');
            $table->unsignedInteger('deviceNetworkId');
            $table->foreign('deviceNetworkId')->references('id')->on('devices_network')->onDelete('cascade');
            $table->string('goodValue');
            $table->string('averageValue');
            $table->string('defectValue');

            $table->timestamp('createdAt')->default(now());
            $table->timestamp('updatedAt')->default(now());
            $table->softDeletes('deletedAt');
        });

    }

    private function deviceTier()
    {
        Schema::create('deviceTier', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('goodTierNote');
            $table->integer('middleTierNote');
            $table->integer('defectTierNote');

            $table->timestamp('createdAt')->default(now());
            $table->timestamp('updatedAt')->default(now());
            $table->softDeletes('deletedAt');
        });

    }

    public function discounts()
    {
        Schema::create('discounts', function (Blueprint $table) {


            $table->increments('id');
            $table->string('title');
            $table->enum('status', ConstantHelper::getAllConstants(DiscountStatus::class));

            $table->string('filterMode')->default('ALL');
            $table->dateTime('startAt');
            $table->dateTime('endAt');
            $table->unsignedInteger('userId')->nullable(true)->default(null);
            $table->unsignedInteger('networkId');

            $table->foreign('userId')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('networkId')->references('id')->on('networks')->onDelete('cascade');

            $table->timestamp('createdAt')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updatedAt')->default(DB::raw('CURRENT_TIMESTAMP'));

            $table->softDeletes('deletedAt');
        });
    }

    public function pointsOfSaleDiscounts()
    {
        Schema::create('pointsOfSale_discounts', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('pointOfSaleId');
            $table->foreign('pointOfSaleId')->references('id')->on('pointsOfSale')->onDelete('cascade');
            $table->unsignedInteger('discountId');
            $table->foreign('discountId')->references('id')->on('discounts')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function devices_discounts()
    {
        Schema::create('devices_discounts', function (Blueprint $table) {
            $table->increments('id');
            $table->double('discount', 8, 2);
            $table->unsignedInteger('deviceId');
            $table->unsignedInteger('discountId');

            $table->timestamp('createdAt')->default(now());
            $table->timestamp('updatedAt')->default(now());
            $table->softDeletes('deletedAt');
            $table->foreign('deviceId')->references('id')->on('devices_outsourced')->onDelete('cascade');
            $table->foreign('discountId')->references('id')->on('discounts')->onDelete('cascade');
        });
    }

    public function discountProducts()
    {
        Schema::create('discount_products', function (Blueprint $table) {
            $table->increments('id');
            $table->string('operator')->nullable(true)->default(null);
            $table->string('operation')->nullable(true)->default(null);
            $table->string('product')->nullable(true)->default(null);
            $table->string('promotion')->nullable(true)->default(null);

            $table->string('filterMode')->nullable(true)->default(null);

            $table->string('label')->nullable();

            $table->unsignedInteger('discountId');
            $table->foreign('discountId')->references('id')->on('discounts')->onDelete('cascade');

            $table->timestamp('createdAt')->default(now());
            $table->timestamp('updatedAt')->default(now());
            $table->softDeletes('deletedAt');
        });
    }

    private function imeiChangeHistory(): void
    {
        Schema::create('imeiChangeHistory', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('serviceTransaction')->index();
            $table->timestamp('exchangeDate')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->string('oldImei')->nullable();
            $table->string('newImei')->nullable();
            $table->unsignedInteger('userIdWhoChanged');
            $table->string('userCpfWhoChanged')->index();
            $table->unsignedInteger('userIdWhoAuthorized');
            $table->string('userCpfWhoAuthorized')->index();
            $table->string('protocol')->nullable()->index();

            $table->timestamp('createdAt')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updatedAt')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->softDeletes('deletedAt');

        });
    }

    private function importHistory()
    {
        Schema::create('importHistory', function (Blueprint $table) {
            $table->increments('id');
            $table->text('type');
            $table->text('inputFile')->nullable(true);
            $table->text('outputFile')->nullable(true);
            $table->text('status');
            $table->unsignedInteger('userId');

            $table->timestamp('createdAt')->default(now());
            $table->timestamp('updatedAt')->default(now());
            $table->softDeletes('deletedAt');


            $table->foreign('userId')->references('id')->on('users')->onDelete('cascade');
        });
    }

    private function goalsTypes()
    {
        Schema::create('goalsTypes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('type')->unique();
            $table->string('label');

            $table->timestamp('createdAt')->default(now());
            $table->timestamp('updatedAt')->default(now());
            $table->softDeletes('deletedAt');
        });
    }

    private function network_goalsTypes()
    {
        Schema::create('network_goalsTypes', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('networkId');
            $table->unsignedInteger('goalTypeId');

            $table->timestamp('createdAt')->default(now());
            $table->timestamp('updatedAt')->default(now());
            $table->softDeletes('deletedAt');

            $table->foreign('networkId')->references('id')->on('networks')->onDelete('cascade');
            $table->foreign('goalTypeId')->references('id')->on('goalsTypes')->onDelete('cascade');
        });
    }

    private function handbooks()
    {
        Schema::create('handbooks', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('userId');
            $table->string('title');
            $table->string('description')->nullable()->default(null);
            $table->string('file');
            $table->string('type');
            $table->string('module');
            $table->string('category');
            $table->string('networksFilterMode')->nullable()->default(null);
            $table->string('rolesFilterMode')->nullable()->default(null);
            $table->timestamp('createdAt')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updatedAt')->default(DB::raw('CURRENT_TIMESTAMP'));

            $table->softDeletes('deletedAt');
        });

        Schema::create('handbooks_networks', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('handbookId');
            $table->unsignedInteger('networkId');

            $table->timestamp('createdAt')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updatedAt')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->softDeletes('deletedAt');

            $table->foreign('handbookId')->references('id')->on('handbooks')->onDelete('cascade');
            $table->foreign('networkId')->references('id')->on('networks')->onDelete('cascade');
        });

        Schema::create('handbooks_roles', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('handbookId');
            $table->unsignedInteger('roleId');

            $table->timestamp('createdAt')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updatedAt')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->softDeletes('deletedAt');

            $table->foreign('handbookId')->references('id')->on('handbooks')->onDelete('cascade');
            $table->foreign('roleId')->references('id')->on('roles')->onDelete('cascade');
        });
    }

    public function jobs()
    {
        Schema::create('jobs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('queue')->index();
            $table->longText('payload');
            $table->unsignedTinyInteger('attempts');
            $table->unsignedInteger('reserved_at')->nullable();
            $table->unsignedInteger('available_at');
            $table->unsignedInteger('created_at');
        });
    }

    public function createHierarchyTrigger()
    {
        DB::unprepared('
            
            CREATE TRIGGER sequence_generator BEFORE INSERT ON `hierarchies`
            
            FOR EACH ROW
            
            BEGIN
                IF (`NEW`.`parent` IS NULL) THEN
                    SET @SEQ = (SELECT COUNT(*) + 1 FROM `hierarchies` WHERE `parent` IS NULL);
                    SET NEW.sequence = @SEQ;
                ELSE
                    SET @SEQ = (SELECT COUNT(*) + 1 FROM `hierarchies` WHERE `parent` = `NEW`.`parent`);
                    SET @SEQ_ID = (SELECT AUTO_INCREMENT FROM `information_schema`.TABLES WHERE TABLE_NAME = \'hierarchies\' AND table_schema = DATABASE());
                    SET @SEQ_PARENT = (SELECT `sequence` FROM `hierarchies` WHERE `id` = `NEW`.`parent`);
                    SET NEW.sequence = CONCAT(@SEQ_PARENT, \'.\', @SEQ);
              END IF;
            END;;
        ');
    }

    public function down(): void
    {
        $driverName = DB::getDriverName();
        $this->setForeignKeyOff($driverName);
        $this->truncateTables();
        $this->setForeginKeyOn($driverName);
    }

    private function setForeignKeyOff(string $driverName): void
    {
        switch ($driverName) {
            case 'mysql':
                DB::statement('SET FOREIGN_KEY_CHECKS=0');
                break;
            case 'sqlite':
                DB::statement('PRAGMA foreign_keys = OFF');
                break;
        }
    }

    public function truncateTables(): void
    {
        $tableNames = Schema::getConnection()->getDoctrineSchemaManager()->listTableNames();
        foreach ($tableNames as $name) {
            //if you don't want to truncate migrations
            if ($name == 'migrations') {
                continue;
            }
            DB::table($name)->truncate();
        }
    }

    private function setForeginKeyOn(string $driverName): void
    {
        switch ($driverName) {
            case 'mysql':
                DB::statement('SET FOREIGN_KEY_CHECKS=1');
                break;
            case 'sqlite':
                DB::statement('PRAGMA foreign_keys = ON');
                break;
        }
    }

    private function charts()
    {
        Schema::create('charts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('slug')->unique();
            $table->string('name');
            $table->string('description');
            $table->string('type');
            $table->timestamp('updatedAt')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('createdAt')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->softDeletes('deletedAt');
        });
    }

    private function chartRoles()
    {
        Schema::create('chart_roles', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('size');
            $table->integer('order');
            $table->unsignedInteger('chartId');
            $table->unsignedInteger('roleId');
            $table->timestamp('updatedAt')->default(now());
            $table->timestamp('createdAt')->default(now());
            $table->softDeletes('deletedAt');

            $table->foreign('chartId')->references('id')->on('charts')->onDelete('cascade');
            $table->foreign('roleId')->references('id')->on('roles')->onDelete('cascade');
        });
    }

    private function ceaGiftCards()
    {
        Schema::connection('outsourced')->create('cea_gift_cards', static function (Blueprint $table) {
            $table->increments('id');
            $table->string('code')->unique();
            $table->string('partner')->nullable();
            $table->float('value')->nullable();
            $table->string('outsourcedId')->nullable();
            $table->string('reference')->nullable();

            $table->timestamp('createdAt')->default(now());
            $table->timestamp('updatedAt')->default(now());

            $table->softDeletes('deletedAt');
        });
    }

    private function integrations()
    {
        Schema::connection('outsourced')->create('integrations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('accessKey');
            $table->unsignedInteger('networkId')->nullable();
            $table->unsignedInteger('operatorId')->nullable();
            $table->unsignedInteger('userId')->nullable();
            $table->string('credentialVerifyUrl', 255)->nullable();
            $table->string('subdomain', 100)->nullable();
            $table->string('client', 100)->nullable();

            $table->foreign('networkId')->references('id')->on("networks");
            $table->foreign('operatorId')->references('id')->on("operators");
            $table->foreign('userId')->references('id')->on("users");
        });
    }

    private function operators()
    {
        Schema::create('operators', function (Blueprint $table) {
            $table->increments('id');
            $table->string('slug')->unique();
            $table->string('label')->nullable()->default(null);
            $table->json('availableServices')->nullable()->default(null);


            $table->timestamp('createdAt')->default(now());
            $table->timestamp('updatedAt')->default(now());

            $table->softDeletes('deletedAt');
        });
    }

    private function operatorsUsers()
    {
        Schema::create('operators_users', function (Blueprint $table) {
            $table->increments('id');

            $table->timestamp('createdAt')->default(now());
            $table->timestamp('updatedAt')->default(now());

            $table->softDeletes('deletedAt');

            $table->unsignedInteger('operatorId');
            $table->unsignedInteger('userId');

            $table->foreign('operatorId')->references('id')->on('operators')->onDelete('cascade');
            $table->foreign('userId')->references('id')->on('users')->onDelete('cascade');
        });
    }

    private function services()
    {

        Schema::create('services', function (Blueprint $table) {
            $table->increments('id');

            $table->string('sector');
            $table->string('operator');
            $table->string('operation');

            $table->timestamp('createdAt')->default(now());
            $table->timestamp('updatedAt')->default(now());

            $table->softDeletes('deletedAt');

        });
    }

    private function channels()
    {
        Schema::create('channels', static function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');

            $table->timestamp('createdAt')->default(now());
            $table->timestamp('updatedAt')->default(now());
            $table->softDeletes('deletedAt');
        });
    }

    private function networks_channels()
    {
        Schema::create('networks_channels', static function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('networkId');
            $table->unsignedInteger('channelId');

            $table->foreign('networkId')->references('id')->on('networks')->onDelete('cascade');
            $table->foreign('channelId')->references('id')->on('channels')->onDelete('cascade');
        });
    }

    private function users_channels()
    {
        Schema::create('users_channels', static function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('userId');
            $table->unsignedInteger('channelId');

            $table->foreign('userId')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('channelId')->references('id')->on('channels')->onDelete('cascade');
        });
    }

    private function availableServices()
    {
        Schema::create('availableServices', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('serviceId');
            $table->unsignedInteger('pointOfSaleId')->nullable(true);
            $table->unsignedInteger('networkId')->nullable(true);

            $table->timestamp('createdAt')->default(now());
            $table->timestamp('updatedAt')->default(now());
            $table->softDeletes('deletedAt');

            $table->foreign('serviceId')->references('id')->on('services')->onDelete('cascade');
            $table->foreign('pointOfSaleId')->references('id')->on('pointsOfSale')->onDelete('cascade');
            $table->foreign('networkId')->references('id')->on('networks')->onDelete('cascade');
        });
    }

    private function serviceOptions(): void
    {
        Schema::create('serviceOptions', static function (Blueprint $table) {
            $table->increments('id');
            $table->string('action');
            $table->timestamp('createdAt')->default(now());
            $table->timestamp('updatedAt')->default(now());

            $table->softDeletes('deletedAt');
        });
    }

    private function servicesServiceOptions(): void
    {
        Schema::create('services_serviceOptions', function (Blueprint $table) {
            $table->increments('id');

            $table->timestamp('createdAt')->default(now());
            $table->timestamp('updatedAt')->default(now());

            $table->unsignedInteger('availableServiceId');
            $table->unsignedInteger('optionId');

            $table->foreign('availableServiceId')->references('id')->on('availableServices')->onDelete('cascade');
            $table->foreign('optionId')->references('id')->on('serviceOptions')->onDelete('cascade');
        });
    }

    private function routes()
    {
        Schema::connection('outsourced')->create('routes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('uri');
            $table->string('method');
            $table->unique(['uri', 'method']);
        });
    }

    private function whitelists()
    {
        Schema::connection('outsourced')->create('whitelists', function (Blueprint $table) {
            $table->increments('id');
            $table->string('ip');
            $table->unsignedInteger('integrationId');
            $table->unique(['ip', 'integrationId']);

            $table->foreign('integrationId')->references('id')->on('integrations');
        });
    }

    private function integrations_routes(){
        Schema::connection('outsourced')->create('integrations_routes', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('integrationId');
            $table->unsignedInteger('routeId');
            $table->unique(['integrationId', 'routeId']);

            $table->foreign('integrationId')->references('id')->on('integrations');
            $table->foreign('routeId')->references('id')->on('routes');
        });
    }

    private function availableRedirects()
    {
        Schema::connection('outsourced')->create('available_redirects', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('integrationId');
            $table->string('redirectUrl');
            $table->boolean('defaultUrl')->default(true);
            $table->string('routeKey');

            $table->foreign('integrationId')->references('id')->on('integrations')->onDelete('cascade');
        });
    }

    private function products(): void
    {
        Schema::create('products', static function (Blueprint $table) {
            $table->increments('id');
            $table->string('code');
            $table->string('title');
            $table->unsignedTinyInteger('areaCode');
            $table->unsignedTinyInteger('loyaltyMonths');
            $table->decimal('price', 10, 2)->default(0);
            $table->unsignedInteger('internet')->default(0);
            $table->unsignedInteger('minutes')->default(0);

            $table->unsignedInteger('serviceId');

            $table->json('extras')->nullable();
            $table->json('original')->nullable();

            $table->index('code');
            $table->index('areaCode');

            $table->foreign('serviceId')->references('id')->on('services');

            $table->softDeletes('deletedAt');
            $table->timestamp('createdAt')->default(now());
            $table->timestamp('updatedAt')->default(now());
        });
    }

    private function accessLogs(): void
    {
        if (!Schema::hasTable('access_logs')) {
            Schema::create('access_logs', static function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('userId');
                $table->ipAddress('ip');
                $table->string('device', 100)->nullable();
                $table->enum('type', ['signin', 'signout'])->default('signin');
                $table->string('requestedUrl')->nullable();
                $table->timestamps();
                $table->foreign('userId')->references('id')->on('users');
            });
        }
    }

    private function evaluationsBonus(): void
    {
        Schema::create('evaluations_bonus', static function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('evaluationId');
            $table->double('goodValue', 8, 2);
            $table->double('averageValue', 8, 2);
            $table->double('defectValue', 8, 2);
            $table->string('sponsor');

            $table->softDeletes('deletedAt');
            $table->timestamp('createdAt')->default(now());
            $table->timestamp('updatedAt')->default(now());

            $table->foreign('evaluationId')->references('id')->on('evaluations');
        });
    }

    private function generaliProduct(): void
    {
        Schema::connection('outsourced')->create('generali_products', static function (Blueprint $table) {
            $table->increments('id');
            $table->string('slug')->nullable(false);
            $table->double('startingTrack', 8, 2)->nullable(false);
            $table->double('finalTrack', 8, 2)->nullable(false);
            $table->integer('twelveMonthsCode')->unique()->nullable(false);
            $table->integer('twentyFourMonthsCode')->unique()->nullable(false);
            $table->double('twelveMonthsPrice', 8, 2)->nullable(false);
            $table->double('twentyFourMonthsPrice', 8, 2)->nullable(false);

            $table->timestamp('createdAt')->default(now());
            $table->timestamp('updatedAt')->default(now());

            $table->softDeletes('deletedAt');
        });
    }

    private function userAuthAlternates(): void
    {
        Schema::create('userAuthAlternates', static function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('userId')->unique();
            $table->string('document');

            $table->timestamp('createdAt')->default(now());
            $table->timestamp('updatedAt')->default(now());
            $table->softDeletes('deletedAt');

            $table->foreign('userId')->references('id')->on('users');
        });
    }

    private function viaVarejoCoupons(): void
    {
        Schema::connection('outsourced')->create('via_varejo_coupons', static function (Blueprint $table) {
            $table->increments('id');
            $table->string('coupon');
            $table->string('campaign');
            $table->unsignedInteger('discountId')->unique();

            $table->timestamp('createdAt')->default(now());
            $table->timestamp('updatedAt')->default(now());
            $table->softDeletes('deletedAt');
        });
    }

    private function recommendations(): void
    {
        Schema::create('recommendations', static function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('statusCode')->default('ACTIVE');
            $table->string('registration');
            $table->unsignedInteger('pointOfSaleId')->nullable();

            $table->timestamp('createdAt')->default(now());
            $table->timestamp('updatedAt')->default(now());

            $table->softDeletes('deletedAt');

            $table->foreign('pointOfSaleId')->references('id')->on('pointsOfSale')->onDelete('cascade');
        });
    }
    
    private function bulletins(): void
    {
        Schema::create('bulletins', static function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->char('title', 70);
            $table->char('description', 240);
            $table->unsignedInteger('networkId');
            $table->boolean('status')->nullable(false);
            $table->string('urlImage');
            $table->date('initialDate');
            $table->date('finalDate');

            $table->timestamp('createdAt')->default(now());
            $table->timestamp('updatedAt')->default(now());
            $table->softDeletes('deletedAt');

            $table->foreign('networkId')->references('id')->on('networks')->onDelete('cascade');
        });
    }

    public function bulletinsRoles(): void
    {
        Schema::create('bulletins_roles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('bulletinId');
            $table->unsignedInteger('roleId');

            $table->timestamp('createdAt')->default(now());
            $table->timestamp('updatedAt')->default(now());
            $table->softDeletes('deletedAt');

            $table->foreign('bulletinId')->references('id')->on('bulletins')->onDelete('cascade');
            $table->foreign('roleId')->references('id')->on('roles')->onDelete('cascade');
        });
    }

    public function bulletinsPointsOfSales(): void
    {
        Schema::create('bulletins_pointsOfSales', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('bulletinId')->nullable(false);
            $table->unsignedInteger('pointOfSaleId')->nullable(false);

            $table->timestamp('createdAt')->default(now());
            $table->timestamp('updatedAt')->default(now());
            $table->softDeletes('deletedAt');

            $table->foreign('bulletinId')->references('id')->on('bulletins')->onDelete('cascade');
            $table->foreign('pointOfSaleId')->references('id')->on('pointsOfSale')->onDelete('cascade');
        });
    }

    public function bulletinsUsers(): void
    {
        Schema::create('bulletins_users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('bulletinId');
            $table->boolean('seen')->default(0)->nullable(false);
            $table->unsignedInteger('userId');

            $table->timestamp('createdAt')->default(now());
            $table->timestamp('updatedAt')->default(now());
            $table->softDeletes('deletedAt');

            $table->foreign('bulletinId')->references('id')->on('bulletins')->onDelete('cascade');
            $table->foreign('userId')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function terms(): void
    {
        Schema::create('terms', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('title');
            $table->string('urlEmbed');
            $table->boolean('active')->default(1);
            $table->enum('type', TypeTermsEnum::TERM_TYPE);
            $table->timestamp('createdAt')->default(now());
            $table->timestamp('updatedAt')->default(now());
            $table->softDeletes('deletedAt');
        });
    }

    public function userTerm(): void
    {
        Schema::create('userTerms', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedInteger('userId');
            $table->unsignedInteger('termId');
            $table->enum('status', StatusUserTermsEnum::AVAILABLE_STATUS)
                ->default(StatusUserTermsEnum::VIEWED);
            $table->timestamp('createdAt')->default(now());
            $table->timestamp('updatedAt')->default(now());
            $table->softDeletes('deletedAt');

            $table->foreign('userId')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('termId')->references('id')->on('terms')->onDelete('cascade');
        });
    }
}
