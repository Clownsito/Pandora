<?php

namespace App\Services;

use App\Models\MarginRule;

class MarginRuleService
{
    public function get(string $channel, string $type): float
    {
        return MarginRule::where('channel', $channel)
            ->where('type', $type)
            ->value('margin_percent') ?? 0;
    }

    public function all()
    {
        return MarginRule::all();
    }
}
