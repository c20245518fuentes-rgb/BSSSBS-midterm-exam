<?php

namespace App\Services;

use App\Models\Client;
use Illuminate\Support\Facades\DB;

class ClientService
{
    /**
     * @param  array{name: string, phone: string}  $data
     */
    public function createClient(array $data): Client
    {
        return DB::transaction(fn (): Client => Client::query()->create([
            'name' => $this->cleanText($data['name']),
            'phone' => $this->cleanPhone($data['phone']),
        ]));
    }

    /**
     * @param  array{name: string, phone: string}  $data
     */
    public function updateClient(int $id, array $data): Client
    {
        return DB::transaction(function () use ($id, $data): Client {
            $client = Client::query()->findOrFail($id);
            $client->update([
                'name' => $this->cleanText($data['name']),
                'phone' => $this->cleanPhone($data['phone']),
            ]);

            return $client->refresh();
        });
    }

    /**
     * Removes formatting while preserving an optional leading international plus.
     */
    public function cleanPhone(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', trim($phone)) ?? '';

        return str_starts_with(trim($phone), '+') ? '+'.$digits : $digits;
    }

    private function cleanText(string $value): string
    {
        return trim(strip_tags($value));
    }
}
