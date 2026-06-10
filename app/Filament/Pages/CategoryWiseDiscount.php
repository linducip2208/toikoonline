<?php

namespace App\Filament\Pages;

use App\Models\Category;
use App\Models\CategoryDiscount;
use Filament\Pages\Page;
use Filament\Notifications\Notification;

class CategoryWiseDiscount extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?string $navigationGroup = '🎫 Promo';

    protected static ?int $navigationSort = 5;

    protected static string $view = 'filament.pages.category-wise-discount';

    protected static ?string $title = 'Diskon Per Kategori';

    public array $categories = [];

    public function mount(): void
    {
        $this->categories = Category::with('categoryDiscount')->get()->map(function ($category) {
            $cd = $category->categoryDiscount;
            return [
                'category_id' => $category->id,
                'category_name' => $category->name,
                'discount' => $cd?->discount ?? 0,
                'discount_type' => $cd?->discount_type ?? 'percent',
                'start_date' => $cd?->start_date ? date('Y-m-d\TH:i', $cd->start_date) : null,
                'end_date' => $cd?->end_date ? date('Y-m-d\TH:i', $cd->end_date) : null,
                'is_active' => $cd?->is_active ?? false,
            ];
        })->values()->toArray();
    }

    public function save(): void
    {
        foreach ($this->categories as $data) {
            if (empty($data['category_id'])) {
                continue;
            }

            $discount = floatval($data['discount'] ?? 0);

            if ($discount > 0) {
                CategoryDiscount::updateOrCreate(
                    ['category_id' => $data['category_id']],
                    [
                        'discount' => $discount,
                        'discount_type' => $data['discount_type'] ?? 'percent',
                        'start_date' => !empty($data['start_date']) ? strtotime($data['start_date']) : null,
                        'end_date' => !empty($data['end_date']) ? strtotime($data['end_date']) : null,
                        'is_active' => !empty($data['is_active']),
                    ]
                );
            } else {
                CategoryDiscount::where('category_id', $data['category_id'])->delete();
            }
        }

        Notification::make()
            ->title('Diskon kategori berhasil disimpan')
            ->success()
            ->send();

        $this->mount();
    }
}
