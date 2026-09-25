<?php

namespace App\Filament\Resources\DownloadableForms\Pages;

use App\Filament\Resources\DownloadableForms\DownloadableFormResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditDownloadableForm extends EditRecord
{
    protected static string $resource = DownloadableFormResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
