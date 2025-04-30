@props([
    'attributes' => '',
    'fccp' => null,
])
{{--<table class="" {{ $attributes }}>--}}
{{--    <thead>--}}
{{--    <tr>--}}
{{--        <th>--}}
{{--            <span class="font-bold text-lg">Должник</span>--}}
{{--            <p class="text-sm font-normal">--}}
{{--                (Физ. лицо: ФИО, дата и место рождения; юр. лицо: наименование, юр. адрес, фактический--}}
{{--                адрес, ИНН)--}}
{{--            </p>--}}
{{--        </th>--}}
{{--        <th>--}}
{{--            <span class="font-bold text-lg">Исполнительное производство</span>--}}
{{--            <p class="text-sm font-normal">--}}
{{--                (Номер, дата возбуждения)--}}
{{--            </p>--}}
{{--        </th>--}}
{{--        <th>--}}
{{--            <span class="font-bold text-lg">Реквизиты исполнительного документа</span>--}}
{{--            <p class="text-sm font-normal">--}}
{{--                (Вид, дата принятия органом, номер, наименование органа, выдавшего исполнительный документ,--}}
{{--                ИНН взыскателя-организации)--}}
{{--            </p>--}}
{{--        </th>--}}
{{--        <th>--}}
{{--            <span class="font-bold text-lg">Дата, причина окончания или прекращения ИП</span>--}}
{{--            <p class="text-sm font-normal">--}}
{{--                (Статья, часть, пункт основания)--}}
{{--            </p>--}}
{{--        </th>--}}
{{--        <th>--}}
{{--            <span class="font-bold text-lg">Сервис</span>--}}
{{--        </th>--}}
{{--        <th>--}}
{{--            <span class="font-bold text-lg">Предмет исполнения, сумма непогашенной задолженности</span>--}}
{{--        </th>--}}
{{--        <th>--}}
{{--            <span class="font-bold text-lg">Отдел судебных приставов</span>--}}
{{--            <p class="text-sm font-normal">--}}
{{--                (Наименование, адрес)--}}
{{--            </p>--}}
{{--        </th>--}}
{{--        <th>--}}
{{--                        <span class="font-bold text-lg">Судебный пристав-исполнитель, телефон для получения информации</span>--}}
{{--        </th>--}}
{{--    </tr>--}}
{{--    </thead>--}}
{{--    <tbody class="text-sm">--}}
{{--    @foreach($tableData as $infoItem)--}}
{{--        <tr>--}}
{{--            --}}{{--        <td class="font-bold">Иванов Иван Иванович, 01.01.1990, г. Москва</td>--}}
{{--            --}}{{--            <td class="font-bold">{{$infoItem['dolzhnik']}}</td>--}}
{{--            --}}{{--            <td>1234567890, 01.01.2021</td>--}}
{{--            --}}{{--            <td>{{$infoItem['process_and_number']}}</td>--}}
{{--            --}}{{--            <td>Исполнительный лист, 01.01.2021, 1234567890, ФССП, 1234567890</td>--}}
{{--            --}}{{--            <td>Исполнительный лист, 01.01.2021, 1234567890, ФССП, 1234567890</td>--}}
{{--            --}}{{--            <td>01.01.2021, ст. 123</td>--}}
{{--            --}}{{--            <td>ФССП</td>--}}
{{--            --}}{{--            <td>1234567890</td>--}}
{{--            --}}{{--            <td>Отдел судебных приставов, г. Москва, ул. Пушкина, д. 1</td>--}}
{{--            --}}{{--            <td>8-800-555-35-35</td>--}}

{{--            <td class="font-bold">{{ $infoItem['dolzhnik'] }}</td>--}}
{{--            <td>{{ $infoItem['process_and_number'] }}</td>--}}
{{--            <td>{{ $infoItem['requzit_document'] }}</td>--}}
{{--            <td>{{ $infoItem['end_reason'] }}</td>--}}
{{--            <td>{{ $infoItem['service'] }}</td>--}}
{{--            <td>{{ $infoItem['subject_sum'] }}</td>--}}
{{--            <td>{{ $infoItem['department'] }}</td>--}}
{{--            <td>{{ $infoItem['officer_info'] }}</td>--}}
{{--        </tr>--}}
{{--    @endforeach--}}

{{--    </tbody>--}}
{{--</table>--}}

<table class="w-full border border-gray-300">
    <thead>
    <tr class="bg-gray-100">
        <th class="p-2 border border-gray-300">Должник</th>
        <th class="p-2 border border-gray-300">Исполнительное производство</th>
        <th class="p-2 border border-gray-300">Реквизиты</th>
        <th class="p-2 border border-gray-300">Причина окончания</th>
        <th class="p-2 border border-gray-300">Сервис</th>
        <th class="p-2 border border-gray-300">Предмет исполнения</th>
        <th class="p-2 border border-gray-300">Отдел</th>
        <th class="p-2 border border-gray-300">Судебный пристав</th>
    </tr>
    </thead>
    <tbody class="divide-y divide-gray-200">
    @if(count($tableData) === 0)
        <tr>
            <td colspan="9" class="text-center opacity-70">Мы не обнаружили данных по ФССП</td>
        </tr>
    @else
        @foreach($tableData as $infoItem)
            <tr>
                <td class="p-2 border border-gray-300">{{ $infoItem['dolzhnik'] }}</td>
                <td class="p-2 border border-gray-300">{{ $infoItem['process_and_number'] }}</td>
                <td class="p-2 border border-gray-300">{{ $infoItem['requzit_document'] }}</td>
                <td class="p-2 border border-gray-300">{{ $infoItem['end_reason'] }}</td>
                <td class="p-2 border border-gray-300">{{ $infoItem['service'] }}</td>
                <td class="p-2 border border-gray-300">{{ $infoItem['subject_sum'] }}</td>
                <td class="p-2 border border-gray-300">{{ $infoItem['department'] }}</td>
                <td class="p-2 border border-gray-300">{{ $infoItem['officer_info'] }}</td>
            </tr>
        @endforeach
    @endif
    </tbody>
</table>
