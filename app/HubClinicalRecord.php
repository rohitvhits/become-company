<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HubClinicalRecord extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'hub_clinical_records';

    protected $fillable = [
        'hub_record_id',
        'name',
        'pdf_type',
        'notes',
        'visit_date',
        'doctor_name',
        'excuse_from',
        'excuse_to',
        'pdf_content',
        'pdf_path',
        'created_by',
        // Patient Information Fields
        'patient_name',
        'patient_dob',
        'patient_gender',
        'patient_address',
        // Medical Form Fields
        'chief_complaint',
        'reason_for_visit',
        'history_of_present_illness',
        'medical_history',
        'current_medications',
        'past_surgical_history',
        'social_history',
        'allergies',
        // Review of Systems
        'cardiovascular',
        'constitutional',
        'ent',
        'endocrine',
        'gastrointestinal',
        'genitourinary',
        'musculoskeletal',
        'neurologic',
        'ophthalmologic',
        'psychiatric',
        'respiratory',
        'skin',
        // Vitals
        'bp',
        'pulse',
        'resp',
        'temp',
        'weight',
        'height',
        'bmi',
        // Physical Exam
        'appearance',
        'heent',
        'neck',
        'cardiovascular_exam',
        'lungs',
        'abdomen',
        'extremities',
        'neuro',
        // Diagnosis, Assessment, Instructions, Medications
        'diagnosis',
        'assessment_plan',
        'instructions',
        'medications',
        // Medical Note Specific Fields
        'excuse',
        'work',
        'school',
        'other',
        'injury',
        'illness',
        'due_to_other',
        'doc_comment'
    ];

    protected $dates = [
        'visit_date',
        'excuse_from',
        'excuse_to',
        // 'patient_dob',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function hubRecord()
    {
        return $this->belongsTo(\App\Model\HubRecord::class, 'hub_record_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(\App\User::class, 'created_by');
    }
}
