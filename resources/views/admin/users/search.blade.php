@inject('request', 'Illuminate\Http\Request')
@extends('admin.layouts.app')

@section('content')
    <h3 class="page-title">@lang('quickadmin.users.title')</h3>

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('quickadmin.qa_list')
        </div>

        <div class="panel-body">
            <form>
                <div class="row">
                    <div class="col-md-4 form-group">
                        <label for="email">Email</label>
                        <input type="text" name="email" id="email"
                               value="{{ old('email') }}" class="form-control"/>
                    </div>
                    <div class="col-md-4 form-group">
                        <label for="mphone">Телефон (в формате 79999999999)</label>
                        <input type="text" name="mphone" id="mphone"
                               value="{{ old('mphone') }}" class="form-control"/>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 form-group">
                        <input type="submit" value="Отфильтровать" class="btn btn-primary"/>
                    </div>
                </div>
            </form>

        </div>
    </div>
@stop