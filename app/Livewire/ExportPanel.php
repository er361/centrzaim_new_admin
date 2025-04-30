<?php

namespace App\Livewire;

use App\Exports\FullUserExport;
use App\Exports\UserExport;
use App\Models\User;
use App\Models\Webmaster;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;

class ExportPanel extends Component
{
    /**
     * Выбранный ID вебмастера
     *
     * @var int|null
     */
    public $webmasterId;

    /**
     * Выбранный период дат
     *
     * @var string|null
     */
    public $dateRange;

    /**
     * Вебмастеры для селектора
     *
     * @var array
     */
    public $webmasters = [];

    /**
     * Флаг, могут ли пользователи экспортировать
     *
     * @var bool
     */
    public $canExport = false;

    /**
     * Инициализация компонента
     */
    public function mount()
    {
        // Загружаем вебмастеров
        $this->loadWebmasters();

        // Проверяем права доступа для экспорта (только роль 3)
        $this->canExport = Auth::user()->isSuperAdmin();
    }

    /**
     * Загрузка вебмастеров из БД
     */
    protected function loadWebmasters()
    {
        // Получаем вебмастеров с API ID и ID
        $webmastersData = Webmaster::select('id', 'api_id')
            ->orderBy('api_id')
            ->get();

        // Формируем массив для селектора (api_id => id)
        $this->webmasters = $webmastersData->mapWithKeys(function ($item) {
            return [$item->api_id => $item->id];
        })->toArray();
    }

    /**
     * Экспорт данных
     */
    public function export()
    {
        // Проверка прав доступа
        if (!$this->canExport) {
            session()->flash('error', 'У вас нет прав для экспорта данных.');
            return;
        }

        // Формируем запрос для выборки данных
        $query = User::query()->with(['webmaster','webmaster.source']); // Замените на вашу таблицу

        // Фильтр по вебмастеру
        if (!empty($this->webmasterId)) {
            $query->where('webmaster_id', $this->webmasterId);
        }

        // Фильтр по дате, если указан период
        if (!empty($this->dateRange)) {
            $dates = explode(' - ', $this->dateRange);
            if (count($dates) == 2) {
                $startDate = Carbon::createFromFormat('d.m.Y', $dates[0])->startOfDay();
                $endDate = Carbon::createFromFormat('d.m.Y', $dates[1])->endOfDay();

                $query->whereBetween('created_at', [$startDate, $endDate]);
            }
        }

        // Генерируем имя файла с текущей датой и временем
        $fileName = 'users_export_' . date('Y-m-d_H-i-s') . '.xlsx';

        return Excel::download(new FullUserExport($query), $fileName);
    }
    /**
     * Сброс фильтров
     */
    public function resetFilters()
    {
        $this->webmasterId = null;
        $this->dateRange = null;

        $this->emit('filtersReset');
    }

    /**
     * Рендеринг компонента
     */
    public function render()
    {
        return view('livewire.export-panel');
    }
}


