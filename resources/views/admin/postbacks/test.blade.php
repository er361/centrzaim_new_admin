@inject('request', 'Illuminate\Http\Request')
@extends('admin.layouts.app')

@section('content')
    <h3 class="page-title">Тестирование постбэка</h3>


    <form class="row" method="post" action="{{ route('admin.postbacks.test.store') }}">
        @csrf
        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Отправить тестовый постбэк
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <label for="link">Партнерская программа</label>
                        <select name="source_id" class="form-control select2" required>
                            @foreach($sources as $id => $name)
                                <option value="{{ $id }}" @if(old('source_id') == $id) selected @endif>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="link">Тестовая ссылка</label>
                        <input type="text" name="link" id="link" class="form-control"
                               value="{{ old('link') }}" required />
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-12">
            <input type="submit" value="Отправить" class="btn btn-primary"/>
        </div>
    </form>
@stop