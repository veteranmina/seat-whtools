@extends('web::layouts.grids.12')
@section('title', trans('whtools::seat.bluetax'))
@section('page_header', trans('whtools::seat.name'))
@section('page_description', trans('whtools::seat.bluetax'))

@section('content')

    <select name="year" id="year">
        <option value="">Select Year</option>
    </select>
    <select name="month" id="month">
        <option value="">Select Month</option>
    </select>
    <p id='test'>{{$daterange['start']}} to {{$daterange['end']}} </p>

    <div class="row">
        <div class="col-md-12">

            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="active" id='sales'><a
                                href="{{route('whtools.bluesalesbydate',['start'=>$daterange['start'],'end'=>$daterange['end']])}}"
                                id='sales'>{{trans('whtools::whtools.bluesalesbydate')}} </a></li>
                    <li class="" id='salestotals'><a
                                href="{{route('whtools.bluetotals',['start'=>$daterange['start'],'end'=>$daterange['end']])}}"
                                id='salestotals'>{{trans('whtools::whtools.bluetotals')}} </a></li>
                    <li class="" id='payments'><a
                                href="{{route('whtools.bluetaxpayments.bydate',['start'=>$daterange['start'],'end'=>$daterange['end']])}}"
                                id='payments'>{{trans('whtools::whtools.bluetaxpayments')}} </a></li>
                    <li class="" id='paymentstotals'><a
                                href="{{route('whtools.bluetaxpayment.totals.bydate',['start'=>$daterange['start'],'end'=>$daterange['end']])}}"
                                id='paymentstotals'>{{trans('whtools::whtools.bluetaxpaymentstotals')}} </a></li>


                </ul>
                <div class="tab-content">

                    <table class="table compact table-condensed table-hover table-responsive"
                           id="character-transactions" data-page-length='100'>
                        <thead>
                        <tr>
                            <th>{{ trans('web::seat.date') }}</th>
                            <th>{{ trans('whtools::whtools.seller') }}</th>
                            <th></th>
                            <th>{{ trans_choice('web::seat.type', 1) }}</th>
                            <th>{{ trans('web::seat.qty') }}</th>
                            <th>{{ trans('web::seat.price') }}</th>
                            <th>{{ trans('web::seat.total') }}</th>
                            <th>{{ trans('web::seat.client') }}</th>
                        </tr>
                        </thead>
                    </table>

                </div>
            </div>

        </div>
    </div>

@stop

@push('javascript')

    <script type="text/javascript">


        var character_transactions = $('table#character-transactions').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route('whtools.bluesales.databydate', [$daterange['start'],$daterange['end']]) }}'

            ,
            columns: [
                {data: 'date', name: 'date'},
                {data: 'seller_view', name: 'users.name'},
                {data: 'is_buy', searchable: false},
                {data: 'item_view', name: 'type.typeName'},
                {data: 'quantity', name: 'quantity'},
                {data: 'unit_price', name: 'unit_price', searchable: 'false'},
                {data: 'sum', searchable: false},
                {data: 'client_view', name: 'client.name'}
            ],
            "order": [[6, "desc"]],
            drawCallback: function () {
                $('img').unveil(100);
                ids_to_names();
            }
        });

        var startdate = new Date('{{$daterange['start']}}');

        for (y = 2018; y <= 2025; y++) {
            var optn = document.createElement("OPTION");
            optn.text = y;
            optn.value = y;

            // if year is 2015 selected
            if (y == startdate.getFullYear()) {
                optn.selected = true;
            }

            document.getElementById('year').options.add(optn);
        }
        var d = new Date();
        var monthArray = new Array();
        monthArray[0] = "January";
        monthArray[1] = "February";
        monthArray[2] = "March";
        monthArray[3] = "April";
        monthArray[4] = "May";
        monthArray[5] = "June";
        monthArray[6] = "July";
        monthArray[7] = "August";
        monthArray[8] = "September";
        monthArray[9] = "October";
        monthArray[10] = "November";
        monthArray[11] = "December";
        for (m = 0; m <= 11; m++) {
            var optn = document.createElement("OPTION");
            optn.text = monthArray[m];
            optn.value = (m);

            // if june selected
            if (m == startdate.getMonth()) {
                optn.selected = true;
            }
            document.getElementById('month').options.add(optn);
        }
        document.getElementById('month').onchange = function () {
            startdate = (new Date($('#year').val(), $('#month').val(), 1)).toISOString();
            enddate = (new Date($('#year').val(), parseInt($('#month').val()) + 1, 0)).toISOString();
            url = "{{ route('whtools.bluesalesbydate', ['start'=>'start','end'=>'end']) }}";
            url = url.replace('start', startdate);
            url = url.replace('end', enddate);
            document.getElementById('test').innerHTML = url;
            window.location = url;
        }

    </script>

    @include('web::includes.javascript.id-to-name')

@endpush