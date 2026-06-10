<?php

namespace App\Services\Ai;

use App\Models\BusinessSetting;
use Illuminate\Support\Facades\Http;

class AiContentService
{
    protected string $baseUrl;
    protected string $apiKey;
    protected string $model;

    public function __construct()
    {
        $this->baseUrl = BusinessSetting::getValue('ai_api_url', '');
        $this->apiKey = BusinessSetting::getValue('ai_api_key', '');
        $this->model = BusinessSetting::getValue('ai_model', 'gpt-4o-mini');
    }

    public function generateProductDescription(string $productName, string $category, array $features = []): string
    {
        $prompt = "Kamu adalah copywriter e-commerce profesional Indonesia. Buat deskripsi produk yang menarik dalam Bahasa Indonesia untuk:\nNama Produk: {$productName}\nKategori: {$category}\nFitur: ".implode(', ', $features)."\n\nFormat: paragraf pembuka menarik, bullet-point fitur (pakai <ul><li>), paragraf penutup dengan CTA. Gunakan tone yang friendly dan profesional. Panjang 150-250 kata. Output langsung HTML tanpa wrapper.";

        return $this->generate($prompt);
    }

    public function generateMetaDescription(string $productName, string $category): string
    {
        $prompt = "Buat meta description SEO-friendly dalam Bahasa Indonesia (maks 160 karakter) untuk produk: {$productName} di kategori {$category}. Output langsung text tanpa kutipan.";
        return $this->generate($prompt);
    }

    public function generateProductTags(string $productName, string $category): array
    {
        $prompt = "Buat 5-8 tag keyword untuk produk e-commerce: {$productName} (kategori: {$category}). Output sebagai JSON array string. Hanya JSON, tanpa teks lain.";
        $result = $this->generate($prompt);
        return json_decode($result, true) ?? [$productName, $category];
    }

    public function generateFaqContent(string $productName): array
    {
        $prompt = "Buat 5 FAQ (pertanyaan dan jawaban) dalam Bahasa Indonesia tentang produk {$productName}. Format output sebagai JSON array dengan key 'question' dan 'answer'. Hanya JSON.";
        $result = $this->generate($prompt);
        return json_decode($result, true) ?? [];
    }

    protected function generate(string $prompt): string
    {
        if (empty($this->baseUrl) || empty($this->apiKey)) {
            return '';
        }

        $response = Http::withHeaders([
            'Authorization' => "Bearer {$this->apiKey}",
            'Content-Type' => 'application/json',
        ])->timeout(60)->post($this->baseUrl . '/chat/completions', [
            'model' => $this->model,
            'messages' => [['role' => 'user', 'content' => $prompt]],
            'temperature' => 0.7,
            'max_tokens' => 1000,
        ]);

        if ($response->successful()) {
            return $response->json()['choices'][0]['message']['content'] ?? '';
        }

        return '';
    }
}
