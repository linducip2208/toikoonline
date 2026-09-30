<?php

namespace App\Traits;

use App\Models\EntityTranslation;

trait Translatable
{
    public function entityTranslations()
    {
        return $this->morphMany(EntityTranslation::class, 'translatable');
    }

    /**
     * Attributes on this model that support per-locale translation.
     * Override in the using model, e.g. protected array $translatableAttributes = ['name','description'];
     */
    public function getTranslatableAttributes(): array
    {
        return property_exists($this, 'translatableAttributes') ? (array) $this->translatableAttributes : [];
    }

    /**
     * Get translated value with fallback chain:
     * requested locale -> fallback setting (BusinessSetting translation_fallback or 'en') -> default locale -> raw attribute.
     */
    public function t(string $key, ?string $locale = null): mixed
    {
        $locale = $locale ?: app()->getLocale();
        $fallback = \App\Services\Locale\LocaleManager::fallbackLocale();
        $default = \App\Services\Locale\LocaleManager::defaultLocale();

        $chain = array_values(array_unique(array_filter([$locale, $fallback, $default])));
        foreach ($chain as $loc) {
            $row = $this->entityTranslations->where('tkey', $key)->where('locale', $loc)->where('status', 'published')->first()
                ?? EntityTranslation::where('translatable_type', static::class)
                    ->where('translatable_id', $this->getKey())
                    ->where('tkey', $key)->where('locale', $loc)->where('status', 'published')->first();
            if ($row && $row->tvalue !== null && $row->tvalue !== '') {
                return $row->tvalue;
            }
        }

        return $this->getAttribute($key);
    }

    public function setTranslation(string $key, string $locale, ?string $value, string $status = 'published'): EntityTranslation
    {
        return EntityTranslation::updateOrCreate(
            ['translatable_type' => static::class, 'translatable_id' => $this->getKey(), 'locale' => $locale, 'tkey' => $key],
            ['tvalue' => $value, 'status' => $status]
        );
    }
}
