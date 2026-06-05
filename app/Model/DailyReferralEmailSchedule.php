<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\User;

class DailyReferralEmailSchedule extends Model
{
    use SoftDeletes;

    protected $table = 'daily_referral_email_schedules';
    protected $guarded = ['id'];

    protected $casts = [
        'recipients' => 'array',
        'cc_emails' => 'array',
        'send_days' => 'array',
        'agency_ids' => 'array',
        'service_ids' => 'array',
        'assigned_to' => 'array',
        'branch_ids' => 'array',
        'is_active' => 'boolean',
        'show_outliers' => 'boolean',
        'show_highest_weight' => 'boolean',
        'show_forms_breakdown' => 'boolean',
        'show_referral_sources' => 'boolean',
        'show_resolution' => 'boolean',
        'show_requests_per_agency' => 'boolean',
        'show_portal_processing' => 'boolean',
        'show_refusals_insights' => 'boolean',
        'show_cancellations_insights' => 'boolean',
        'show_non_mdo_forms' => 'boolean',
        'show_mdo_completed' => 'boolean',
        'show_updates_per_agency' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
        'last_sent_at' => 'datetime',
        'period_days' => 'integer',
        'monthly_date' => 'integer'
    ];

    protected $dates = [
        'start_date',
        'end_date',
        'last_sent_at',
        'created_at',
        'updated_at'
    ];

    /**
     * Get the user who created this schedule
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Check if the schedule should run today
     */
    public function shouldRunTodayQurey()
    {
        if (!$this->is_active) {
            return false;
        }

        // Check if we're within the date range
        $currentDate = now()->toDateString();

        if ($this->start_date && $currentDate < $this->start_date->toDateString()) {
            return false;
        }

        if ($this->end_date && $currentDate > $this->end_date->toDateString()) {
            return false;
        }

        // Check frequency-based logic
        switch ($this->frequency) {
            case 'daily':
                return $this->shouldRunDaily();
            case 'weekly':
                return $this->shouldRunWeekly();
            case 'monthly':
                return $this->shouldRunMonthly();
            default:
                // Fallback to old logic for existing schedules
                return $this->shouldRunDaily();
        }
    }

    /**
     * Check if should run today for daily frequency
     */
    private function shouldRunDaily()
    {
        $today = now()->format('l'); // Get day name (Monday, Tuesday, etc.)
        $todayLower = strtolower($today);

        // For daily frequency, check if today is in the send_days array
        if ($this->send_days && !in_array($todayLower, $this->send_days)) {
            return false;
        }

        // Check custom period (every X days)
        if ($this->period_days && $this->period_days > 1) {
            if (!$this->last_sent_at) {
                return true; // First time, should run
            }

            $daysSinceLastSent = now()->diffInDays($this->last_sent_at);
            return $daysSinceLastSent >= $this->period_days;
        }

        return true;
    }

    /**
     * Check if should run today for weekly frequency
     */
    private function shouldRunWeekly()
    {
        $today = strtolower(now()->format('l'));

        // Check if today matches the weekly day
        if ($this->weekly_day && $this->weekly_day !== $today) {
            return false;
        }

        // Check if it's been at least a week since last sent
        if ($this->last_sent_at && now()->diffInDays($this->last_sent_at) < 7) {
            return false;
        }

        return true;
    }

    /**
     * Check if should run today for monthly frequency
     */
    private function shouldRunMonthly()
    {
        $today = now()->day;
        $daysInCurrentMonth = now()->daysInMonth;

        // Handle edge cases for monthly dates
        $targetDate = $this->monthly_date;

        // Handle end of month option (indicated by -1)
        if ($targetDate === -1 || $targetDate < 0) {
            $targetDate = $daysInCurrentMonth; // Last day of current month
        } else if ($targetDate > $daysInCurrentMonth) {
            // If the target date doesn't exist in the current month, use the last day of the month
            $targetDate = $daysInCurrentMonth;
        }

        // Check if today matches the target date
        if ($targetDate !== $today) {
            return false;
        }

        // Check if it's been at least a month since last sent
        if ($this->last_sent_at && now()->diffInMonths($this->last_sent_at) < 1) {
            return false;
        }

        return true;
    }

    /**
     * Check if the schedule should run at the current time
     * Uses a 2-minute grace period to handle processing delays
     */
    public function shouldRunNow()
    {
        if (!$this->shouldRunTodayQurey()) {
            return false;
        }

        $currentTime = now();

        // Parse schedule time - stored as TIME in database (H:i:s format)
        if ($this->send_time instanceof \Carbon\Carbon) {
            $scheduleTime = $this->send_time;
        } else {
            // Parse time string (e.g., "04:10:00" or "04:10")
            $timeStr = strlen($this->send_time) > 5 ? $this->send_time : $this->send_time . ':00';
            $scheduleTime = \Carbon\Carbon::parse($timeStr);
        }

        // Allow a 2-minute grace period AFTER the scheduled time
        // This accounts for cron processing delays when handling multiple schedules
        $gracePeriodEnd = $scheduleTime->copy()->addMinutes(2);

        // Check if current time is between scheduled time and grace period end
        $isWithinGracePeriod = $currentTime->between($scheduleTime, $gracePeriodEnd);

        return $isWithinGracePeriod;
    }

    /**
     * Check if already sent today
     */
    public function alreadySentToday()
    {
        if (!$this->last_sent_at) {
            return false;
        }

        return $this->last_sent_at->isToday();
    }

    /**
     * Mark as sent
     */
    public function markAsSent()
    {
        $this->update([
            'last_sent_at' => now()
        ]);
    }

    /**
     * Get formatted send days
     */
    public function getFormattedSendDaysAttribute()
    {
        return implode(', ', array_map('ucfirst', $this->send_days));
    }

    /**
     * Get formatted frequency description
     */
    public function getFrequencyDescriptionAttribute()
    {
        switch ($this->frequency) {
            case 'daily':
                if ($this->period_days && $this->period_days > 1) {
                    return "Every {$this->period_days} days";
                }
                return 'Daily (' . $this->formatted_send_days . ')';
            case 'weekly':
                return 'Weekly (' . ucfirst($this->weekly_day) . ')';
            case 'monthly':
                if ($this->monthly_date === -1 || $this->monthly_date < 0) {
                    $dateText = 'End of Month';
                } else {
                    $dateText = $this->monthly_date ? "Day {$this->monthly_date}" : 'Same day';
                    if ($this->monthly_date && $this->monthly_date > 28) {
                        $dateText .= '*'; // Add asterisk to indicate edge case handling
                    }
                }
                return 'Monthly (' . $dateText . ')';
            default:
                return 'Daily';
        }
    }

    /**
     * Get the effective monthly date for the current month
     */
    public function getEffectiveMonthlyDate($date = null)
    {
        if ($this->frequency !== 'monthly') {
            return null;
        }

        $targetDate = $date ? \Carbon\Carbon::parse($date) : now();
        $daysInMonth = $targetDate->daysInMonth;

        // Handle end of month option (indicated by -1)
        if ($this->monthly_date === -1 || $this->monthly_date < 0) {
            return $daysInMonth; // Last day of month
        }

        if (!$this->monthly_date) {
            return null;
        }

        // If the target date doesn't exist in the current month, use the last day of the month
        return min($this->monthly_date, $daysInMonth);
    }

    /**
     * Get all recipients (both to and cc)
     */
    public function getAllRecipientsAttribute()
    {
        $recipients = $this->recipients ?? [];
        $ccEmails = $this->cc_emails ?? [];

        return array_merge($recipients, $ccEmails);
    }

    /**
     * Scope for active schedules
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for schedules that should run today
     */
    public function scopeShouldRunToday($query)
    {
        $today = strtolower(now()->format('l'));
        $currentDate = now()->toDateString();
        return $query->active()
            ->whereJsonContains('send_days', $today)
            ->where(function ($q) use ($currentDate) {
                $q->whereNull('start_date')
                    ->orWhere('start_date', '<=', $currentDate);
            })
            ->where(function ($q) use ($currentDate) {
                $q->whereNull('end_date')
                    ->orWhere('end_date', '>=', $currentDate);
            });
    }
}
