<?php

namespace App\Filament\Resources\DetailPesananResource\Pages;

use App\Filament\Resources\DetailPesananResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDetailPesanans extends ListRecords
{
    protected static string $resource = DetailPesananResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
