<?php

namespace Dashed\DashedEcommerceWebwinkelkeur;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Dashed\DashedEcommerceWebwinkelkeur\Filament\Pages\Settings\WebwinkelkeurSettingsPage;

class DashedEcommerceWebwinkelkeurServiceProvider extends PackageServiceProvider
{
    public static string $name = 'dashed-ecommerce-webwinkelkeur';

    public function configurePackage(Package $package): void
    {
        cms()->registerSettingsPage(WebwinkelkeurSettingsPage::class, 'Webwinkelkeur', 'chat-bubble-left-ellipsis', 'Koppel Webwinkelkeur');

        if (method_exists(cms(), 'registerIntegration')) {
            cms()->registerIntegration([
                'slug' => 'webwinkelkeur',
                'label' => 'Webwinkelkeur',
                'icon' => 'heroicon-o-star',
                'category' => 'reviews',
                'settings_page' => WebwinkelkeurSettingsPage::class,
                'health_check' => fn (?string $siteId = null) => \Dashed\DashedCore\Integrations\IntegrationHealth::fromSettings(['webwinkelkeur_client_id', 'webwinkelkeur_auth_token'], $siteId, 'Client ID of auth-token ontbreekt'),
                'package' => 'dashed-ecommerce-webwinkelkeur',
            ]);
        }

        cms()->registerSettingsDocs(
            page: \Dashed\DashedEcommerceWebwinkelkeur\Filament\Pages\Settings\WebwinkelkeurSettingsPage::class,
            title: 'WebwinkelKeur instellingen',
            intro: 'Op deze pagina koppel je jouw webshop aan WebwinkelKeur. Via deze koppeling kunnen reviews automatisch worden opgehaald en worden er na een bestelling review-uitnodigingen naar je klanten verstuurd. Werk je met meerdere sites? Dan kun je per site een eigen WebwinkelKeur shop koppelen.',
            sections: [
                [
                    'heading' => 'Wat kun je hier instellen?',
                    'body' => <<<MARKDOWN
Op deze pagina regel je twee dingen:

- Het Client ID van je WebwinkelKeur shop.
- De Auth token waarmee je webshop zich authenticeert bij de WebwinkelKeur API.
MARKDOWN,
                ],
                [
                    'heading' => 'Hoe zet je dit op?',
                    'body' => <<<MARKDOWN
1. Log in op je WebwinkelKeur dashboard.
2. Ga naar het onderdeel API of Integraties.
3. Kopieer je Client ID (dit is het shop ID van je WebwinkelKeur account).
4. Kopieer de bijbehorende Auth token. Heb je nog geen token, maak deze dan eerst aan in je WebwinkelKeur dashboard.
5. Plak beide waarden op deze pagina.
6. Sla de instellingen op.
MARKDOWN,
                ],
            ],
            fields: [
                'Client ID' => 'Het Client ID van je WebwinkelKeur shop. Dit is hetzelfde als je shop ID bij WebwinkelKeur en bepaalt aan welke shop de reviews gekoppeld worden.',
                'Auth token' => 'De Auth token waarmee jouw webshop verbinding maakt met de WebwinkelKeur API. Behandel deze token als een wachtwoord en deel hem niet met derden.',
            ],
            tips: [
                'Bewaar je Auth token op een veilige plek. Mocht hij ooit uitlekken, maak dan direct een nieuwe aan in je WebwinkelKeur dashboard.',
            ],
        );

        $package
            ->name('dashed-ecommerce-webwinkelkeur');

        cms()->builder('plugins', [
            new DashedEcommerceWebwinkelkeurPlugin(),
        ]);
    }
}
