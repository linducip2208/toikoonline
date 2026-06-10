<x-filament-panels::page>
    <div class="space-y-6">
        <form wire:submit="save" class="space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-stone-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-stone-50 border-b border-stone-200">
                            <tr>
                                <th class="px-4 py-3 font-semibold text-stone-700">Kategori</th>
                                <th class="px-4 py-3 font-semibold text-stone-700">Diskon</th>
                                <th class="px-4 py-3 font-semibold text-stone-700">Tipe</th>
                                <th class="px-4 py-3 font-semibold text-stone-700">Mulai</th>
                                <th class="px-4 py-3 font-semibold text-stone-700">Berakhir</th>
                                <th class="px-4 py-3 font-semibold text-stone-700">Aktif</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($categories as $index => $cat)
                                <tr class="border-b border-stone-100 hover:bg-stone-50/50">
                                    <td class="px-4 py-3 font-medium text-stone-800">
                                        {{ $cat['category_name'] }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <input type="number" wire:model="categories.{{ $index }}.discount"
                                               class="w-32 rounded-lg border-stone-300 text-sm py-1.5 px-3 focus:ring-brand-500 focus:border-brand-500"
                                               step="0.01" min="0">
                                    </td>
                                    <td class="px-4 py-3">
                                        <select wire:model="categories.{{ $index }}.discount_type"
                                                class="rounded-lg border-stone-300 text-sm py-1.5 px-3 focus:ring-brand-500 focus:border-brand-500">
                                            <option value="percent">Persen (%)</option>
                                            <option value="amount">Nominal (Rp)</option>
                                        </select>
                                    </td>
                                    <td class="px-4 py-3">
                                        <input type="datetime-local" wire:model="categories.{{ $index }}.start_date"
                                               class="rounded-lg border-stone-300 text-sm py-1.5 px-3 focus:ring-brand-500 focus:border-brand-500">
                                    </td>
                                    <td class="px-4 py-3">
                                        <input type="datetime-local" wire:model="categories.{{ $index }}.end_date"
                                               class="rounded-lg border-stone-300 text-sm py-1.5 px-3 focus:ring-brand-500 focus:border-brand-500">
                                    </td>
                                    <td class="px-4 py-3">
                                        <input type="checkbox" wire:model="categories.{{ $index }}.is_active" value="1"
                                               class="rounded border-stone-300 text-brand-600 focus:ring-brand-500">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit"
                        class="px-6 py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold rounded-xl shadow-sm transition-colors">
                    Simpan Semua Diskon
                </button>
            </div>
        </form>

        <x-filament-actions::modals />
    </div>
</x-filament-panels::page>
