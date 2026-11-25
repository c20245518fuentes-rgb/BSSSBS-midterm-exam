<?php

namespace App\Services;

use App\Models\Service;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ServiceCatalogService
{
    /**
     * @param  array{category: string, name: string, duration_min: int|string, price: int|float|string}  $data
     */
    public function createService(array $data): Service
    {
        return DB::transaction(fn (): Service => Service::query()->create($this->sanitizeServiceData($data)));
    }

    /**
     * @param  array{category: string, name: string, duration_min: int|string, price: int|float|string}  $data
     */
    public function updateService(Service $service, array $data): Service
    {
        return DB::transaction(function () use ($service, $data): Service {
            $service->update($this->sanitizeServiceData($data));

            return $service;
        });
    }

    /**
     * @param  array{category: string, name: string, duration_min: int|string, price: int|float|string}  $data
     * @return array{category: string, name: string, duration_min: int, price: float}
     */
    private function sanitizeServiceData(array $data): array
    {
        return [
            'category' => Str::title($this->cleanText($data['category'])),
            'name' => Str::title($this->cleanText($data['name'])),
            'duration_min' => (int) $data['duration_min'],
            'price' => round((float) $data['price'], 2),
        ];
    }

    private function cleanText(string $value): string
    {
        return trim(strip_tags($value));
    }
}
