<table id="users_list" class="table table-bordered table-hover">
    <thead>
        <tr>
            <th>
                Sr No.
            </th>
            <th>
                Customer Name
            </th>
            <th>
                Customer Email
            </th>
            <th>
                Attorney
            </th>
            <th>
                Date
            </th>
            <th>
                Time
            </th>

            <th>
                Evaluation
            </th>

            <th>
                Consultation Type
            </th>

        </tr>
    </thead>
    <tbody>
        @php
            $immigrationHistory = [];
            $factorsOptions = [];
            $inadmissibility = [];
            // $immigrationHistory = [];
        @endphp

        @foreach ($consultations as $consultation)
            <tr>
                <th>
                    {{ $loop->index + 1 }}
                </th>
                <td>
                    <a data-target="#user-detail-{{$consultation->id}}" data-toggle="modal" href="#user-detail" > {{ $consultation->user->first_name ?? '' }} {{ $consultation->user->last_name ?? '' }}</a>
                    @include('admin.table_partial_views.user-detail')
                </td>
                <td>
                    {{ $consultation->user->email ?? '' }}
                </td>
                <td>
                    {{ $consultation->consultation_with ?? '' }}
                </td>
                <td>
                    {{ $consultation->date ?? '' }}
                </td>
                <td>
                    {{ $consultation->consultation_time ?? '' }}
                </td>
                <td>
                    @if (isset($consultation->questionnaire_summery) && $consultation->questionnaire_summery != null)
                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal"
                            data-target="#myModal_{{ $loop->index + 1 }}">Evaluation</button>
                        @include('admin.table_partial_views.questionnaire')
                    @else
                        <h6>Null</h6>
                    @endif
                </td>
                <td>
                {{ $consultation->appointment_type ?? '' }}
                </td>
        @endforeach
    </tbody>
</table>
