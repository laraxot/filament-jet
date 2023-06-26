<?php

namespace Spatie\PersonalDataExport;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use Spatie\PersonalDataExport\Exceptions\CouldNotAddToPersonalDataSelection;
use Spatie\TemporaryDirectory\TemporaryDirectory;

class PersonalDataSelection
{
    protected array $files = [];

    public ExportsPersonalData | Model $user;

    public function __construct(
        protected TemporaryDirectory $temporaryDirectory
    ) {
    }

    public function files(): array
    {
        return $this->files;
    }

    public function forUser(ExportsPersonalData $user)
    {
        $this->user = $user;

        return $this;
    }

    public function add(string $nameInDownload, array | string $content): PersonalDataSelection
    {
        if (! is_string($content)) {
            $content = json_encode($content, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        }

        $path = $this->temporaryDirectory->path($nameInDownload);

        $this->ensureDoesNotOverwriteExistingFile($path);

        $this->files[] = $path;

        file_put_contents($path, $content);

        return $this;
    }

    public function addFile(string $pathToFile, string $diskName = null, string $directory = null)
    {
        return is_null($diskName)
            ? $this->copyLocalFile($pathToFile, $directory)
            : $this->copyFileFromDisk($pathToFile, $diskName, $directory);
    }

    protected function copyLocalFile(string $pathToFile, string $directory = null)
    {
        $directory = trim($directory, '/');

        $destination = $this->getDestinationFilePath(pathinfo($pathToFile, PATHINFO_BASENAME), $directory);

        $this->ensureDoesNotOverwriteExistingFile($destination);

        (new Filesystem())->copy($pathToFile, $destination);

        $this->files[] = $destination;

        return $this;
    }

    protected function copyFileFromDisk(string $pathOnDisk, string $diskName, string $directory = null)
    {
        $directory = trim($directory, '/');

        $stream = Storage::disk($diskName)->readStream($pathOnDisk);

        $pathOnDirectory = (is_null($directory) ? '' : $directory . '/') . $pathOnDisk;

        $pathInTemporaryDirectory = $this->getDestinationFilePath($pathOnDisk, $directory);

        $this->ensureDoesNotOverwriteExistingFile($pathInTemporaryDirectory);

        file_put_contents($pathInTemporaryDirectory, stream_get_contents($stream), FILE_APPEND);

        $this->files[] = $pathInTemporaryDirectory;

        return $this;
    }

    protected function getDestinationFilePath($pathOnDisk, $directory)
    {
        $directory = (is_null($directory) ? '' : $directory . '/');

        return config('personal-data-export.keep_directory_structure')
            ? $this->temporaryDirectory->path($directory . $pathOnDisk)
            : $this->temporaryDirectory->path(
                $directory.array_slice(explode('/', $pathOnDisk), -1)[0]
            );
    }

    protected function ensureDoesNotOverwriteExistingFile(string $path)
    {
        if (file_exists($path)) {
            throw CouldNotAddToPersonalDataSelection::fileAlreadyAddedToPersonalDataSelection($path);
        }
    }
}
