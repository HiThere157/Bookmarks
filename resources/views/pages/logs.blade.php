@extends('layouts.default')

@section('content')
    <div class="d-flex justify-content-between align-items-center mx-5">
        <h1 class="mt-3">Logs</h1>
    </div>

    <form action="{{ route('logs') }}">
        <div class="d-flex flex-wrap mb-3 justify-content-center" style="column-gap: 1rem;">
            <div class="flex-grow-1">

                <div class="input-group mb-3">
                    <input type="text" class="form-control" name="q" placeholder="Search Logs..." value="{{ $query }}">
                    <button id="typeBtn" class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false"></button>
                    <input id="type" type="text" name="type" hidden>
                    <ul class="dropdown-menu dropdown-menu-end" data-target-input="#type" data-target-btn="#typeBtn">
                        <li class="dropdown-item text-secondary">debug</li>
                        <li class="dropdown-item text-info">info</li>
                        <li class="dropdown-item text-success">notice</li>
                        <li class="dropdown-item text-warning">warning</li>
                        <li class="dropdown-item text-danger">error</li>
                        <li class="dropdown-item text-danger">critical</li>
                        <li class="dropdown-item text-danger">alert</li>
                        <li class="dropdown-item text-danger">emergency</li>
                        <li><hr class="dropdown-divider"></li>
                        <li class="dropdown-item">None Specified</li>
                    </ul>
                </div>

            </div>
            <div>
                <div class="d-flex">
                    <label class="col-form-label text-nowrap">Results:</label>
                    <input id="n" type="text" name="n" hidden>
                    <div class="ms-2 dropdown">
                        <button id="nBtn" class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false"></button>
                        <ul class="dropdown-menu" data-target-input="#n" data-target-btn="#nBtn">
                            <li class="dropdown-item">10</li>
                            <li class="dropdown-item">20</li>
                            <li class="dropdown-item">50</li>
                            <li class="dropdown-item">100</li>
                            <li class="dropdown-item">500</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="d-flex align-items-center" style="height: fit-content; column-gap: 1rem;">
                <div class="d-flex">
                    <label for="search_start" class="col-form-label text-nowrap">Search Start:</label>
                    <input type="date" class="ms-2 form-control" id="search_start" name="start_time" />
                </div>
                <div class="d-flex">
                    <label for="search_end" class="col-form-label text-nowrap">Search End:</label>
                    <input type="date" class="ms-2 form-control" id="search_end" name="end_time" />
                </div>
                <div>
                    <button id="resetBtn" type="button" style="width: 10rem;" class="btn btn-secondary">Reset</button>
                    <button id="submitBtn" type="submit" style="width: 10rem;" class="btn btn-primary">Search</button>
                </div>
            </div>
        </div>
    </form>

    {{ $logs->links() }}
    <table class="table table-striped table-hover tablesorter">
        <thead>
            <tr>
                <th>Time</th>
                <th>Type</th>
                <th>Controller</th>
                <th>Description</th>
            </tr>
        </thead>
        <tbody>
            @foreach($logs as $log)
                <tr @class([
                        'table-danger' => $log['type'] == 'EMERGENCY' || $log['type'] == 'ALERT' || $log['type'] == 'CRITICAL'
                    ])>
                    <td class="text-nowrap" style="width: 0;">{{ $log['time'] }}</td>
                    
                    <td @class([
                        'text-nowrap',
                        'text-danger' => $log['type'] == 'EMERGENCY' || $log['type'] == 'ALERT' || $log['type'] == 'CRITICAL' || $log['type'] == 'ERROR',
                        'text-warning' => $log['type'] == 'WARNING',
                        'text-success' => $log['type'] == 'NOTICE',
                        'text-info' => $log['type'] == 'INFO',
                        'text-secondary' => $log['type'] == 'DEBUG'
                        ]) style="width: 0;"> {{ $log['type'] }}
                    </td>
                    
                    <td class="text-nowrap text-end" style="width: 0;">{{ $log['controller'] }}</td>
                    <td>{{ $log['description'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    {{ $logs->links() }}


    <script>
        function setDropdownBySender(sender) {
            var parent = $(sender).parent();
            setDropdown(parent.attr('data-target-input'), parent.attr('data-target-btn'), $(sender).text())
        }

        function setDropdown(inputElement, btnElement, value) {
            if(value == "") {
                value = "None Specified";
            }

            $(btnElement).text(value);

            if(value == "None Specified") {
                value = "";
            }
            
            $(inputElement).val(value);
        }

        $(document).ready(function() {
            //apply table sorter
            $(".table").tablesorter();

            //set previous dropdown value and start and end time
            setDropdown("#n", "#nBtn", "{{ $n }}");
            setDropdown("#type", "#typeBtn", "{{ $type }}");
            $("#search_start").val("{{ $start_time }}");
            $("#search_end").val("{{ $end_time }}");

            //apply dropdown onclick
            $(".dropdown-item").click(function() {
                setDropdownBySender(this);
            }).css("cursor", "pointer");

            //reset button onclick
            $("#resetBtn").click(function() {
                $("input[name='q']").val("");
                $("#search_start").val("");
                $("#search_end").val("");
                setDropdown("#n", "#nBtn", "20");
                setDropdown("#type", "#typeBtn", "");
            });
        });
    </script>
@stop
