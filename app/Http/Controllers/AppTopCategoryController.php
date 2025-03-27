<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class AppTopCategoryController extends Controller
{
    public function getPositions(Request $request)
    {
        if (!$request->has('date')) {
            return response()->json([
                'error' => 'Bad request: Missing required query parameter: date'
            ], 400);
        }
        $validated = $request->validate([
            'date' => 'required|date_format:Y-m-d',
        ]);

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


         // Проверяем наличие кэша
        // Если записи нет, делаем запрос и сохраняем результат.
        $cachedResponse = Cache::remember($cacheKey, now()->addDays(30), function () use ($url, $queryParams, $date) {
            $response = Http::get($url, $queryParams);
            if ($response->failed()) {
                // В случае ошибки не кэшируем; бросаем исключение либо возвращаем null
                return [
                    'status_code' => $response->status(),
                    'message'     => 'Failed to fetch data from Apptica',
                    'data'        => [],
                    'max_positions' => []
                ];
            }

            $json = $response->json();
            $appticaData = $json['data'] ?? [];

            // Получаем "максимальные позиции" (на самом деле это минимальное число среди subcategory)
            $maxPositionsByCategory = [];
            foreach ($appticaData as $category => $subcatData) {
                $positions = [];
                foreach ($subcatData as $subCategory => $dates) {
                    if (!empty($dates[$date])) {
                        $positions[] = $dates[$date];
                    }
                }
                if (!empty($positions)) {
                    $maxPositionsByCategory[$category] = min($positions);
                }
            }

            // Сформируем структуру, которую будем возвращать и кэшировать
            return [
                'status_code'   => 200,
                'message'       => 'ok',
                'data'          => $appticaData,
                'max_positions' => $maxPositionsByCategory,
            ];
        });

        // Возвращаем уже готовый результат (либо из кэша, либо свежий)
        return response()->json($cachedResponse);
    }
}
