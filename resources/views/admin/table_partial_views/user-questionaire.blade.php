<div id="myModal_{{ $loop->index + 1 }}" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Evaluation</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                @php
                    $result = json_decode($user->questionnaireStatesSummary->current_summary, true);
                @endphp
                @isset($result)

                    @foreach ($result as $res)
                        @php

                            if (isset($res['id'])) {
                                $res = json_encode($res);
                            }
                            $res = json_decode($res, true);
                            if (isset($res['description'])) {
                                $description = strip_tags($res['description']);
                            } else {
                                break;
                            }
                        @endphp
                        @switch($res['category'])
                            @case('immigrationHistory')
                                @php
                                    array_push($immigrationHistory, $description);
                                @endphp
                            @break

                            @case('factorsOptions')
                                @php
                                    array_push($factorsOptions, $description);
                                @endphp
                            @break

                            @case('inadmissibility')
                                @php
                                    array_push($inadmissibility, $description);
                                @endphp
                            @break

                            {{-- @case(factorsOptions)
                            <span>`Password` input is empty!</span>
                        @break --}}
                        @endswitch
                    @endforeach
                    <div class="m-1">
                        <h4>Immigration History</h4>
                    </div>
                    @isset($immigrationHistory)
                        @foreach ($immigrationHistory as $immigration)
                            <div class="m-2">{!! html_entity_decode($immigration) !!}</div>
                        @endforeach
                    @endisset

                    <hr>
                    <div class="m-1">
                        <h4>Factors Relating to Your Options</h4>
                    </div>
                    @isset($factorsOptions)
                        @foreach ($factorsOptions as $factorsOption)
                            <div class="m-2">{!! html_entity_decode($factorsOption) !!}</div>
                        @endforeach
                    @endisset

                    <hr>
                    <div class="m-1">
                        <h4 class="m-2">Grounds of Inadmissibility</h4>
                    </div>
                    @isset($inadmissibility)
                        @foreach ($inadmissibility ?? '' as $inadmissibility)
                            <div class="m-2">{!! html_entity_decode($inadmissibility) !!}</div>
                        @endforeach
                    @endisset

                @endisset


            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>

    </div>
</div>
