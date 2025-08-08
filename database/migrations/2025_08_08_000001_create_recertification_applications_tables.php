<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Main applications table - use SQL Server connection
        Schema::connection('sqlsrv')->create('recertification_applications', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('application_reference')->unique();
            $table->date('application_date')->nullable();

            // Applicant type: Individual, Corporate, Government Body, Multiple Owners
            $table->string('applicant_type', 50)->nullable();

            // Corporate-specific quick columns (detailed fields are still captured in payload)
            $table->string('organisation_name')->nullable();
            $table->string('cac_registration_no', 100)->nullable();
            $table->string('type_of_organisation')->nullable();
            $table->string('type_of_business')->nullable();

            // Full raw payload for flexibility (stored as NVARCHAR(MAX) on SQL Server)
            $table->text('payload');

            $table->timestamps();
        });

        // Owners table for Multiple Owners support
        Schema::connection('sqlsrv')->create('recertification_owners', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('application_id');

            // Owner personal details
            $table->string('surname');
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('title')->nullable();
            $table->string('occupation')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('nationality', 100)->nullable();
            $table->string('state_of_origin')->nullable();
            $table->string('lga_of_origin')->nullable();
            $table->string('nin', 50)->nullable();
            $table->string('gender', 10)->nullable();
            $table->string('marital_status', 20)->nullable();
            $table->string('maiden_name')->nullable();

            // File storage path for the uploaded passport photograph
            $table->string('passport_photo_path')->nullable();

            $table->timestamps();

            // FK -> applications
            $table->foreign('application_id')
                ->references('id')
                ->on('recertification_applications')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('sqlsrv')->dropIfExists('recertification_owners');
        Schema::connection('sqlsrv')->dropIfExists('recertification_applications');
    }
};
