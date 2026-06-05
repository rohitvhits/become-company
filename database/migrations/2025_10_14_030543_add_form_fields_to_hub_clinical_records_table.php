<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('hub_clinical_records', function (Blueprint $table) {
            // Patient Information Fields
            $table->string('patient_name')->nullable()->after('notes');
            $table->date('patient_dob')->nullable()->after('patient_name');
            $table->enum('patient_gender', ['male', 'female'])->nullable()->after('patient_dob');
            $table->text('patient_address')->nullable()->after('patient_gender');

            // Medical Form Fields
            $table->string('chief_complaint')->nullable()->after('patient_address');
            $table->string('reason_for_visit')->nullable()->after('chief_complaint');
            $table->text('history_of_present_illness')->nullable()->after('reason_for_visit');
            $table->string('medical_history')->nullable()->after('history_of_present_illness');
            $table->string('current_medications')->nullable()->after('medical_history');
            $table->string('past_surgical_history')->nullable()->after('current_medications');
            $table->string('social_history')->nullable()->after('past_surgical_history');

            // Review of Systems
            $table->string('cardiovascular')->nullable()->after('social_history');
            $table->string('constitutional')->nullable()->after('cardiovascular');
            $table->string('ent')->nullable()->after('constitutional');
            $table->string('endocrine')->nullable()->after('ent');
            $table->string('gastrointestinal')->nullable()->after('endocrine');
            $table->string('genitourinary')->nullable()->after('gastrointestinal');
            $table->string('musculoskeletal')->nullable()->after('genitourinary');
            $table->string('neurologic')->nullable()->after('musculoskeletal');
            $table->string('ophthalmologic')->nullable()->after('neurologic');
            $table->string('psychiatric')->nullable()->after('ophthalmologic');
            $table->string('respiratory')->nullable()->after('psychiatric');
            $table->string('skin')->nullable()->after('respiratory');

            // Vitals
            $table->string('bp')->nullable()->after('skin');
            $table->string('pulse')->nullable()->after('bp');
            $table->string('resp')->nullable()->after('pulse');
            $table->string('temp')->nullable()->after('resp');
            $table->string('weight')->nullable()->after('temp');
            $table->string('height')->nullable()->after('weight');
            $table->string('bmi')->nullable()->after('height');

            // Physical Exam
            $table->string('appearance')->nullable()->after('bmi');
            $table->string('heent')->nullable()->after('appearance');
            $table->string('neck')->nullable()->after('heent');
            $table->string('cardiovascular_exam')->nullable()->after('neck');
            $table->string('lungs')->nullable()->after('cardiovascular_exam');
            $table->string('abdomen')->nullable()->after('lungs');
            $table->string('extremities')->nullable()->after('abdomen');
            $table->string('neuro')->nullable()->after('extremities');

            // Diagnosis, Assessment, Instructions, Medications
            $table->text('diagnosis')->nullable()->after('neuro');
            $table->text('assessment_plan')->nullable()->after('diagnosis');
            $table->text('instructions')->nullable()->after('assessment_plan');
            $table->text('medications')->nullable()->after('instructions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('hub_clinical_records', function (Blueprint $table) {
            $table->dropColumn([
                'patient_name', 'patient_dob', 'patient_gender', 'patient_address',
                'chief_complaint', 'reason_for_visit', 'history_of_present_illness',
                'medical_history', 'current_medications', 'past_surgical_history', 'social_history',
                'cardiovascular', 'constitutional', 'ent', 'endocrine', 'gastrointestinal',
                'genitourinary', 'musculoskeletal', 'neurologic', 'ophthalmologic',
                'psychiatric', 'respiratory', 'skin',
                'bp', 'pulse', 'resp', 'temp', 'weight', 'height', 'bmi',
                'appearance', 'heent', 'neck', 'cardiovascular_exam', 'lungs',
                'abdomen', 'extremities', 'neuro',
                'diagnosis', 'assessment_plan', 'instructions', 'medications'
            ]);
        });
    }
};
