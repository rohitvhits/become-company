<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class SiteSetting extends Model
{
	use Notifiable;
	public $timestamps = false;
	protected $table = 'site_setting';
	protected $fillable = ['id', 'cancellation_email', 'hub_nybest_email', 'mdo_upload_notify_email', 'telehealth_upload_notify_email', 'agency_notification_extra_users', 'del_flag', 'created_date', 'created_by', 'updated_date', 'updated_by', 'document_dashboard_status','is_send_agency_merge_mail_notify','is_send_agency_merge_liaison_notify', 'health_check_enabled', 'announcement_popup_enabled', 'supervision_due_date_months', 'task_health_cron_enabled','caregiver_email_notification','patient_email_notification','telehealth_time_frame_hours'];
}
