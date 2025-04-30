@inject('request', 'Illuminate\Http\Request')
@extends('admin.layouts.app')

@section('content')
    <h3 class="page-title">SMS</h3>
    @can('sms_create')
        <p>
            <a href="{{ route('admin.sms.create') }}" class="btn btn-success">@lang('quickadmin.qa_add_new')</a>

        </p>
    @endcan



    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('quickadmin.qa_list')
        </div>

        <div class="panel-body table-responsive">
            <table class="table table-bordered table-striped {{ count($model) > 0 ? 'datatable' : '' }} @can('sms_delete') dt-select @endcan">
                <thead>
                <tr>
                    @can('sms_delete')
                        <th style="text-align:center;"><input type="checkbox" id="select-all"/></th>
                    @endcan

                    <th>Название</th>
                    <th>Провайдер</th>
                    <th>Для пользователей</th>
                    <th>Задержка перед отправкой (в минутах)</th>
                    <th>Включена?</th>
                    <th>&nbsp;</th>

                </tr>
                </thead>

                <tbody>
                @if (count($model) > 0)
                    @foreach ($model as $item)
                        <tr data-entry-id="{{ $item->id }}">
                            @can('sms_delete')
                                <td></td>
                            @endcan

                            <td field-key='name'>{{ $item->name }}</td>
                            <td field-key='provider'>{{ $item->smsProvider->name ?? '-' }}</td>
                            <td field-key='source_name'>{{ $item->source?->name ?? '-' }}</td>
                            <td field-key='delay'>{{ $item->delay }}</td>
                            <td field-key='is_enabled'>{{ $item->is_enabled ? 'Да' : 'Нет' }}</td>
                            <td>

                                @can('sms_access')
                                    <a href="{{ route('admin.sms.show', [$item->id]) }}"
                                       class="btn btn-xs btn-info">@lang('quickadmin.qa_show')</a>
                                @endcan
                                @can('sms_edit')
                                    <a href="{{ route('admin.sms.edit',[$item->id]) }}"
                                       class="btn btn-xs btn-info">@lang('quickadmin.qa_edit')</a>
                                @endcan
                                @can('sms_delete')
                                    {!! Form::open(array(
                                                                            'style' => 'display: inline-block;',
                                                                            'method' => 'DELETE',
                                                                            'onsubmit' => "return confirm('".trans("quickadmin.qa_are_you_sure")."');",
                                                                            'route' => ['admin.sms.destroy', $item->id])) !!}
                                    {!! Form::submit(trans('quickadmin.qa_delete'), array('class' => 'btn btn-xs btn-danger')) !!}
                                    {!! Form::close() !!}
                                @endcan
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

@section('javascript')
    <script>
        @can('sms_delete')
            window.route_mass_crud_entries_destroy = '{{ route('admin.sms.mass_destroy') }}';
        @endcan

    </script>
@endsection