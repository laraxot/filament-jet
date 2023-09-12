<?php

namespace ArtMin96\FilamentJet\Datas;

use Spatie\LaravelData\Data;

class ShouldRegisterNavigationData extends Data
{
    public bool $account;

    public bool $api_tokens;

    public bool $team_settings;

    public bool $create_team;
}
