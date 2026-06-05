<?php

namespace App\Services;

use App\Model\PaymentLogService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PaymentLogServiceService
{
    /**
     * Save services for a payment log
     *
     * @param int $paymentLogId
     * @param array $services
     * @return bool
     */
    public function saveServices($paymentLogId, $services)
    {
        try {
            DB::beginTransaction();

            // Soft delete existing services for this payment log
            PaymentLogService::where('payment_log_id', $paymentLogId)
                ->update([
                    'delete_flag' => 'Y',
                    'deleted_by' => Auth::id(),
                    'deleted_at' => now()
                ]);

            // Insert new services
            foreach ($services as $service) {
                if (!empty($service['service_name'])) {
                    $amount = floatval($service['amount'] ?? 0);

                    // Only save if amount is greater than 0
                    if ($amount > 0) {
                        PaymentLogService::create([
                            'payment_log_id' => $paymentLogId,
                            'service_name' => $service['service_name'],
                            'total_amount' => $amount,
                            'created_by' => Auth::id(),
                            'delete_flag' => 'N'
                        ]);
                    }
                }
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Get services for a payment log
     *
     * @param int $paymentLogId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getServices($paymentLogId)
    {
        return PaymentLogService::where('payment_log_id', $paymentLogId)
            ->active()
            ->orderBy('created_at', 'asc')
            ->get();
    }

    /**
     * Delete service by ID
     *
     * @param int $serviceId
     * @return bool
     */
    public function deleteService($serviceId)
    {
        $service = PaymentLogService::find($serviceId);

        if (!$service) {
            return false;
        }

        $service->delete_flag = 'Y';
        $service->deleted_by = Auth::id();
        $service->deleted_at = now();
        $service->save();

        return true;
    }

    /**
     * Update a single service
     *
     * @param int $serviceId
     * @param array $data
     * @return bool
     */
    public function updateService($serviceId, $data)
    {
        $service = PaymentLogService::find($serviceId);

        if (!$service) {
            return false;
        }

        $service->service_name = $data['service_name'] ?? $service->service_name;
        $service->total_amount = $data['amount'] ?? $service->total_amount;
        $service->updated_by = Auth::id();
        $service->save();

        return true;
    }

    /**
     * Get total amount for a payment log from services
     *
     * @param int $paymentLogId
     * @return float
     */
    public function getTotalAmount($paymentLogId)
    {
        return PaymentLogService::where('payment_log_id', $paymentLogId)
            ->active()
            ->sum('total_amount');
    }

    /**
     * Bulk create services
     *
     * @param array $servicesData Array of services with payment_log_id, service_name, total_amount
     * @return bool
     */
    public function bulkCreateServices($servicesData)
    {
        try {
            DB::beginTransaction();

            foreach ($servicesData as $data) {
                $amount = floatval($data['total_amount'] ?? 0);

                // Only save if amount is greater than 0
                if ($amount > 0 && !empty($data['service_name'])) {
                    PaymentLogService::create([
                        'payment_log_id' => $data['payment_log_id'],
                        'service_name' => $data['service_name'],
                        'total_amount' => $amount,
                        'created_by' => Auth::id(),
                        'delete_flag' => 'N'
                    ]);
                }
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Check if payment log has services
     *
     * @param int $paymentLogId
     * @return bool
     */
    public function hasServices($paymentLogId)
    {
        return PaymentLogService::where('payment_log_id', $paymentLogId)
            ->active()
            ->exists();
    }

    /**
     * Get services formatted for invoice generation
     *
     * @param int $paymentLogId
     * @return array
     */
    public function getServicesForInvoice($paymentLogId)
    {
        $services = $this->getServices($paymentLogId);

        $formatted = [];
        foreach ($services as $service) {
            $formatted[] = [
                'service_name' => $service->service_name,
                'amount' => floatval($service->total_amount)
            ];
        }

        return $formatted;
    }
}
