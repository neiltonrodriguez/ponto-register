<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('cpf', 14)->unique()->after('email');
            $table->string('position')->after('cpf');
            $table->date('birth_date')->after('position');
            $table->string('zip_code', 9)->after('birth_date');
            $table->string('address')->after('zip_code');
            $table->string('number')->after('address');
            $table->string('complement')->nullable()->after('number');
            $table->string('neighborhood')->after('complement');
            $table->string('city')->after('neighborhood');
            $table->string('state', 2)->after('city');
            $table->enum('role', ['employee', 'admin'])->default('employee')->after('state');
            $table->unsignedBigInteger('admin_id')->nullable()->after('role');
            
            $table->foreign('admin_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['admin_id']);
            $table->dropColumn([
                'name', 'email', 'cpf', 'position', 'birth_date', 'zip_code', 'address', 'number', 
                'complement', 'neighborhood', 'city', 'state', 'role', 'admin_id'
            ]);
        });
    }
};