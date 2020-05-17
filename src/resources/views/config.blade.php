@extends('web::layouts.grids.4-4-4')

@section('title', trans('web::seat.configuration'))
@section('page_header', trans('contractstock::seat.name'))
@section('page_description',trans('web::seat.configuration'))

@section('left')
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">{{trans('contractstock::seat.name')}}</h3>
        </div>
        <div class="panel-body">
            <!--add post route-->
            <form role="form" action="{{route('contractstock.config.post')}}" method="post" class="form-horizontal">
                {{ csrf_field() }}

                <div class="box-body">

                    <legend>{{trans('web::seat.configuration')}}</legend>

					<div class="form-group">
                        <label for="contractstock-configuration-corptomem" class="col-md-4">Corp to member contracts</label>
                        <div class="col-md-7">
                            <div class="input-group input-group-sm">
                                <select class="form-control " id="contractcorp-corp-corptomem" name="contractcorp-corp-corptomem"
                                        required>
                                    @foreach($corps as $corp)
                                        @if(setting('contractcorp.corp.corptomem', true) == $corp['id'])
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
                        <label for="contractstock-configuration-memtocorp" class="col-md-4">Member to corp contracts</label>
                        <div class="col-md-7">
                            <div class="input-group input-group-sm">
                                <select class="form-control " id="contractcorp-corp-memtocorp" name="contractcorp-corp-memtocorp"
                                        required>
                                    @foreach($corps as $corp)
                                        @if(setting('contractcorp.corp.memtocorp', true) == $corp['id'])
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
                    Installed version: <b>{{ config('contractstock.config.version') }}</b>
                </div>
                <div class="col-md-6">
                    Latest version:
                    <a href="https://packagist.org/packages/veteranmina/contractstock">
                        <img src="https://poser.pugx.org/veteranmina/contractstock/v/stable"
                             alt="Discord Connector Version"/>

                    </a>
                </div>
            </div>
        </div>
    </div>
@stop

@push('javascript')
    <script type="application/javascript">
        $('#contractstock.corp.corptomem').val({{setting('contractstock.corp.corptomem',true)}}).trigger('change');
		$('#contractstock.corp.memtocorp').val({{setting('contractstock.corp.memtocorp',true)}}).trigger('change');
    </script>
@endpush