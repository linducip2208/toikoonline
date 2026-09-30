{{-- Cek ongkir live (Biteship prioritas, fallback RajaOngkir). Dipakai di product-detail & cart. --}}
<div x-data="shippingChecker({{ $product->weight ?? 500 }})" class="bg-white border border-stone-200 rounded-2xl p-4 mt-4">
    <div class="flex items-center gap-2 mb-3">
        <i class="fas fa-truck text-brand-500"></i>
        <h3 class="font-bold text-sm text-stone-800">Cek Ongkir</h3>
        @if($freeOngkirMin ?? false)
        <span class="ml-auto text-[10px] font-bold text-green-700 bg-green-50 px-2 py-1 rounded-full">Gratis ongkir min. Rp150rb</span>
        @endif
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 mb-2">
        <input x-model="dest" @input.debounce.500ms="lookupArea" placeholder="Ketik kecamatan/kota… (cth: Bandung)" class="col-span-2 px-3 py-2.5 min-h-[44px] border border-stone-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-400" aria-label="Tujuan pengiriman">
        <select x-model="courier" class="px-3 py-2.5 min-h-[44px] border border-stone-200 rounded-xl text-sm bg-white" aria-label="Kurir">
            <option value="">Semua kurir</option>
            <option value="jne">JNE</option>
            <option value="jnt">J&T</option>
            <option value="sicepat">SiCepat</option>
            <option value="anteraja">AnterAja</option>
            <option value="gosend">GoSend</option>
        </select>
    </div>
    <div x-show="areas.length" class="mb-2 border border-stone-100 rounded-xl overflow-hidden">
        <template x-for="a in areas.slice(0,5)" :key="a.id">
            <button @click="selectArea(a)" class="w-full text-left px-3 py-2.5 text-sm hover:bg-brand-50 flex justify-between gap-2">
                <span x-text="a.name"></span><span class="text-stone-400 text-xs" x-text="a.postal_code"></span>
            </button>
        </template>
    </div>
    <button @click="checkCost()" :disabled="loading || !selectedArea" class="w-full py-2.5 min-h-[44px] bg-stone-900 hover:bg-stone-800 disabled:opacity-40 text-white text-sm font-semibold rounded-xl transition">
        <span x-show="!loading">Lihat Ongkir</span><span x-show="loading">Menghitung…</span>
    </button>
    <div x-show="results.length" class="mt-3 space-y-2">
        <template x-for="c in results" :key="c.courier + c.service">
            <div class="flex items-center justify-between text-sm border border-stone-100 rounded-xl px-3 py-2">
                <div><p class="font-semibold" x-text="c.courier.toUpperCase() + ' ' + c.service"></p><p class="text-[11px] text-stone-500" x-text="c.etd"></p></div>
                <p class="font-bold text-brand-600" x-text="'Rp ' + Number(c.cost).toLocaleString('id-ID')"></p>
            </div>
        </template>
        <p x-show="freeCover>0" class="text-[11px] text-green-700 font-medium">Termasuk subsidi gratis ongkir Rp <span x-text="Number(freeCover).toLocaleString('id-ID')"></span></p>
    </div>
    <p x-show="error" x-text="error" class="text-xs text-red-600 mt-2"></p>
</div>
<script>
function shippingChecker(weightDefault){
    return {
        dest:'', areas:[], selectedArea:null, courier:'', results:[], loading:false, error:'', freeCover:0, weight:weightDefault||500,
        async lookupArea(){
            if(this.dest.length<3){this.areas=[];return;}
            try{
                const r = await fetch(`/api/shipping/areas?q=${encodeURIComponent(this.dest)}`);
                const d = await r.json(); this.areas = d.data||[];
            }catch(e){}
        },
        selectArea(a){ this.selectedArea=a; this.dest=a.name; this.areas=[]; },
        async checkCost(){
            this.loading=true; this.error=''; this.results=[];
            try{
                const params = new URLSearchParams({destination:this.selectedArea?.id||'', weight:this.weight, courier:this.courier, subtotal:0});
                const r = await fetch('/api/shipping/cost?'+params.toString());
                const d = await r.json();
                if(!d.success) throw new Error(d.message||'Gagal hitung ongkir');
                this.freeCover = d.free_ongkir_cover||0;
                this.results = (d.data||[]).flatMap(g => (g.costs||[]).map(c=>({courier:g.courier, ...c}))).slice(0,6);
                if(!this.results.length) this.error='Ongkir tidak ditemukan untuk tujuan ini.';
            }catch(e){ this.error=e.message; }
            this.loading=false;
        }
    }
}
</script>
