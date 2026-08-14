<?php

namespace Dashed\DashedEcommerceWebwinkelkeur\Mail\EmailBlocks;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Builder\Block;
use Dashed\DashedCore\Mail\EmailBlocks\EmailBlock;

class ReviewScoreBlock extends EmailBlock
{
    public static function key(): string
    {
        return 'webwinkelkeur-score';
    }

    public static function label(): string
    {
        return __('WebwinkelKeur beoordelingscijfer');
    }

    public static function contexts(): array
    {
        return [self::CONTEXT_NEWSLETTER];
    }

    public static function filamentBlock(): Block
    {
        // WebwinkelKeur zelf houdt geen cijfer of aantal beoordelingen bij in
        // dit pakket (de integratie stuurt alleen review-uitnodigingen), dus
        // de redacteur vult het cijfer hier zelf in, zoals bij de losse code
        // in het kortingscodeblok.
        return Block::make(self::key())
            ->label(self::label())
            ->icon('heroicon-o-star')
            ->schema([
                TextInput::make('intro')
                    ->label(__('Introductietekst')),
                TextInput::make('score')
                    ->label(__('Cijfer'))
                    ->numeric(),
                TextInput::make('review_count')
                    ->label(__('Aantal beoordelingen'))
                    ->numeric(),
            ]);
    }

    public static function render(array $blockData, array $context): string
    {
        $intro = trim((string) ($blockData['intro'] ?? ''));
        $score = $blockData['score'] ?? null;
        $reviewCount = $blockData['review_count'] ?? null;

        // Een blok dat "0,0 uit 0 beoordelingen" toont is erger dan geen
        // blok: zonder intro en zonder cijfer is er niets om te laten zien.
        if ($intro === '' && ! $score) {
            return '';
        }

        return view('dashed-ecommerce-webwinkelkeur::emails.blocks.review-score', [
            'intro' => $intro ?: null,
            'score' => $score,
            'reviewCount' => $reviewCount,
            'primaryColor' => $context['primaryColor'] ?? '#111827',
            'textColor' => $context['textColor'] ?? '#ffffff',
        ])->render();
    }
}
