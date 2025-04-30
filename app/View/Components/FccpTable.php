<?php

namespace App\View\Components;

use App\Models\Fccp;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class FccpTable extends Component
{
    public Fccp $fccp;

    public function __construct(Fccp $fccp)
    {
        $this->fccp = $fccp;
    }

    public function render(): View
    {
        $data = data_get($this->fccp->info, 'result', []);
        $tableData = collect($data)->map(function ($item) {
            return [
                'dolzhnik' => $item['debtor_name'] . ', ' . $item['debtor_dob'] . ', ' . $item['debtor_address'],
                'process_and_number' => $item['process_title'] . ', ' . $item['process_date'],
                'requzit_document' => $item['document_type'] . ', ' . $item['document_date'] . ', '
                    . $item['document_num'] . ', ' . $item['document_organization'] . ', ИНН: '
                    . $item['document_claimer_inn'],
                'end_reason' => $item['process_total'] ?: 'Не указано',
                'service' => $item['payment_available'] ? 'Доступно' : 'Недоступно',
                'subject_sum' => collect($item['subjects'])->map(function ($subject) {
                    return $subject['title'] . ': ' . data_get($subject,'sum');
                })->implode(', '),
                'department' => $item['department_title'] . ', ' . $item['department_address'],
                'officer_info' => $item['officer_name'] . ', ' . implode('; ', $item['officer_phones']),
            ];
        })->toArray();;

        return view('components.fccp-table', compact('tableData'));
    }
}
