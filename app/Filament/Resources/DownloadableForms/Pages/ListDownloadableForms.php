<?php

namespace App\Filament\Resources\DownloadableForms\Pages;

use App\Filament\Resources\DownloadableForms\DownloadableFormResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDownloadableForms extends ListRecords
{
    protected static string $resource = DownloadableFormResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
