<?php

namespace App\Services\Content;

/**
 * Sanitasi HTML konten CMS (Page/Blog) saat simpan: cegah XSS tersimpan
 * (script, event handler, javascript: URL, form/iframe/objek) sambil
 * mempertahankan formatting editor (p, heading, list, table, link, img...).
 * Tanpa dependensi tambahan — DOMDocument allowlist.
 */
class SafeHtml
{
    protected static array $allowedTags = [
        'p', 'br', 'hr',
        'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
        'strong', 'b', 'em', 'i', 'u', 's', 'strike', 'mark', 'small',
        'sup', 'sub', 'blockquote', 'pre', 'code',
        'ul', 'ol', 'li', 'dl', 'dt', 'dd',
        'a', 'img', 'figure', 'figcaption',
        'table', 'thead', 'tbody', 'tfoot', 'tr', 'th', 'td', 'caption', 'colgroup', 'col',
        'div', 'span', 'section', 'article',
    ];

    protected static array $allowedAttrs = [
        'href', 'src', 'alt', 'title', 'width', 'height', 'loading',
        'target', 'rel', 'colspan', 'rowspan', 'align',
        'class', 'id', 'style',
    ];

    public static function clean(?string $html): ?string
    {
        if ($html === null || trim($html) === '') {
            return $html;
        }

        $prev = libxml_use_internal_errors(true);
        $doc = new \DOMDocument('1.0', 'UTF-8');
        // bungkus agar fragment multi-root tetap utuh
        $doc->loadHTML('<?xml encoding="utf-8"?><div id="__root__">'.$html.'</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();
        libxml_use_internal_errors($prev);

        $xpath = new \DOMXPath($doc);
        foreach ($xpath->query('//*') as $node) {
            /** @var \DOMElement $node */
            $tag = strtolower($node->nodeName);
            if ($tag === 'html' || $tag === 'body') {
                continue;
            }
            if (! in_array($tag, static::$allowedTags, true)) {
                // Unwrap: buang tag, pertahankan isi teks/child aman.
                $frag = $doc->createDocumentFragment();
                while ($node->firstChild) {
                    $frag->appendChild($node->firstChild);
                }
                $node->parentNode->replaceChild($frag, $node);
                continue;
            }
            // Bersihkan atribut: allowlist + tolak event handler + URL berbahaya.
            foreach (iterator_to_array($node->attributes) as $attr) {
                $name = strtolower($attr->nodeName);
                $value = trim($attr->nodeValue);
                if (! in_array($name, static::$allowedAttrs, true) || str_starts_with($name, 'on')) {
                    $node->removeAttribute($attr->nodeName);
                    continue;
                }
                if (in_array($name, ['href', 'src'], true)) {
                    $lower = strtolower(preg_replace('/\s+/', '', $value));
                    if (str_starts_with($lower, 'javascript:') || str_starts_with($lower, 'data:text/html') || str_starts_with($lower, 'vbscript:')) {
                        $node->removeAttribute($attr->nodeName);
                        continue;
                    }
                }
                if ($name === 'style' && preg_match('/expression|behaviour|javascript\s*:/i', $value)) {
                    $node->removeAttribute($attr->nodeName);
                }
            }
            if ($tag === 'a' && $node->hasAttribute('target') && $node->getAttribute('target') === '_blank') {
                $node->setAttribute('rel', trim($node->getAttribute('rel').' noopener'));
            }
        }

        $root = $doc->getElementById('__root__');
        if (! $root) {
            return '';
        }
        $out = '';
        foreach ($root->childNodes as $child) {
            $out .= $doc->saveHTML($child);
        }

        return $out;
    }
}
