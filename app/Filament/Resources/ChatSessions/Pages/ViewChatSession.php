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
            Action::make('external_chat')
                ->label('Buka Chat (Publik)')
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->color('success')
                ->url(fn () => route('chat.show', $this->record->session_token))
                ->openUrlInNewTab()
                ->visible(fn () => $this->record->status === ChatSessionStatus::Active),

            Action::make('admin_chat')
                ->label('Chat Admin')
                ->icon('heroicon-o-chat-bubble-left-right')
                ->color('info')
                ->url(fn () => ChatSessionResource::getUrl('chat', ['record' => $this->record]))
                ->visible(fn () => $this->record->status === ChatSessionStatus::Active),

            EditAction::make(),
            DeleteAction::make(),
        ];
    }
}
