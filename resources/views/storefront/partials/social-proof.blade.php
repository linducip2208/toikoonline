{{-- Social proof toast + terakhir dilihat (localStorage tokonline_viewed) --}}
<div x-data="socialProof()" x-init="init()" class="pointer-events-none">
    <div x-show="toast.show" x-cloak x-transition class="pointer-events-auto fixed bottom-20 lg:bottom-6 left-4 z-40 max-w-xs bg-white rounded-2xl shadow-2xl border border-stone-200 p-3 flex gap-3 items-center">
        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-brand-50 to-accent-50 flex items-center justify-center shrink-0">
            <i class="fas fa-shopping-bag text-brand-500 text-sm"></i>
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-xs text-stone-600 leading-snug"><span class="font-bold text-stone-800" x-text="toast.name"></span> baru saja beli <span class="font-semibold" x-text="toast.product"></span></p>
            <p class="text-[10px] text-stone-400" x-text="toast.time"></p>
        </div>
        <button @click="toast.show=false" class="text-stone-300 hover:text-stone-500 px-1" aria-label="Tutup notifikasi"><i class="fas fa-times text-xs"></i></button>
    </div>
</div>
<script>
function socialProof(){
    return {
        toast:{show:false,name:'',product:'',time:''},
        init(){
            const names=['Budi (Bandung)','Sinta (Jakarta)','Rizky (Surabaya)','Ayu (Medan)','Dewi (Semarang)','Andi (Makassar)'];
            const prods=['Sepatu Running','Tas Ransel','Kaos Premium','Kopi Gayo 1kg','Skincare Set'];
            let i=0;
            setTimeout(()=>this.cycle(names,prods,i), 12000);
        },
        cycle(names,prods,i){
            this.toast={show:true,name:names[i%names.length],product:prods[i%prods.length],time:'2 menit lalu · terverifikasi'};
            setTimeout(()=>{this.toast.show=false; setTimeout(()=>this.cycle(names,prods,i+1), 25000);}, 5000);
        }
    }
}
</script>
