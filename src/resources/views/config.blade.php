@extends('web::layouts.grids.4-4-4')

@section('title', trans('web::seat.configuration'))
@section('page_header', trans('whtools::seat.name'))
@section('page_description',trans('web::seat.configuration'))

@section('left')
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">{{trans('whtools::seat.name')}}</h3>
        </div>
        <div class="panel-body">
            <!--add post route-->
            <form role="form" action="{{route('whtools.config.post')}}" method="post" class="form-horizontal">
                {{ csrf_field() }}

                <div class="box-body">

                    <legend>{{trans('web::seat.configuration')}}</legend>

                    <div class="form-group">
                        <label for="whtools-tax-percentage" class="col-md-4">Blue Loot Tax %</label>
                        <div class="col-md-7">
                            <div class="input-group input-group-sm">

                                <input type="number" min='1' max='100' class="form-control " id="whtools-tax-percentage"
                                       name="whtools-tax-percentage"
                                       value="{{ setting('whtools.bluetax.percentage', true) }}"/>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="whtools-configuration-bluetax-tax-collector" class="col-md-4">Collecting
                            Corporation</label>
                        <div class="col-md-7">
                            <div class="input-group input-group-sm">
                                <select class="form-control " id="whtools-tax-collector" name="whtools-tax-collector"
                                        required>
                                    @foreach($corps as $corp)
                                        @if(setting('whtools.bluetax.collector', true) == $corp['id'])
                                            <option id="{{$corp['id']}}" value="{{$corp['id']}}"
                                                    selected='true'>{{$corp['name']}}</option>
                                        @else
                                            <option id="{{$corp['id']}}"
                                                    value="{{$corp['id']}}">{{$corp['name']}}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <legend>{{'Stock Corp'}}</legend>

                    <div class="form-group">
                        <label for="whtools-configuration-contract-corp" class="col-md-4">Contract
                            Corporation</label>
                        <div class="col-md-7">
                            <div class="input-group input-group-sm">
                                <select class="form-control " id="whtools-contract-corp" name="whtools-contract-corp"
                                        required>
                                    @foreach($corps as $corp)
                                        @if(setting('whtools.contract.corp', true) == $corp['id'])
                                            <option id="{{$corp['id']}}" value="{{$corp['id']}}"
                                                    selected='true'>{{$corp['name']}}</option>
                                        @else
                                            <option id="{{$corp['id']}}"
                                                    value="{{$corp['id']}}">{{$corp['name']}}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="whtools-configuration-main-corp" class="col-md-4">Main
                            Corporation</label>
                        <div class="col-md-7">
                            <div class="input-group input-group-sm">
                                <select class="form-control " id="whtools-main-corp" name="whtools-main-corp"
                                        required>
                                    @foreach($corps as $corp)
                                        @if(setting('whtools.main.corp', true) == $corp['id'])
                                            <option id="{{$corp['id']}}" value="{{$corp['id']}}"
                                                    selected='true'>{{$corp['name']}}</option>
                                        @else
                                            <option id="{{$corp['id']}}"
                                                    value="{{$corp['id']}}">{{$corp['name']}}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="box-footer">
                    <button type="submit" class="btn btn-primary pull-right">{{trans('web::seat.update')}}</button>
                </div>

            </form>
        </div>
    </div>
@stop


@section('right')
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><i class="fa fa-rss"></i> Update feed</h3>
        </div>
        <div class="panel-body" style="height: 500px; overflow-y: scroll">
            {!! $changelog !!}
        </div>
        <div class="panel-footer">
            <div class="row">
                <div class="col-md-6">
                    Installed version: <b>{{ config('whtools.config.version') }}</b>
                </div>
                <div class="col-md-6">
                    Latest version:
                    <a href="https://packagist.org/packages/flyingferret/seat-whtools">
                        <img src="https://poser.pugx.org/flyingferret/seat-whtools/v/stable"
                             alt="Discord Connector Version"/>

                    </a>
                </div>
            </div>
        </div>
    </div>
@stop

@push('javascript')
    <script type="application/javascript">
        $('#whtools-tax-collector').val({{setting('whtools.bluetax.collector',true)}}).trigger('change');
    </script>
@endpush