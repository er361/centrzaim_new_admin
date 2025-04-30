@inject('request', 'Illuminate\Http\Request')
@extends('admin.layouts.app')

@section('content')
    <h3 class="page-title">Аккаунты SMS</h3>
    <p>
        <a href="{{ route('admin.sms-providers.create') }}" class="btn btn-success">@lang('quickadmin.qa_add_new')</a>
    </p>



    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('quickadmin.qa_list')
        </div>

        <div class="panel-body table-responsive">
            <table class="table table-bordered table-striped {{ count($model) > 0 ? 'datatable' : '' }}">
                <thead>
                <tr>
                    <th>Название</th>
                    <th>Сервис</th>
                    <th>&nbsp;</th>

                </tr>
                </thead>

                <tbody>
                @if (count($model) > 0)
                    @foreach ($model as $item)
                        <tr data-entry-id="{{ $item->id }}">
                            <td field-key='name'>{{ $item->name }}</td>
                            <td field-key='service'>{{ $services[$item->service_id] }}</td>
                            <td>

                                <a href="{{ route('admin.sms-providers.edit',[$item->id]) }}"
                                   class="btn btn-xs btn-info">@lang('quickadmin.qa_edit')</a>

                                    {!! Form::open(array(
                                                                            'style' => 'display: inline-block;',
                                                                            'method' => 'DELETE',
                                                                            'onsubmit' => "return confirm('".trans("quickadmin.qa_are_you_sure")."');",
                                                                            'route' => ['admin.sms-providers.destroy', $item->id])) !!}
                                    {!! Form::submit(trans('quickadmin.qa_delete'), array('class' => 'btn btn-xs btn-danger')) !!}
                                    {!! Form::close() !!}
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
