<div>
    <div class="col-xs-6 form-group">
        <label for="sms_provider_id">Провайдер*</label>
        <select wire:model.live="selectedProvider" class="form-control" required>
            <option value="">Выберите провайдера</option>
            @foreach($providers as $id => $name)
                <option value="{{ $id }}">{{ $name }}</option>
            @endforeach
        </select>
        <!-- Добавляем скрытое поле для sms_provider_id -->
        <input type="hidden" name="sms_provider_id" value="{{ $selectedProvider }}">
    </div>

    <div class="col-xs-6 form-group">
        <label for="from_name">Имя отправителя</label>
        <select wire:model.live="selectedFrom" name="from" class="form-control" required>
            <option value="">Выберите имя</option>
            @if(is_array($fromNames) && count($fromNames) > 0)
                @foreach($fromNames as $name)
                    <option value="{{ $name }}">{{ $name }}</option>
                @endforeach
            @endif
        </select>
    </div>
</div>

