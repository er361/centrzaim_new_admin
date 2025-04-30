@inject('request', 'Illuminate\Http\Request')
@extends('admin.layouts.app')

@section('content')
    <h3 class="page-title">Партнерские программы</h3>

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('quickadmin.qa_list')
        </div>

        <div class="panel-body table-responsive">
            <table class="table table-bordered table-striped {{ count($sources) > 0 ? 'datatable' : '' }}">
                <thead>
                    <tr>
                        <th>Название</th>
                        <th>Стоимость конверсии</th>
                                                <th>&nbsp;</th>

                    </tr>
                </thead>
                
                <tbody>
                    @if (count($sources) > 0)
                        @foreach ($sources as $source)
                            <tr data-entry-id="{{ $source->id }}">

                                <td>{{ $source->name }}</td>
                                <td>{{ $source->postback_cost }}</td>

                                <td>

                                    <a href="{{ route('admin.sources.edit', $source) }}" class="btn btn-xs btn-info">
                                        @lang('quickadmin.qa_edit')
                                    </a>
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