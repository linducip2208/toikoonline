{{-- Newsletter --}}
@if($settings['newsletter'] === '1')
<section class="py-16 bg-gradient-to-r from-brand-600 via-brand-700 to-accent-700">
    <div class="max-w-3xl mx-auto px-4 text-center reveal">
        <h2 class="font-display text-3xl font-bold text-white mb-3">{{ __('storefront.newsletter_title') }}</h2>
        <p class="text-brand-100/80 mb-8">{{ __('storefront.newsletter_subtitle') }}</p>
        <form action="{{ route('newsletter.subscribe') }}" method="POST" class="flex gap-3 max-w-md mx-auto">
            @csrf
            <div class="flex-1 relative">
                <i class="fas fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-stone-400 text-sm"></i>
                <input type="email" name="email" placeholder="{{ __('storefront.newsletter_email_ph') }}" required
                       class="w-full pl-10 pr-4 py-3 rounded-xl border-0 text-sm bg-white/10 text-white placeholder:text-white/50 focus:outline-none focus:ring-2 focus:ring-white/30 backdrop-blur-sm">
            </div>
            <button type="submit" class="px-6 py-3 bg-white text-brand-700 font-semibold text-sm rounded-xl hover:bg-brand-50 transition-colors shrink-0">
                {{ __('storefront.subscribe') }}
            </button>
        </form>
    </div>
</section>
@endif
