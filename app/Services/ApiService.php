<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ApiService
{
    protected $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.api.base_url');
    }

    public function searchAppointments($params)
    {
        try {
            $response = Http::timeout(30)->get($this->baseUrl . '/search', $params);
            return $response->json();
        } catch (\Exception $e) {
            Log::error('API Error: ' . $e->getMessage());
            return ['status' => 0, 'error_msg' => 'Connection error. Please try again.'];
        }
    }

    public function markAsCheckIn($appointmentId)
    {
        try {
            $response = Http::timeout(30)->post($this->baseUrl . '/checkin', [
                'id' => $appointmentId
            ]);
            return $response->json();
        } catch (\Exception $e) {
            Log::error('API Error: ' . $e->getMessage());
            return ['status' => 0, 'error_msg' => 'Connection error. Please try again.'];
        }
    }

    public function getLanguages()
    {
        try {
            $response = Http::timeout(30)->get($this->baseUrl . '/languages');
            return $response->json();
        } catch (\Exception $e) {
            Log::error('API Error: ' . $e->getMessage());
            return ['status' => 0, 'error_msg' => 'Connection error.'];
        }
    }

    public function getServices()
    {
        try {
            $response = Http::timeout(30)->get($this->baseUrl . '/services');
            return $response->json();
        } catch (\Exception $e) {
            Log::error('API Error: ' . $e->getMessage());
            return ['status' => 0, 'error_msg' => 'Connection error.'];
        }
    }

    public function getDisciplines()
    {
        try {
            $response = Http::timeout(30)->get($this->baseUrl . '/disciplines');
            return $response->json();
        } catch (\Exception $e) {
            Log::error('API Error: ' . $e->getMessage());
            return ['status' => 0, 'error_msg' => 'Connection error.'];
        }
    }

    public function getInsurances()
    {
        try {
            $response = Http::timeout(30)->get($this->baseUrl . '/insurances');
            return $response->json();
        } catch (\Exception $e) {
            Log::error('API Error: ' . $e->getMessage());
            return ['status' => 0, 'error_msg' => 'Connection error.'];
        }
    }

    public function getAgencies()
    {
        try {
            $response = Http::timeout(30)->get($this->baseUrl . '/agencies');
            return $response->json();
        } catch (\Exception $e) {
            Log::error('API Error: ' . $e->getMessage());
            return ['status' => 0, 'error_msg' => 'Connection error.'];
        }
    }

    public function getLocations()
    {
        try {
            $response = Http::timeout(30)->get($this->baseUrl . '/locations');
            return $response->json();
        } catch (\Exception $e) {
            Log::error('API Error: ' . $e->getMessage());
            return ['status' => 0, 'error_msg' => 'Connection error.'];
        }
    }

    public function getAppointmentTimes($locationId, $appointmentDate)
    {
        try {
            $response = Http::timeout(30)->get($this->baseUrl . '/appointment-times', [
                'location_id' => $locationId,
                'appointment_date' => $appointmentDate,
            ]);
            return $response->json();
        } catch (\Exception $e) {
            Log::error('API Error: ' . $e->getMessage());
            return ['status' => 0, 'error_msg' => 'Connection error.'];
        }
    }

    public function getAllDropdowns()
    {
        try {
            $response = Http::timeout(30)->get($this->baseUrl . '/dropdowns');
            return $response->json();
        } catch (\Exception $e) {
            Log::error('API Error: ' . $e->getMessage());
            return ['status' => 0, 'error_msg' => 'Connection error.'];
        }
    }

    public function createAppointment($data)
    {
        try {
            $response = Http::timeout(30)->post($this->baseUrl . '/create', $data);
            return $response->json();
        } catch (\Exception $e) {
            Log::error('API Error: ' . $e->getMessage());
            return ['status' => 0, 'error_msg' => 'Connection error. Please try again.'];
        }
    }

    protected static function client(?string $token = null, array $headers = [])
    {
        $token = env('KIOSK_TOKEN');
        $request = Http::timeout(30)->acceptJson();

        if ($token) {
            $request = $request->withToken($token);
        }

        if (!empty($headers)) {
            $request = $request->withHeaders($headers);
        }

        return $request;
    }

    public static function get(string $url, array $params = [], ?string $token = null, array $headers = [])
    {
        return self::client($token, $headers)
            ->get($url, $params)
            ->json();
    }

    public static function post(string $url, array $data = [], ?string $token = null, array $headers = [])
    {
        return self::client($token, $headers)
            ->post($url, $data)
            ->json();
    }

    public static function put(string $url, array $data = [], ?string $token = null, array $headers = [])
    {
        return self::client($token, $headers)
            ->put($url, $data)
            ->json();
    }

    public static function delete(string $url, array $data = [], ?string $token = null, array $headers = [])
    {
        return self::client($token, $headers)
            ->delete($url, $data)
            ->json();
    }
}