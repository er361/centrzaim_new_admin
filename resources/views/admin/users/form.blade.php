<div class="panel panel-default">
    <div class="panel-heading">
        @isset($user)
            Редактирование пользователя
        @else
            Создание пользователя
        @endisset

    </div>

    <div class="panel-body">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    {!! Form::label('email', 'Email*', ['class' => 'control-label']) !!}
                    {!! Form::text('email', old('email'), ['class' => 'form-control', 'placeholder' => '', 'required' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('email'))
                        <p class="help-block">
                            {{ $errors->first('email') }}
                        </p>
                    @endif
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    {!! Form::label('password', isset($user) ? 'Новый пароль' : 'Пароль*', ['class' => 'control-label']) !!}
                    {!! Form::password('password', ['class' => 'form-control', 'placeholder' => '', 'autocomplete' => 'new-password', 'required' => !isset($user)]) !!}
                    <p class="help-block"></p>
                    @if($errors->has('password'))
                        <p class="help-block">
                            {{ $errors->first('password') }}
                        </p>
                    @endif
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    {!! Form::label('role_id', 'Роль', ['class' => 'control-label']) !!}
                    {!! Form::select('role_id', $roles, old('role_id'), ['class' => 'form-control select2',]) !!}
                    <p class="help-block"></p>
                    @if($errors->has('role_id'))
                        <p class="help-block">
                            {{ $errors->first('role_id') }}
                        </p>
                    @endif
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    {!! Form::label('accessible_webmaster_id[]', 'Вебмастера', ['class' => 'control-label']) !!}
                    {!! Form::select('accessible_webmaster_id[]', $webmasters, isset($user) ? $user->accessibleWebmasters->pluck('id') : [], ['class' => 'form-control select2', 'multiple' => 'multiple',]) !!}
                    <p class="help-block"></p>
                    @if($errors->has('accessible_webmaster_id'))
                        <p class="help-block">
                            {{ $errors->first('accessible_webmaster_id') }}
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>