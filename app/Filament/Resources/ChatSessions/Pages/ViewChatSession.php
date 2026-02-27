<?php

namespace App\Filament\Resources\ChatSessions\Pages;

use App\Enums\ChatSessionStatus;
use App\Filament\Resources\ChatSessions\ChatSessionResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewChatSession extends ViewRecord
{
    protected static string $resource = ChatSessionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('chat')
                ->label('Buka Chat')
                ->icon('heroicon-o-chat-bubble-left-right')
                ->color('success')
                ->url(fn () => ChatSessionResource::getUrl('chat', ['record' => $this->record]))
                ->visible(fn () => $this->record->status === ChatSessionStatus::Active),

            EditAction::make(),
            DeleteAction::make(),
        ];
    }
}
