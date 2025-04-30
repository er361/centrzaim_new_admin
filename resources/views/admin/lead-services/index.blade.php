@inject('request', 'Illuminate\Http\Request')
@extends('admin.layouts.app')

@section('content')
    <h3 class="page-title">Отправка анкет</h3>

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('quickadmin.qa_list')
        </div>

        <div class="panel-body table-responsive">
            <table class="table table-bordered table-striped {{ count($model) > 0 ? 'datatable' : '' }}">
                <thead>
                <tr>
                    <th>Название</th>
                    <th>Зарегистрированным после</th>
                    <th>Задержка в минутах</th>
                    <th>&nbsp;</th>

                </tr>
                </thead>

                <tbody>
                @if (count($model) > 0)
                    @foreach ($model as $item)
                        <tr data-entry-id="{{ $item->id }}">
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->registered_after?->format('d.m.Y H:i:s') ?? 'Выключен' }}</td>
                            <td>{{ $item->delay_minutes }}</td>
                            <td>
                                <a href="{{ route('admin.lead-services.edit',[$item->id]) }}"
                                   class="btn btn-xs btn-info">@lang('quickadmin.qa_edit')</a>
                            </td>

                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="8">@lang('quickadmin.qa_no_entries_in_table')</td>
                    </tr>
                @endif
                </tbody>
            </table>
        </div>
    </div>
@stop
