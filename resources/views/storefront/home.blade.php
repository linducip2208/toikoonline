@extends('layouts.storefront')

@section('title', 'Belanja Mudah, Harga Terbaik â€” TokoOnline')
@section('meta_description', 'Temukan ribuan produk berkualitas dengan harga bersaing dan pengiriman cepat ke seluruh Indonesia. Belanja online aman, nyaman, dan terpercaya hanya di TokoOnline.')

@push('styles')
<style>
    .hero-gradient { background: linear-gradient(135deg, #312e81 0%, #4338ca 30%, #6366f1 60%, #a21caf 100%); }
    .hero-gradient::before { content:'';position:absolute;inset:0;background:radial-gradient(circle at 20% 80%,rgba(168,85,247,.2) 0%,transparent 50%),radial-gradient(circle at 80% 20%,rgba(99,102,241,.25) 0%,transparent 50%),radial-gradient(circle at 50% 50%,rgba(236,72,153,.1) 0%,transparent 60%);pointer-events:none; }
    .slider-dot { width:10px;height:10px;border-radius:50%;background:rgba(255,255,255,.4);transition:all .3s;cursor:pointer; }
    .slider-dot.active { background:#fff;transform:scale(1.3); }
    .browser-mock { border-radius:14px;overflow:hidden;box-shadow:0 32px 64px -16px rgba(0,0,0,.35);border:2px solid rgba(255,255,255,.15); }
    .browser-dots span { display:inline-block;width:10px;height:10px;border-radius:50%; }
    .flash-timer-box { background:rgba(239,68,68,.15);border:1px solid rgba(239,68,68,.3); }
    .product-card:hover .product-image img { transform:scale(1.08); }
    .product-image img { transition:transform .5s cubic-bezier(.16,1,.3,1); }
    .discount-badge { background:linear-gradient(135deg,#ef4444,#dc2626); }
    .star-gold { color:#f59e0b; }
    .btn-gradient { background:linear-gradient(135deg,#6366f1,#8b5cf6);transition:transform .2s,box-shadow .2s; }
    .btn-gradient:hover { transform:translateY(-1px);box-shadow:0 8px 24px -6px rgba(99,102,241,.45); }
    .cat-sidebar { scrollbar-width:thin;scrollbar-color:rgba(0,0,0,.15) transparent; }
    .cat-sidebar::-webkit-scrollbar { width:4px; }
    .cat-sidebar::-webkit-scrollbar-thumb { background:rgba(0,0,0,.15);border-radius:2px; }
    .section-enter { opacity:0;transform:translateY(30px);transition:opacity .7s ease,transform .7s cubic-bezier(.16,1,.3,1); }
    .section-enter.visible { opacity:1;transform:translateY(0); }
    @media (max-width:640px) { .hero-headline { font-size:2rem; } }
</style>
@endpush

@section('content')
{{-- CMS versi kita: urutan + on/off section dikendalikan /admin → Homepage Sections ($cmsSectionOrder) --}}
@foreach(($cmsSectionOrder ?? ['hero','voucher_rail','flash_deals','todays_deal','banners_1','categories','featured_products','banners_2','best_sellers','banners_3','category_products','new_products','coupons','brands','newsletter','final_cta']) as $cmsKey)
@includeIf('storefront.home.sections.'.$cmsKey)
@endforeach
@endsection

@push('scripts')
<script>
    function flashTimer(endTimestamp) {
        return {
            end: endTimestamp * 1000,
            hours: 0, minutes: 0, seconds: 0,
            now: Date.now(),
            pad(n) { return String(n).padStart(2, '0'); },
            init() {
                this.tick();
                this.interval = setInterval(() => { this.now = Date.now(); this.tick(); }, 1000);
            },
            tick() {
                const diff = Math.max(0, Math.floor((this.end - this.now) / 1000));
                this.hours = Math.floor(diff / 3600);
                this.minutes = Math.floor((diff % 3600) / 60);
                this.seconds = diff % 60;
            }
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });

        document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
    });
</script>
@endpush
