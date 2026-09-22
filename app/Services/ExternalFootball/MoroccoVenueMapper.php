<?php

namespace App\Services\ExternalFootball;

use App\Models\City;
use App\Models\Stadium;

class MoroccoVenueMapper
{
    public function status(): array
    {
        return [
            'cities_count' => City::query()->count(),
            'stadiums_count' => Stadium::query()->count(),
        ];
    }

    public function map(int $index): array
    {
        $stadiums = Stadium::query()
            ->with('city')
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        if ($stadiums->isEmpty()) {
            return [
                'blocked' => true,
                'error' => 'No active Moroccan stadiums are available for local venue mapping.',
                'mapping_index' => $index,
            ];
        }

        $mappedIndex = $index % $stadiums->count();
        $stadium = $stadiums->values()->get($mappedIndex);

        return [
            'blocked' => false,
            'local_stadium_id' => $stadium->id,
            'local_stadium_name' => $stadium->name,
            'city_name' => $stadium->city?->name,
            'mapping_index' => $mappedIndex,
        ];
    }
}
