<div>
    <div class="col-xs-6 form-group">
        <label for="sms_provider_id">Провайдер*</label>
        <select wire:model.live="selectedProvider" class="form-control" required>
            <option value="">Выберите провайдера</option>
            @foreach($providers as $id => $name)
                <option value="{{ $id }}">{{ $name }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-xs-6 form-group">
        <label for="from_name">Имя отправителя</label>
        <select wire:model.live="selectedFrom" name="from" class="form-control" required>
            <option value="">Выберите имя</option>
            @foreach($fromNames as $name)
                <option value="{{ $name }}">{{ $name }}</option>
            @endforeach
        </select>
    </div>
</div>

