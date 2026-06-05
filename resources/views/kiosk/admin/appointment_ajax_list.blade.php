
 <!-- Table -->
    <div class="overflow-x-auto">
        <table class="table table-bordered table-width1">
            <thead>
                <tr>
                    <th>Token No</th>
                    <th>Patient Name</th>
                    <th>Mobile</th>
                    <th>Location</th>
                    <th>Services</th>
                    <th>Docs</th>
                    <th>Status</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($appointments as $appointment)
                <tr>
                    <td class="font-semibold text-blue-600">{{ $appointment['token_no'] ?? 'N/A' }}</td>

                    <td>
                        <div>
                            <p class="font-medium">
                                {{ $appointment['first_name'] }} {{ $appointment['middle_name'] }} {{ $appointment['last_name'] }}
                            </p>
                            @if($appointment['email'])
                                <p class="text-xs text-gray-500">{{ $appointment['email'] }}</p>
                            @endif
                        </div>
                    </td>

                    <td>{{ $appointment['mobile'] }}</td>

                    <td>{{ $locationsMap[$appointment['location_id']] ?? 'N/A' }}</td>

                    <td>
                        @if(is_array($appointment['service_names']) && count($appointment['service_names']))
                            @foreach($appointment['service_names'] as $serviceId)
                                <span class="inline-block px-2 py-0.5 bg-blue-100 text-blue-700 text-xs rounded mb-1">
                                    {{ $serviceId ?? '' }}
                                </span>
                            @endforeach
                        @else
                            N/A
                        @endif
                    </td>

                    <td>
                        @if($appointment['documents_count'] > 0)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-green-100 text-green-700 text-xs rounded">
                                {{ $appointment['documents_count'] }}
                            </span>
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>

                    <td>
                        <span class="status-badge status-{{ $appointment['status'] }}">
                            {{ $appointment['status'] === 'checked_in' ? 'Check-in' : ucfirst(str_replace('_',' ',$appointment['status'])) }}
                        </span>
                    </td>

                    <td>
                        {{ !empty($appointment['created_at'])
                            ? \Carbon\Carbon::parse($appointment['created_at'])->format('m/d/Y h:i A')
                            : 'N/A'
                        }}
                    </td>

                     <td>
                        {{--<a href="{{ route('admin.appointments.show', $appointment['id']) }}"
                        class="text-blue-600 hover:text-blue-800" target="_blank">
                            <i class="fa fa-eye"></i>
                        </a> --}}
                    </td> 
                </tr>
                @empty
                <tr>
                    <td colspan="10" style="text-align:center">No record available</td>
                </tr>
                @endforelse

            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if(!empty($pagination))
    <div class="p-6 border-t border-gray-100">
        <ul class="pagination flex gap-2">
            @if($pagination['current_page'] > 1)
            <li>
                <a href="javascript:void(0)" data-page="{{ $pagination['current_page'] - 1 }}" class="page-link">Prev</a>
            </li>
            @endif

            @for($i = 1; $i <= $pagination['last_page']; $i++)
            <li>
                <a href="javascript:void(0)" data-page="{{ $i }}" class="page-link {{ $i == $pagination['current_page'] ? 'font-bold' : '' }}">{{ $i }}</a>
            </li>
            @endfor

            @if($pagination['current_page'] < $pagination['last_page'])
            <li>
                <a href="javascript:void(0)" data-page="{{ $pagination['current_page'] + 1 }}" class="page-link">Next</a>
            </li>
            @endif
        </ul>
    </div>
    @endif