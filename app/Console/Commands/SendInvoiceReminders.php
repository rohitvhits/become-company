<?php

namespace App\Console\Commands;

use App\Model\Invoice;
use App\Jobs\SendInvoiceReminder;
use Illuminate\Console\Command;
use Carbon\Carbon;

class SendInvoiceReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoices:send-reminders {--dry-run : Show what would be sent without actually sending}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send payment reminders for invoices that are due soon or overdue';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->info('DRY RUN MODE - No emails will be sent');
            $this->info('');
        }

        // Get invoices due in 3 days (configurable)
        $reminderDays = config('invoice.reminder_days', 3);
        $dueSoonInvoices = Invoice::with('agency')
            ->where('status', 'sent')
            ->whereDate('due_date', '=', Carbon::now()->addDays($reminderDays))
            ->get();

        // Get overdue invoices
        $overdueInvoices = Invoice::with('agency')
            ->where('status', '!=', 'paid')
            ->where('status', '!=', 'draft')
            ->whereDate('due_date', '<', Carbon::now())
            ->get();

        $this->info("Found {$dueSoonInvoices->count()} invoices due in {$reminderDays} days");
        $this->info("Found {$overdueInvoices->count()} overdue invoices");
        $this->info('');

        // Send reminders for invoices due soon
        if ($dueSoonInvoices->count() > 0) {
            $this->info('Processing due soon reminders:');
            $this->sendReminders($dueSoonInvoices, 'reminder', $dryRun);
        }

        // Send overdue notices
        if ($overdueInvoices->count() > 0) {
            $this->info('Processing overdue notices:');
            $this->sendReminders($overdueInvoices, 'overdue', $dryRun);
        }

        if ($dueSoonInvoices->count() === 0 && $overdueInvoices->count() === 0) {
            $this->info('No reminders to send today.');
        }

        return 0;
    }

    /**
     * Send reminders for a collection of invoices
     *
     * @param \Illuminate\Database\Eloquent\Collection $invoices
     * @param string $type
     * @param bool $dryRun
     */
    protected function sendReminders($invoices, $type, $dryRun)
    {
        $sent = 0;
        $errors = 0;

        foreach ($invoices as $invoice) {
            try {
                if ($dryRun) {
                    $this->line("  Would send {$type} for invoice {$invoice->invoice_number} to {$invoice->agency->email}");
                } else {
                    SendInvoiceReminder::dispatch($invoice, $type);
                    $this->line("  ✓ Queued {$type} for invoice {$invoice->invoice_number} to {$invoice->agency->email}");
                }
                $sent++;
            } catch (\Exception $e) {
                $this->error("  ✗ Failed to queue {$type} for invoice {$invoice->invoice_number}: " . $e->getMessage());
                $errors++;
            }
        }

        $this->info("Processed {$sent} {$type} reminders" . ($errors > 0 ? " with {$errors} errors" : ""));
        $this->info('');
    }
}