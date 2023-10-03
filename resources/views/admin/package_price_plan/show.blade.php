<div id="flat-fee-detail-{{ $package->id }}" class="modal fade" role="dialog">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Flat Fee Details</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">

                <div style="display:flex;">
                    <div class="m-3"><button class="btn btn-primary button-english"  id="eng-language">English</button></div>
                    <div class="m-3"><button class="btn btn-primary button-spn" id="btn-spn"
                            >Spanish</button></div>
                </div>

                <div class="tab-content">
                    <div id="english" class="english-tab">
                        @include('admin.table_partial_views.flat-fee-data-english')
                    </div>
                    <div id="spanish" style="display: none" class="spanish-tab">
                        @include('admin.table_partial_views.flat-fee-data-spanish')
                    </div>
                </div>
                {{-- tabend --}}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>

    </div>
</div>

    <script>

        $(".button-spn").click(function() {
                $(".spanish-tab").show();
                $(".english-tab").hide();
            });
            $(".button-english").click(function() {
                $(".english-tab").show();
                $(".spanish-tab").hide();
            });
    </script>
