<?php

namespace App\Filament\Resources\WorkUnits\Pages;

use App\Filament\Resources\WorkUnits\WorkUnitResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditWorkUnit extends EditRecord
{
    protected static string $resource = WorkUnitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
