<div class="tab-pane" id="notes-section">
    @php
        $notesFlag = 0;
    @endphp
    @if ($record->type != '')
        @if ($record->link_hha_patient != '')
            @php
                $notesFlag = 1;
            @endphp
        @elseif($record->link_hha_caregiver != '')
            @php
                $notesFlag = 1;
            @endphp
        @else
            @if ($record->hha_id != '')
                @php
                    $notesFlag = 1;
                @endphp
            @endif
        @endif
    @endif
    @include('patient._partial.notes_section')
</div>
