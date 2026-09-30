<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TranslationResource\Pages;
use App\Models\Translation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TranslationResource extends Resource
{
    protected static ?string $model = Translation::class;
    protected static ?string $navigationIcon = 'heroicon-o-language';
    protected static ?string $navigationGroup = '🧩 CMS';
    protected static ?int $navigationSort = 10;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('lang')
                ->required()->options(fn () => \App\Models\Language::where('status', true)->pluck('name', 'code')->all() ?: ['id' => 'Indonesia', 'en' => 'English']),
            Forms\Components\TextInput::make('lang_key')->required()->maxLength(255),
            Forms\Components\Textarea::make('lang_value')->required()->rows(3)->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('lang')->badge()->sortable(),
                Tables\Columns\TextColumn::make('lang_key')->searchable()->sortable()->limit(50),
                Tables\Columns\TextColumn::make('lang_value')->searchable()->limit(60),
                Tables\Columns\TextColumn::make('updated_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('lang')->options(fn () => \App\Models\Language::pluck('name', 'code')->all() ?: ['id' => 'id', 'en' => 'en']),
                Tables\Filters\Filter::make('missing_en')
                    ->label('Missing in en')
                    ->query(function ($q) {
                        $enKeys = Translation::where('lang', 'en')->pluck('lang_key')->all();
                        $idOnly = Translation::where('lang', 'id')->whereNotIn('lang_key', $enKeys)->pluck('id')->all();

                        return $q->whereIn('id', $idOnly);
                    }),
                Tables\Filters\Filter::make('missing_id')
                    ->label('Missing in id')
                    ->query(function ($q) {
                        $idKeys = Translation::where('lang', 'id')->pluck('lang_key')->all();
                        $enOnly = Translation::where('lang', 'en')->whereNotIn('lang_key', $idKeys)->pluck('id')->all();

                        return $q->whereIn('id', $enOnly);
                    }),
            ])
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('exportCsv')
                        ->label('Export CSV')->icon('heroicon-o-arrow-down-tray')
                        ->action(function ($records) {
                            $csv = "lang,lang_key,lang_value\n";
                            foreach ($records as $r) {
                                $csv .= '"'.str_replace('"', '""', $r->lang).'","'.str_replace('"', '""', $r->lang_key).'","'.str_replace('"', '""', (string) $r->lang_value)."\"\n";
                            }
                            $path = storage_path('app/translations-export-'.time().'.csv');
                            file_put_contents($path, $csv);

                            \Filament\Notifications\Notification::make()->title('Exported to '.$path)->success()->send();
                        }),
                ]),
            ])
            ->headerActions([
                Tables\Actions\Action::make('importCsv')
                    ->label('Import CSV')->icon('heroicon-o-arrow-up-tray')
                    ->form([Forms\Components\FileUpload::make('file')->acceptedFileTypes(['text/csv'])->directory('imports')->required()])
                    ->action(function (array $data) {
                        $path = storage_path('app/public/'.$data['file']);
                        if (! file_exists($path)) {
                            return;
                        }
                        $h = fopen($path, 'r');
                        fgetcsv($h);
                        $n = 0;
                        while (($row = fgetcsv($h)) !== false) {
                            if (count($row) < 3) {
                                continue;
                            }
                            Translation::updateOrCreate(['lang' => trim($row[0]), 'lang_key' => trim($row[1])], ['lang_value' => $row[2]]);
                            $n++;
                        }
                        fclose($h);
                        \Filament\Notifications\Notification::make()->title("Imported {$n} rows")->success()->send();
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTranslations::route('/'),
            'create' => Pages\CreateTranslation::route('/create'),
            'edit' => Pages\EditTranslation::route('/{record}/edit'),
        ];
    }
}
