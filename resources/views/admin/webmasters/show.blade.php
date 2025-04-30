@extends('admin.layouts.app')

@section('content')
    <h3 class="page-title">Вебмастер #{{ $webmaster->api_id }}</h3>

    <p>
        <a href="{{ route('admin.actions.index', ['webmaster_id' => $webmaster->id,'date_from' => request('date_from'),'date_to' => request('date_to')]) }}"
           class="btn btn-success">Клики</a>

        <a href="{{ route('admin.users.index', ['webmaster_id' => $webmaster->id,'date_from' => request('date_from'),'date_to' => request('date_to')]) }}"
           class="btn btn-success">Пользователи</a>

        <a href="{{ route('admin.conversions.index', ['webmaster_id' => $webmaster->id,'date_from' => request('date_from'),'date_to' => request('date_to')]) }}"
           class="btn btn-success">Конверсии</a>
    </p>

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('quickadmin.qa_view')
        </div>

        <div class="panel-body table-responsive">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th>Партнерская программа</th>
                            <td field-key='source'>{{ $webmaster->source->name }}</td>
                        </tr>
                        <tr>
                            <th>Идентификатор</th>
                            <td field-key='api_id'>{{ $webmaster->api_id }}</td>
                        </tr>
                        <tr>
                            <th>Комментарий</th>
                            <td field-key='comment'>{{ $webmaster->comment }}</td>
                        </tr>
                        <tr>
                            <th>Ссылка</th>
                            <td>{{ $link }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <p>
                <a href="{{ route('admin.users.index') }}" class="btn btn-default">@lang('quickadmin.qa_back_to_list')</a>
            </p>
        </div>
    </div>
@stop