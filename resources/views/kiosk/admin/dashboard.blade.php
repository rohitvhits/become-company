@include('include/header')
@include('include/sidebar')

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Total Appointments -->
    <div class="admin-card p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 mb-1">Total Appointments</p>
                <p class="text-3xl font-bold text-gray-800">{{ $totalAppointments }}</p>
            </div>
            <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
        </div>
        <p class="text-xs text-gray-400 mt-2">All time</p>
    </div>

    <!-- Today's Appointments -->
    <div class="admin-card p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 mb-1">Today's Appointments</p>
                <p class="text-3xl font-bold text-gray-800">{{ $todayAppointments }}</p>
            </div>
            <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>
        <p class="text-xs text-gray-400 mt-2">{{ now()->format('F j, Y') }}</p>
    </div>

    <!-- Checked In -->
    <div class="admin-card p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 mb-1">Check-in Today</p>
                <p class="text-3xl font-bold text-gray-800">{{ $checkedInToday }}</p>
            </div>
            <div class="w-12 h-12 rounded-full bg-purple-100 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-600" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>
        </div>
        <p class="text-xs text-gray-400 mt-2">Status: Check-in</p>
    </div>

    <!-- Pending -->
    <div class="admin-card p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 mb-1">Pending</p>
                <p class="text-3xl font-bold text-gray-800">{{ $pendingAppointments }}</p>
            </div>
            <div class="w-12 h-12 rounded-full bg-yellow-100 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-600" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>
        <p class="text-xs text-gray-400 mt-2">Awaiting check-in</p>
    </div>
</div>

<!-- Recent Appointments -->
<div class="admin-card">
    <div class="p-6 border-b border-gray-100">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-bold text-gray-800">Recent Appointments</h3>
            <a href="{{ route('admin.appointments') }}" class="text-sm text-blue-600 hover:text-blue-800">View All</a>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Token No</th>
                    <th>Patient Name</th>
                    <th>Mobile</th>
                    <th>Location</th>
                    <th>Status</th>
                    <th>Created At</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentAppointments as $appointment)
                <tr>
                    <td class="font-semibold text-blue-600">{{ $appointment->token_no ?? 'N/A' }}</td>
                    <td>{{ $appointment->first_name }} {{ $appointment->last_name }}</td>
                    <td>{{ $appointment->mobile }}</td>
                    <td>{{ $locationsMap[$appointment->location_id] ?? 'N/A' }}</td>
                    <td>
                        <span class="status-badge status-{{ $appointment->status }}">
                            @if($appointment->status == 'checked_in')
                            Check-in
                            @else
                            {{ ucfirst(str_replace('_', ' ', $appointment->status)) }}
                            @endif
                        </span>
                    </td>
                    <td>{{ $appointment->created_at->format('M j, Y g:i A') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-gray-500 py-8">No appointments found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@include('include/footer')