<?php

namespace App\Model;

use App\Model\HubCompany;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class HubRecord extends Model
{
	use Notifiable;
	public $timestamps = false;
	protected $table = 'hub_record';
	protected $fillable = ['id', 'full_name', 'first_name', 'middle_name', 'last_name', 'dob', 'gender', 'deleted_flag', 'created_date', 'created_by', 'updated_date', 'updated_by', 'deleted_date', 'deleted_by', 'phone', 'agency_id', 'mobile', 'language', 'location_id', 'assign_user_id', 'address1', 'address2', 'state', 'city', 'zip_code', 'county', 'email', 'status', 'import_flag', 'deactivated_date', 'deactivated_by', 'ssn', 'parent_id', 'relation_ship', 'otp', 'otp_expired_time', 'token', 'token_expired_time', 'is_dependent'];

	protected $dates = [
		'dob',
		'created_date',
		'updated_date',
		'deactivated_date'
	];
	protected $casts = [
		'dob' => 'date'
	];
	public function users()
	{
		return $this->belongsTo(User::class, 'created_by', 'id');
	}

	public function languages()
	{
		return $this->belongsTo(Language::class, 'language', 'id');
	}

	public function locations()
	{
		return $this->belongsTo(LocationMaster::class, 'location_id', 'id');
	}

	public function assignToUser()
	{
		return $this->hasOne(User::class, 'id', 'assign_user_id');
	}

	public function agencyDetail()
	{
		return $this->hasOne(HubCompany::class, 'id', 'agency_id');
	}

	public function usersUpdate()
	{
		return $this->belongsTo(User::class, 'updated_by', 'id');
	}

	// Relationship with hub record agencies
	public function hubRecordAgencies()
	{
		return $this->hasMany(HubRecordAgency::class, 'hub_record_id', 'id');
	}

	public function agencyRelations()
	{
		return $this->hasMany(HubRecordAgency::class, 'hub_record_id');
	}

	public function agencyRelation($agencyId)
	{
		return $this->hasOne(HubRecordAgency::class, 'hub_record_id')->where('agency_id', $agencyId);
	}

	public function creator()
	{
		return $this->belongsTo(User::class, 'created_by');
	}

	public function updater()
	{
		return $this->belongsTo(User::class, 'updated_by');
	}

	public function language()
	{
		return $this->belongsTo(Language::class, 'language');
	}

	public function location()
	{
		return $this->belongsTo(LocationMaster::class, 'location_id');
	}

	public static function getFieldsList()
	{
		return [
			'last_name' => 'Last Name',
			'first_name' => 'First Name',
			'middle_name' => 'Middle Initial',
			'dob' => 'Birth Date',
			'gender' => 'Gender',
			'email' => 'Email Address',
			'address1' => 'Primary Address 1',
			'address2' => 'Primary Address 2',
			'city' => 'Primary City',
			'state' => 'Primary State',
			'zip_code' => 'Primary Zip Code',
			'phone' => 'Home Phone',
			'mobile' => 'Mobile Phone',
			'ssn' => 'SSN',
			// Fields from hub_record_agency table
			'hire_date' => 'Hire Date',
			'work_contact' => 'Work Contact',
			'work_email' => 'Work Email',
			'last_worked_date' => 'Last Worked Date',
			'member_id' => 'Member Id',
			'employee_code' => 'Employee Code'
		];
	}

	public static function getUniqueFields()
	{
		return [
			'email' => 'Email Address',
			'ssn' => 'SSN',
			'member_id' => 'Member Id',
			'employee_code' => 'Employee Code'
		];
	}

	// Get employee with agency relationship data
	public static function getEmployeeWithAgencyData($hubRecordId, $agencyId)
	{
		return self::select('hub_record.*', 'hub_record_agency.*')
			->leftJoin('hub_record_agency', function ($join) use ($agencyId) {
				$join->on('hub_record.id', '=', 'hub_record_agency.hub_record_id')
					->where('hub_record_agency.agency_id', '=', $agencyId);
			})
			->where('hub_record.id', $hubRecordId)
			->first();
	}

	public function getFullNameAttribute()
	{
		return trim($this->first_name . ' ' . $this->middle_name . ' ' . $this->last_name);
	}

	public function scopeActive($query)
	{
		return $query->where('status', 'Active');
	}

	public function scopeInactive($query)
	{
		return $query->where('status', 'Inactive');
	}

	public function scopeByAgency($query, $agencyId)
	{
		return $query->where('agency_id', $agencyId);
	}

	public function scopeEmployees($query)
	{
		return $query->where('deleted_flag', 'N');
	}
}
