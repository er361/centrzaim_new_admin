@can('user_view')
    <a href="{{ route('admin.users.show', $row->id) }}"
       class="btn btn-xs btn-primary">@lang('quickadmin.qa_view')</a>
@endcan

@can('user_edit')
    <a href="{{ route('admin.users.edit', $row->id) }}"
       class="btn btn-xs btn-default">@lang('quickadmin.qa_edit')</a>
@endcan

@if($row->role_id === \App\Models\Role::ID_TRAFFIC_SOURCE)
    @can('revenue_report_access')
        <a href="{{ route('admin.report.revenue', ['webmaster_id' => $row->accessibleWebmasters->pluck('id')->toArray()]) }}"
           class="btn btn-xs btn-success">Статистика</a>
    @endcan
@endif