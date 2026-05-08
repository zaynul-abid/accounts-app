<?php

namespace App\Support;

use App\Models\Place;
use Illuminate\Support\Facades\DB;

class SlNumberGenerator
{
    public static function forHouse(Place $mahallu, ?int $ignoreHouseId = null): string
    {
        $prefix = self::prefixFromName($mahallu->name);

        $query = DB::table('house_creations')
            ->where('place_id', $mahallu->id)
            ->whereNotNull('sl_number');

        if ($ignoreHouseId !== null) {
            $query->where('id', '!=', $ignoreHouseId);
        }

        return self::nextFromExisting($prefix, $query->pluck('sl_number')->all());
    }

    public static function forMember(Place $mahallu): string
    {
        $prefix = self::prefixFromName($mahallu->name) . '-M';

        $existing = DB::table('members')
            ->join('house_creations', 'members.house_id', '=', 'house_creations.id')
            ->where('house_creations.place_id', $mahallu->id)
            ->whereNotNull('members.sl_number')
            ->pluck('members.sl_number')
            ->all();

        return self::nextFromExisting($prefix, $existing);
    }

    private static function prefixFromName(string $name): string
    {
        preg_match_all('/[A-Za-z0-9]+/', $name, $matches);
        $words = $matches[0] ?? [];

        if (count($words) >= 2) {
            return strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
        }

        if (count($words) === 1) {
            return strtoupper(substr($words[0], 0, 2));
        }

        return 'MH';
    }

    private static function nextFromExisting(string $prefix, array $existing): string
    {
        $max = 0;
        $pattern = '/^' . preg_quote($prefix, '/') . '-(\d+)$/i';

        foreach ($existing as $slNumber) {
            if (preg_match($pattern, (string) $slNumber, $matches)) {
                $max = max($max, (int) $matches[1]);
            }
        }

        $next = $max + 1;

        return $prefix . '-' . str_pad((string) $next, 2, '0', STR_PAD_LEFT);
    }
}
