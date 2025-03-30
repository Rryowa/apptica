<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\GetPositionsRequest;


class AppTopCategoryController extends Controller
{
    public function getPositions(GetPositionsRequest $request)
    {
        $validated = $request->validated();

        $date = $validated['date'];
        $applicationId = 1421444;
        $countryId = 1;
        $baseUrl = 'https://api.apptica.com/package/top_history';

        $url = sprintf('%s/%d/%d', $baseUrl, $applicationId, $countryId);
        $queryParams = [
            'date_from' => $date,
            'date_to'   => $date,
            'B4NKGg'    => 'fVN5Q9KVOlOHDx9mOsKPAQsFBlEhBOwguLkNEDTZvKzJzT3l',
        ];

        $cacheKey = "apptica_positions_{$date}";

        // Если ключа нет - выполнить замыкание
        $cachedResponse = Cache::remember($cacheKey, now()->addDays(30), function () use ($url, $queryParams, $date) {
            try {
                $response = Http::timeout(5)->get($url, $queryParams)->throw();

                $appticaData = $response->json()['data'] ?? [];

                if (empty($appticaData)) {
                    return [
                        'status_code'   => 200,
                        'message'       => 'ok',
                        'data'          => [],
                        'min_positions' => [],
                    ];
                }

                $minPositionsByCategory = [];
                foreach ($appticaData as $category => $subcatData) {
                    $positions = [];
                    foreach ($subcatData as $subCategory => $dates) {
                        if (!empty($dates[$date])) {
                            $positions[] = $dates[$date];
                        }
                    }
                    if (!empty($positions)) {
                        $minPositionsByCategory[$category] = min($positions);
                    }
                }

                return [
                    'status_code'   => 200,
                    'message'       => 'ok',
                    'data'          => $appticaData,
                    'min_positions' => $minPositionsByCategory,
                ];

            } catch (\Illuminate\Http\Client\RequestException $e) {
                Log::error('HTTP request failed: ' . $e->getMessage(), [
                    'url' => $url,
                    'params' => $queryParams,
                ]);

                throw new \Exception('Failed to fetch data from Apptica');
            }
        });

        return response()->json($cachedResponse);
    }
}
