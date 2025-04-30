<?php


namespace App\Services\ReportService;


class RevenueReportStatisticsDataHolder
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
     * @param string|null $sourceId
     * @param string|null $webmasterId
     * @param array $values
     * @return void
     */
    public function setData(string $date, ?string $sourceId, ?string $webmasterId, array $values): void
    {
        $keyToSet = $this->getDataKey($date, $sourceId, $webmasterId);

        if (!isset($this->dataToInsert[$keyToSet])) {
            $this->dataToInsert[$keyToSet] = [
                'date' => $date,
                'source_id' => $sourceId,
                'webmaster_id' => $webmasterId,
            ];
        }

        $this->dataToInsert[$keyToSet] = array_merge($this->dataToInsert[$keyToSet], $values);
    }

    /**
     * Получить ранее установленные данные по ключу.
     *
     * @param string $date
     * @param string|null $sourceId
     * @param string|null $webmasterId
     * @return array
     */
    public function getDataPart(string $date, ?string $sourceId, ?string $webmasterId): array {
        $keyToSet = $this->getDataKey($date, $sourceId, $webmasterId);

        if (!isset($this->dataToInsert[$keyToSet])) {
            $this->dataToInsert[$keyToSet] = [
                'date' => $date,
                'source_id' => $sourceId,
                'webmaster_id' => $webmasterId,
            ];
        }

        return $this->dataToInsert[$keyToSet];
    }

    /**
     * @param string $date
     * @param string|null $sourceId
     * @param string|null $webmasterId
     * @return string
     */
    protected function getDataKey(string $date, ?string $sourceId, ?string $webmasterId): string
    {
        return $date . '_' . $sourceId . '_' . $webmasterId;
    }

}