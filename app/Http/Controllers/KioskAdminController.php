<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Model\KioskAppointment;
use App\Model\KioskAppointmentDocument;
use App\Services\ApiService;
use App\Services\LocationMasterService;

class KioskAdminController extends Controller
{
    protected $apiService;
    protected $LocationMasterService;

    public function __construct(ApiService $apiService, LocationMasterService $locationMasterService)
    {
        $this->apiService = $apiService;
        $this->locationMasterService = $locationMasterService;
    }

    /**
     * Get locations mapped by ID
     */
    protected function getLocationsMap()
    {
        $locations = $this->locationMasterService->AllListWithoutPaginate();
        $locationsMap = [];
        if (isset($locations)) {
            foreach ($locations as $location) {
                $locationsMap[$location['id']] = $location['address1'] ?? $location['name'] ?? 'Location ' . $location['id'];
            }
        }
        return $locationsMap;
    }

    /**
     * Get services mapped by ID
     */
    protected function getServicesMap()
    {
        $services = $this->apiService->getServices();
        $servicesMap = [];
        if (isset($services['data'])) {
            foreach ($services['data'] as $service) {
                $servicesMap[$service['id']] = $service['name'] ?? $service['service_name'] ?? 'Service ' . $service['id'];
            }
        }
        return $servicesMap;
    }

    /**
     * Get languages mapped by ID
     */
    protected function getLanguagesMap()
    {
        $languages = $this->apiService->getLanguages();
        $languagesMap = [];
        if (isset($languages['data'])) {
            foreach ($languages['data'] as $language) {
                $languagesMap[$language['id']] = $language['name'] ?? $language['language_name'] ?? 'Language ' . $language['id'];
            }
        }
        return $languagesMap;
    }

    /**
     * Get insurances mapped by ID
     */
    protected function getInsurancesMap()
    {
        $insurances = $this->apiService->getInsurances();
        $insurancesMap = [];
        if (isset($insurances['data'])) {
            foreach ($insurances['data'] as $insurance) {
                $insurancesMap[$insurance['id']] = $insurance['name'] ?? $insurance['insurance_name'] ?? 'Insurance ' . $insurance['id'];
            }
        }
        return $insurancesMap;
    }

    /**
     * Show login form
     */
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.login');
    }

    /**
     * Handle login submission
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended(route('admin.dashboard'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Handle logout
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->with('success', 'You have been logged out.');
    }

    /**
     * Dashboard
     */
    public function dashboard()
    {
        $totalAppointments = KioskAppointment::count();
        $todayAppointments = KioskAppointment::whereDate('created_at', today())->count();
        $checkedInToday = KioskAppointment::whereDate('created_at', today())
            ->where('status', 'checked_in')
            ->count();
        $pendingAppointments = KioskAppointment::where('status', 'pending')->count();
        $recentAppointments = KioskAppointment::latest()->take(10)->get();

        // Get location and service maps
        $locationsMap = $this->getLocationsMap();
        $servicesMap = $this->getServicesMap();

        return view('kiosk.admin.dashboard', compact(
            'totalAppointments',
            'todayAppointments',
            'checkedInToday',
            'pendingAppointments',
            'recentAppointments',
            'locationsMap',
            'servicesMap'
        ));
    }

    /**
     * Appointments list
     */
    public function appointments(Request $request)
    {
        abort(403, 'You are not authorized to access this page. Contact other server.');

        $locations = $this->getLocationsMap();
        $servicesMap = $this->getServicesMap();

        return view('kiosk.admin.appointments', compact('servicesMap', 'locations'));
    }

    /**
     * Appointments list
     */
    public function appointmentsAjaxList(Request $request)
    { 
        $response = ApiService::get(
            'https://kiosk.sandbox-nybest.com/api/v1/appointments',
            [
                'search'      => $request->search,
                'status'      => $request->status,
                'date'        => isset($request->date) && !empty($request->date) ? date('Y-m-d', strtotime($request->date)) : "",
                'location_id' => $request->location,
                'page'        => $request->page,
                'per_page'    => 10,
            ]
        );
        $appointments = response()->json($response);
        if(isset($appointments->original['success'])){
            $appointments = $appointments->original['data'];
            return response()->json([
                'status' => true,
                'html' => view(
                    'kiosk.admin.appointment_ajax_list',
                    [
                        'appointments' => $appointments,
                        'locationsMap' => $this->getLocationsMap(),
                        'servicesMap'  => $this->getServicesMap(),
                    ]
                )->render(),
                'pagination' => $response['meta'] ?? [],
            ]);
        }
    }

    /**
     * Show single appointment
     */
    public function showAppointment($id)
    {
        $appointment = KioskAppointment::findOrFail($id);
        $appointmentDocuments = KioskAppointmentDocument::where('kiosk_appointment_id', $id)->get();
        // Get location, service, language and insurance maps
        $locationsMap = $this->getLocationsMap();
        $servicesMap = $this->getServicesMap();
        $languagesMap = $this->getLanguagesMap();
        $insurancesMap = $this->getInsurancesMap();
        return view('kiosk.admin.appointment-show', compact('appointment', 'appointmentDocuments', 'locationsMap', 'servicesMap', 'languagesMap', 'insurancesMap'));
    }
}