<?php

namespace Dashed\DashedEcommerceWebwinkelkeur\Filament\Pages\Settings;

use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Dashed\DashedCore\Classes\Sites;
use Filament\Schemas\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Tabs\Tab;
use Dashed\DashedCore\Models\Customsetting;
use Filament\Infolists\Components\TextEntry;
use Dashed\DashedCore\Traits\HasSettingsPermission;
use Dashed\DashedEcommerceWebwinkelkeur\Classes\Webwinkelkeur;

class WebwinkelkeurSettingsPage extends Page
{
    use HasSettingsPermission;

    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $title = 'Webwinkelkeur';

    protected string $view = 'dashed-core::settings.pages.default-settings';
    public array $data = [];

    public function mount(): void
    {
        $formData = [];
        $sites = Sites::getSites();
        foreach ($sites as $site) {
            $formData["webwinkelkeur_client_id_{$site['id']}"] = Customsetting::get('webwinkelkeur_client_id', $site['id'], 'same');
            $formData["webwinkelkeur_auth_token_{$site['id']}"] = Customsetting::get('webwinkelkeur_auth_token', $site['id'], 'order');
            $formData["webwinkelkeur_connected_{$site['id']}"] = Customsetting::get('webwinkelkeur_connected', $site['id'], 0);
            $formData["webwinkelkeur_connection_error_{$site['id']}"] = Customsetting::get('webwinkelkeur_connection_error', $site['id'], '');
        }

        $this->form->fill($formData);
    }

    public function form(Schema $schema): Schema
    {
        $sites = Sites::getSites();
        $tabGroups = [];

        $tabs = [];
        foreach ($sites as $site) {
            $newSchema = [
                TextEntry::make("Webwinkelkeur voor {$site['name']}")
                    ->state('Activeer webwinkelkeur zodat de klanten automatisch een mail krijgen om een review achter te laten.')
                    ->columnSpan([
                        'default' => 1,
                        'lg' => 2,
                    ]),
                TextEntry::make("Webwinkelkeur is " . (! Customsetting::get('webwinkelkeur_connected', $site['id'], 0) ? 'niet' : '') . ' geconnect')
                    ->state(Customsetting::get('webwinkelkeur_connection_error', $site['id'], ''))
                    ->columnSpan([
                        'default' => 1,
                        'lg' => 2,
                    ]),
                TextInput::make("webwinkelkeur_client_id_{$site['id']}")
                    ->label('Webwinkelkeur Client ID')
                    ->maxLength(255),
                TextInput::make("webwinkelkeur_auth_token_{$site['id']}")
                    ->label('Webwinkelkeur Auth Token')
                    ->maxLength(255),
            ];

            $tabs[] = Tab::make($site['id'])
                ->label(ucfirst($site['name']))
                ->schema($newSchema)
                ->columns([
                    'default' => 1,
                    'lg' => 2,
                ]);
        }
        $tabGroups[] = Tabs::make('Sites')
            ->tabs($tabs);

        return $schema->schema($tabGroups)
            ->statePath('data');
    }

    public function submit()
    {
        $sites = Sites::getSites();

        foreach ($sites as $site) {
            Customsetting::set('webwinkelkeur_client_id', $this->form->getState()["webwinkelkeur_client_id_{$site['id']}"], $site['id']);
            Customsetting::set('webwinkelkeur_auth_token', $this->form->getState()["webwinkelkeur_auth_token_{$site['id']}"], $site['id']);
            Customsetting::set('webwinkelkeur_connected', Webwinkelkeur::isConnected($site['id']), $site['id']);
        }

        Notification::make()
            ->title('De Webwinkelkeur instellingen zijn opgeslagen')
            ->success()
            ->send();

        return redirect(WebwinkelkeurSettingsPage::getUrl());
    }
}
