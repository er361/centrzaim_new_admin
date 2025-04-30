<?php


namespace App\Services\ReportService;


class SmsReportStatisticsDataHolder
{
    /**
     * @var array
     */
    protected array $dataToInsert = [];

    /**
     * @return array
     */
    public function getData(): array
    {
        return array_values($this->dataToInsert);
    }

    /**
     * @param string $date
     * @param string|null $smsId
     * @param array $values
     * @return void
     */
    public function setData(string $date, ?string $smsId, array $values): void
    {
        $keyToSet = $this->getDataKey($date, $smsId);

        if (!isset($this->dataToInsert[$keyToSet])) {
            $this->dataToInsert[$keyToSet] = [
                'date' => $date,
                'sms_id' => $smsId,
            ];
        }

        $this->dataToInsert[$keyToSet] = array_merge($this->dataToInsert[$keyToSet], $values);
    }

    /**
     * Получить ранее установленные данные по ключу.
     *
     * @param string $date
     * @param string|null $smsId
     * @return array
     */
    public function getDataPart(string $date, ?string $smsId): array {
        $keyToSet = $this->getDataKey($date, $smsId);

        if (!isset($this->dataToInsert[$keyToSet])) {
            $this->dataToInsert[$keyToSet] = [
                'date' => $date,
                'sms_id' => $smsId,
            ];
        }

        return $this->dataToInsert[$keyToSet];
    }

    /**
     * @param string $date
     * @param string|null $smsId
     * @return string
     */
    protected function getDataKey(string $date, ?string $smsId): string
    {
        return $date . '_' . $smsId;
    }

}