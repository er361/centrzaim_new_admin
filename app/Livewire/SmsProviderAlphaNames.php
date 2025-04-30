<?php

namespace App\Livewire;

use App\Models\Sms;
use App\Models\SmsProvider;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

class SmsProviderAlphaNames extends Component
{
    public $providers = [];
    public $fromNames = [];
    public $selectedProvider = '';
    public $selectedFrom = '';
    public ?Sms $sms;

    public function mount(Sms $sms = null, $selectedProvider = null)
    {
        if (!$sms->exists) {
            throw new \InvalidArgumentException('Объект Sms обязателен!');
        }

        $this->sms = $sms;

        // Загружаем провайдеров
        $this->providers = SmsProvider::pluck('name', 'id')->toArray();

        // Если передан провайдер — загружаем связанные "from_names"
        if ($selectedProvider) {
            $this->selectedProvider = $selectedProvider;
            $this->updateFromNames($selectedProvider);
        }
    }

    public function updatedSelectedProvider($providerId)
    {
        $this->updateFromNames($providerId);
        $this->selectedFrom = ''; // Сбрасываем выбор "from"
    }

    private function updateFromNames($providerId)
    {
        $provider = SmsProvider::find($providerId);
        $this->fromNames = $provider ? $provider->from_name : [];

        // Если список пуст — сбрасываем выбор
        if (empty($this->fromNames)) {
            $this->selectedFrom = '';
            return;
        }

        // Если в fromNames есть from из sms, выбираем его
        if (in_array($this->sms->from, $this->fromNames)) {
            $this->selectedFrom = $this->sms->from;
        } else {
            // Иначе выбираем ключ с индексом 1 (если есть)
            $keys = array_keys($this->fromNames);
            $this->selectedFrom = $keys[1] ?? reset($keys); // Если нет индекса 1, берём первый элемент
        }
    }

    public function render()
    {
        $view = view('livewire.sms-provider-alpha-names');

        return $view;
    }
}
