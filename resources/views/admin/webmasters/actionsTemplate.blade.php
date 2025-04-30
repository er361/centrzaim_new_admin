<p>
    <a href="{{ route('admin.webmasters.show', ['webmaster' => $row, 'date_from' => request('date_from'),'date_to' => request('date_to')]) }}"
       class="btn btn-xs btn-primary">Просмотр</a>

    <a href="{{ route('admin.webmasters.edit', ['webmaster' => $row]) }}"
       class="btn btn-xs btn-primary">Редактировать</a>
</p>

<p>
    <a href="{{ route('admin.actions.index', ['webmaster_id' => $row->id,'date_from' => request('date_from'),'date_to' => request('date_to')]) }}"
       class="btn btn-xs btn-success">Клики</a>

    <a href="{{ route('admin.users.index', ['webmaster_id' => $row->id,'date_from' => request('date_from'),'date_to' => request('date_to')]) }}"
       class="btn btn-xs btn-success">Пользователи</a>
</p>