<?php

namespace ArtMin96\FilamentJet\Jobs;

use Illuminate\Bus\Batchable;
use Spatie\PersonalDataExport\Jobs\CreatePersonalDataExportJob as BaseCreatePersonalDataExportJob;

final class CreatePersonalDataExportJob extends BaseCreatePersonalDataExportJob
{
    use Batchable;
}
