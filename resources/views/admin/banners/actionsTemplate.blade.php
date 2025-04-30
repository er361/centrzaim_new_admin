<form method="post" action="{{ route($routeKey.'.up', $row->id) }}" style="display: inline">
    {{ csrf_field() }}
    <button type="submit" class="btn btn-primary btn-xs">
        <i class="fa fa-arrow-up" aria-hidden="true"></i>
    </button>
</form>
<form method="post" action="{{ route($routeKey.'.down', $row->id) }}" style="display: inline">
    {{ csrf_field() }}
    <button type="submit" class="btn btn-primary btn-xs">
        <i class="fa fa-arrow-down" aria-hidden="true"></i>
    </button>
</form>

@can($gateKey.'view')
    <a href="{{ route($routeKey.'.show', $row->id) }}"
       class="btn btn-xs btn-primary">@lang('quickadmin.qa_view')</a>
@endcan

@can($gateKey.'edit')
    <a href="{{ route($routeKey.'.edit', $row->id) }}"
       class="btn btn-xs btn-default">@lang('quickadmin.qa_edit')</a>
@endcan

@can($gateKey.'delete')
    {!! Form::open(array(
        'style' => 'display: inline-block;',
        'method' => 'DELETE',
        'onsubmit' => "return confirm('".trans("quickadmin.qa_are_you_sure")."');",
        'route' => [$routeKey.'.destroy', $row->id])) !!}
    {!! Form::submit(trans('quickadmin.qa_delete'), array('class' => 'btn btn-xs btn-danger')) !!}
    {!! Form::close() !!}
@endcan