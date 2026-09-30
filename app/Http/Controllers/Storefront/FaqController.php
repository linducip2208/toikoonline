<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Page;

class FaqController extends Controller
{
    public function index()
    {
        // Sumber utama: halaman CMS bertipe "faq" yang aktif & terjadwal tampil.
        $faqPages = Page::active()->where('type', 'faq')->orderBy('title')->get();

        // Sumber tambahan: blok "faq" di dalam page builder (tipe lain).
        $blockFaqs = Page::active()
            ->where('type', '!=', 'faq')
            ->whereNotNull('blocks')
            ->get()
            ->flatMap(function (Page $page) {
                $items = [];
                foreach ((array) $page->blocks ?? [] as $block) {
                    if (($block['type'] ?? null) === 'faq' && ! empty($block['data']['items'])) {
                        foreach ((array) $block['data']['items'] as $item) {
                            $items[] = [
                                'question' => $item['question'] ?? '',
                                'answer' => $item['answer'] ?? '',
                                'source' => $page->title,
                            ];
                        }
                    }
                }

                return $items;
            })
            ->filter(fn ($i) => $i['question'] !== '' && $i['answer'] !== '')
            ->values();

        return view('storefront.faq', compact('faqPages', 'blockFaqs'));
    }
}
