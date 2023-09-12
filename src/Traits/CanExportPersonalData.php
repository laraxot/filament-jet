<?php

namespace ArtMin96\FilamentJet\Traits;

use ArtMin96\FilamentJet\Features;
use Illuminate\Support\Str;
use Spatie\PersonalDataExport\PersonalDataSelection;

trait CanExportPersonalData
{
    public function personalDataExportName(): string
    {
        $userName = Str::slug($this->name);
        $exportName = Features::getOption(Features::personalDataExport(), 'export-name');

        return sprintf('%s-%s.zip', $exportName, $userName);
    }

    public function selectPersonalData(PersonalDataSelection $personalDataSelection): void
    {
        $personalDataSelection->add('user.json', ['name' => $this->name, 'email' => $this->email]);

        if (Features::managesProfilePhotos()) {
<<<<<<< HEAD
            $personalDataSelection->addFile(storage_path(sprintf('app/%s/%s', $this->profilePhotoDisk(), $this->profile_photo_path)));
=======
            $personalDataSelection->addFile(storage_path("app/{$this->profilePhotoDisk()}/{$this->profile_photo_path}"));
>>>>>>> d2abb10143a78f54643890ce9d627c88f47f59a0
        }

        $additionalFile = Features::getOption(Features::personalDataExport(), 'add');
        $additionalFiles = Features::getOption(Features::personalDataExport(), 'add-files');

        if (! empty($additionalFile)) {
            foreach ($additionalFile as $file) {
                $personalDataSelection->add(
                    $file['nameInDownload'],
                    $file['content']
                );
            }
        }

        if (! empty($additionalFiles)) {
            foreach ($additionalFiles as $additionalFile) {
                $personalDataSelection->addFile(
                    $additionalFile['pathToFile'],
                    $additionalFile['diskName'],
                    $additionalFile['directory']
                );
            }
        }
    }
}
