<?php

namespace App\Services\BannerService;

use App\Models\Banner;
use App\Models\BannerStatistic;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Psr\Log\LoggerInterface;

/**
 * Сервис для работы с баннерной статистикой
 */
class AdsFinSyncStatisticsService
{
    /**
     * @var string
     */
    protected const API_BASE_URL = 'https://client.adsfin.pro/stats/api';

    /**
     * @var string
     */
    private string $token;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @param string|null $token
     * @param LoggerInterface|null $logger
     */
    public function __construct(?string $token = null, ?LoggerInterface $logger = null)
    {
        $this->token = $token ?? config('services.banner.adsfin.token', '');
        $this->logger = $logger ?? Log::channel('services');
    }

    /**
     * Синхронизирует статистику показов
     *
     * @param int $daysBack Количество дней назад для синхронизации
     * @return void
     * @throws \Exception При критических ошибках
     */
    public function syncCommonStatistics(int $daysBack = 7): void
    {
        $this->logger->info('AdsFinBannerService: Запуск синхронизации статистики показов');

        try {
            $now = CarbonImmutable::now();
            $startDate = $now->subDays($daysBack);
            $endDate = $now;

            // Получаем данные через первый метод API
            $data = $this->fetchCommonStatistics($startDate, $endDate);

            if (empty($data)) {
                $this->logger->info('AdsFinBannerService: Нет данных о показах для сохранения');
                return;
            }

            // Сохраняем данные о показах
            $this->processStatisticsData($data, 'saveCommonStatistics');

        } catch (\Exception $e) {
            $this->logger->error('AdsFinBannerService: Ошибка при синхронизации показов', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Синхронизирует статистику доходов
     *
     * @return void
     * @throws \Exception При критических ошибках
     */
    public function syncWebmasterStatistics(): void
    {
        $this->logger->info('AdsFinBannerService: Запуск синхронизации статистики доходов');

        try {
            $now = CarbonImmutable::now();
            $startDate = $now->subDay();
            $endDate = $now;

            // Получаем данные через второй метод API
            $data = $this->fetchWebmasterStatistics($startDate, $endDate);

            if (empty($data)) {
                $this->logger->info('AdsFinBannerService: Нет данных о доходах для сохранения');
                return;
            }

            // Сохраняем данные о доходах
            $this->processStatisticsData($data, 'saveWebmasterStatistics');

        } catch (\Exception $e) {
            $this->logger->error('AdsFinBannerService: Ошибка при синхронизации доходов', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Получает статистику показов
     *
     * @param \DateTimeInterface $startDate
     * @param \DateTimeInterface $endDate
     * @return array
     */
    protected function fetchCommonStatistics(\DateTimeInterface $startDate, \DateTimeInterface $endDate): array
    {
        if (empty($this->token)) {
            $this->logger->warning('AdsFinBannerService: Попытка получения статистики показов с пустым токеном');
            return [];
        }

        $startDateFormatted = $startDate->format('d.m.Y');
        $endDateFormatted = $endDate->format('d.m.Y');

        // Первый метод API
        $url = self::API_BASE_URL . '/v2/show-stat';
        $query = [
            'time' => "$startDateFormatted-$endDateFormatted",
            'group_by' => 'Зона,Сайт',
            'divide_by' => 'День'
        ];

        $this->logger->info('AdsFinBannerService: Запрос статистики показов', [
            'period' => "$startDateFormatted-$endDateFormatted"
        ]);

        return $this->makeApiRequest($url, $query);
    }

    /**
     * Получает статистику доходов
     *
     * @param \DateTimeInterface $startDate
     * @param \DateTimeInterface $endDate
     * @return array
     */
    protected function fetchWebmasterStatistics(\DateTimeInterface $startDate, \DateTimeInterface $endDate): array
    {
        if (empty($this->token)) {
            $this->logger->warning('AdsFinBannerService: Попытка получения статистики по вебмастерам с пустым токеном');
            return [];
        }

        $startDateFormatted = $startDate->format('d.m.Y');
        $endDateFormatted = $endDate->format('d.m.Y');

        $channelsData = [];
        // Второй метод API
        $url = self::API_BASE_URL . '/v1/show-divided-stats/';
        $query = [
            'time' => "$startDateFormatted-$endDateFormatted",
            'mark_name' => 'subid1',
        ];

        $this->logger->info('AdsFinBannerService: Запрос статистики по вебмастерам', [
            'period' => "$startDateFormatted-$endDateFormatted",
        ]);

        $data = $this->makeApiRequest($url, $query);
        return $data;
    }

    /**
     * Выполняет запрос к API
     *
     * @param string $url
     * @param array $query
     * @return array
     * @throws \Exception
     */
    protected function makeApiRequest(string $url, array $query): array
    {
        try {
            $response = Http::acceptJson()
                ->withToken($this->token, 'Token')
                ->timeout(30)
                ->get($url, $query);

            if ($response->failed()) {
                $statusCode = $response->status();
                $errorMessage = $response->body();

                $this->logger->error('AdsFinBannerService: Ошибка API', [
                    'status' => $statusCode,
                    'message' => $errorMessage,
                    'url' => $url
                ]);

                if ($statusCode >= 500) {
                    throw new \Exception("Ошибка сервера AdsFinBanner API: $statusCode - $errorMessage");
                }

                return [];
            }

            $responseData = $response->json();

            if (!is_array($responseData) || empty($responseData)) {
                $this->logger->warning('AdsFinBannerService: Получен пустой или некорректный ответ', [
                    'url' => $url
                ]);
                return [];
            }

            return $responseData;
        } catch (\Exception $e) {
            $this->logger->error('AdsFinBannerService: Ошибка при запросе к API', [
                'error' => $e->getMessage(),
                'url' => $url
            ]);
            throw $e;
        }
    }

    /**
     * Обрабатывает полученные данные статистики
     *
     * @param array $data Данные статистики
     * @param string $saveMethod Метод для сохранения данных
     * @return void
     */
    protected function processStatisticsData(array $data, string $saveMethod): void
    {
        $processedItems = 0;
        $failedItems = 0;

        if ($saveMethod === 'saveWebmasterStatistics') {
            // Обработка данных для каналов вебмастеров (структура массива отличается)
            foreach ($data as $domain => $domainData) {
                try {
                        $this->saveWebmasterStatistics($domain, $domainData);
                        $processedItems++;
                } catch (\Exception $e) {
                    $failedItems++;
                    $this->logger->error('AdsFinBannerService: Ошибка при сохранении данных канала', [
                        'error' => $e->getMessage(),
                        'domain' => $domain,
                        'method' => $saveMethod
                    ]);
                }
            }
        } else {
            // Обработка обычной статистики (оригинальный код)
            foreach ($data as $item) {
                try {
                    if ($this->isValidCommonStatisticsItem($item)) {
                        $this->saveCommonStatistics($item);
                        $processedItems++;
                    } else {
                        $failedItems++;
                        $this->logger->debug('AdsFinBannerService: Пропущен некорректный элемент', [
                            'item' => $item,
                            'method' => $saveMethod
                        ]);
                    }
                } catch (\Exception $e) {
                    $failedItems++;
                    $this->logger->error('AdsFinBannerService: Ошибка при сохранении элемента', [
                        'error' => $e->getMessage(),
                        'item' => $item,
                        'method' => $saveMethod
                    ]);
                }
            }
        }

        $this->logger->info('AdsFinBannerService: Обработка данных завершена', [
            'method' => $saveMethod,
            'processed' => $processedItems,
            'failed' => $failedItems,
            'total' => count($data)
        ]);
    }

    /**
     * Проверяет валидность элемента статистики показов
     *
     * @param array $item
     * @return bool
     */
    protected function isValidCommonStatisticsItem(array $item): bool
    {
        // Проверка наличия всех необходимых полей
        $requiredFields = [
            'period',
            'placement_name',
            'placement_id',
            'shows',
            'clicks',
            'earned'
        ];

        foreach ($requiredFields as $field) {
            if (!array_key_exists($field, $item) || ($item[$field] === null)) {
                return false;
            }
        }

        // Проверка формата даты
        try {
            Carbon::createFromFormat('Y-m-d', $item['period']);
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * Проверяет валидность данных статистики по вебмастерам для канала
     *
     * @param string $channel Идентификатор канала
     * @param array $channelData Данные статистики по каналу
     * @return bool
     */
    protected function isValidWebmasterStatisticsItem(string $channel, array $channelData): bool
    {
        // Проверяем, что канал не пустой
        if (empty($channel)) {
            return false;
        }

        // Проверяем, что данные канала не пустые и являются массивом
        if (empty($channelData) || !is_array($channelData)) {
            // Пустой массив может быть валидным результатом (нет данных для канала)
            return true;
        }

        // Проверяем, что есть хотя бы один ключ (домен/сайт) с данными
        foreach ($channelData as $domain => $stats) {
            // Проверяем, что это массив с данными и содержит поле cost
            if (is_array($stats) && isset($stats['cost'])) {
                return true;
            }
        }

        return false;
    }

    /**
     * Сохраняет элемент статистики показов
     *
     * @param array $item
     * @return void
     */
    protected function saveCommonStatistics(array $statisticItem): void
    {
        // Получение основных данных из элемента статистики
        $period = Arr::get($statisticItem, 'period');
        $placementId = Arr::get($statisticItem, 'placement_id');
        $shows = (int)Arr::get($statisticItem, 'shows');
        $clicks = (int)Arr::get($statisticItem, 'clicks');
        $earned = (float)Arr::get($statisticItem, 'earned');

        $bannerId = Banner::where('placement_id', $placementId)->first()?->id;

        // Расчет дополнительных метрик
        // CTR (Click-Through Rate)
        $ctr = $shows > 0 ? ($clicks / $shows) * 100 : 0;

        // eCPM (Effective Cost Per Mille) - доход на 1000 показов
        $eCpm = $shows > 0 ? ($earned / $shows) * 1000 : 0;

        try {
            // Сохранение или обновление записи в базе данных
            BannerStatistic::query()->updateOrCreate(
                [
                    'api_date' => $period,
                    'banner_id' => $bannerId,
                ],
                [
                    'impressions' => $shows,
                    'clicks' => $clicks,
                    'ctr' => $ctr,
                    'revenue' => $earned,
                    'e_cpm' => $eCpm,
                ]
            );

            $this->logger->debug('AdsFinStatisticSaver: Сохранена статистика', [
                'date' => $period,
                'banner_id' => $bannerId,
                'impressions' => $shows,
                'revenue' => $earned
            ]);
        } catch (\Exception $e) {
            $this->logger->error('AdsFinStatisticSaver: Ошибка при сохранении в БД', [
                'error' => $e->getMessage(),
                'period' => $period,
                'banner_id' => $bannerId,
            ]);
            throw $e;
        }
    }

    /**
     * Сохраняет статистику по вебмастерам
     *
     * @param string $domain Идентификатор канала
     * @param array $channelData Данные статистики по каналу
     * @return void
     */
    /**
     * Сохраняет статистику по вебмастерам
     *
     * @param string $domain Строка с информацией о баннере, источнике и вебмастере
     * @param array $channelData Данные статистики
     * @return void
     */
    protected function saveWebmasterStatistics(string $domain, array $channelData): void
    {
        // Если данные пустые, просто пропускаем
        if (empty($channelData)) {
            $this->logger->debug('AdsFinBannerService: Пустые данные для домена', [
                'channel' => $domain
            ]);
            return;
        }

        // Удаляем параметр cost из массива, так как это общее значение, а не разбивка по вебмастерам
        $totalCost = $channelData['cost'] ?? 0;
        unset($channelData['cost']);

        // Дата для статистики (текущий день)
        $date = Carbon::now()->format('Y-m-d');

        // Проходим по всем вебмастерам в данных
        foreach ($channelData as $webmasterDomain => $cost) {
            // Проверяем что ключ имеет формат banner_X_source_Y_webmaster_Z
            if (!is_string($webmasterDomain) || $cost <= 0) {
                continue;
            }

            if (preg_match('/banner_(\d+)_source_(\d+)_webmaster_(\d+)/', $webmasterDomain, $matches)) {
                $bannerId = $matches[1];
                $sourceId = $matches[2];
                $webmasterId = $matches[3];

                try {
                    // Сохранение в таблицу banner_webmaster_statistics
                    $bannerStatistic = BannerStatistic::query()->updateOrCreate(
                        [
                            'api_date' => $date,
                            'banner_id' => $bannerId,
                            'source_id' => $sourceId,
                            'webmaster_id' => $webmasterId,
                        ],
                        [
                            'revenue' => (float)$cost,
                            'impressions' => 0,
                            'clicks' => 0,
                            'ctr' => 0,
                            'e_cpm' => 0,
                        ]
                    );

                    $this->logger->debug('AdsFinBannerService: Сохранена статистика по вебмастеру', [
                        'date' => $date,
                        'domain' => $webmasterDomain,
                        'revenue' => (float)$cost
                    ]);
                } catch (\Exception $e) {
                    $this->logger->error('AdsFinBannerService: Ошибка при сохранении статистики вебмастера', [
                        'error' => $e->getMessage(),
                        'domain' => $webmasterDomain
                    ]);
                    throw $e;
                }
            } else {
                $this->logger->warning('AdsFinBannerService: Невозможно распарсить вебмастера', [
                    'domain' => $webmasterDomain
                ]);
            }
        }
    }

    /**
     * Синхронизирует все виды статистики
     *
     * @param int $daysBack
     * @return void
     * @throws \Exception
     */
    public function syncIncome(int $daysBack = 7): void
    {
        $this->logger->info('AdsFinBannerService: Запуск полной синхронизации');

        try {
            $this->syncCommonStatistics($daysBack);
            $this->syncWebmasterStatistics($daysBack);

            $this->logger->info('AdsFinBannerService: Полная синхронизация завершена успешно');
        } catch (\Exception $e) {
            $this->logger->error('AdsFinBannerService: Ошибка при полной синхронизации', [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
}