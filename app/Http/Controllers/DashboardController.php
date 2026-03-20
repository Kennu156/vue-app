<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class DashboardController extends Controller
{
    private const DEFAULT_CITY = 'Kuressaare';
    private const CACHE_TTL = 3600;

    public function __invoke(Request $request)
    {
        $city = trim($request->query('city', self::DEFAULT_CITY));
        $city = preg_replace('/[^a-zA-ZÀ-ÿ\s,\-]/', '', $city) ?: self::DEFAULT_CITY;

        $weather  = Cache::remember('weather-'  . strtolower($city), self::CACHE_TTL, fn () => $this->fetchWeather($city));
        $forecast = Cache::remember('forecast-' . strtolower($city), self::CACHE_TTL, fn () => $this->fetchForecast($city));

        return Inertia::render('Dashboard', [
            'weather'  => $weather,
            'forecast' => $forecast,
        ]);
    }

    private function fetchWeather(string $city): ?array
    {
        try {
            return Http::timeout(8)->get('https://api.openweathermap.org/data/2.5/weather', [
                'q'     => $city,
                'units' => 'metric',
                'lang'  => 'et',
                'appid' => config('services.open_weather_map.key'),
            ])->json();
        } catch (\Exception $e) {
            Log::error('Weather API error', ['city' => $city, 'error' => $e->getMessage()]);
            return null;
        }
    }

    private function fetchForecast(string $city): ?array
    {
        try {
            return Http::timeout(8)->get('https://api.openweathermap.org/data/2.5/forecast', [
                'q'     => $city,
                'units' => 'metric',
                'lang'  => 'et',
                'cnt'   => 40,
                'appid' => config('services.open_weather_map.key'),
            ])->json();
        } catch (\Exception $e) {
            Log::error('Forecast API error', ['city' => $city, 'error' => $e->getMessage()]);
            return null;
        }
    }
}