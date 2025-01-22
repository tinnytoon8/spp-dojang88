<x-filament-panels::page>
    <x-filament::section>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- nama, nomor telepon, email --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- nama --}}
                <div>
                    <label class="block text-gray-400 font-medium mb-1">Nama:</label>
                    <p class="text-gray-900 font-semibold">{{ $user->name }}</p>
                </div>
                {{-- nomor telepon --}}
                <div>
                    <label class="block text-gray-400 font-medium mb-1">Nomor Telepon:</label>
                    <p class="text-gray-900 font-semibold">{{ $user->phone ?? 'Belum Diisi' }}</p>
                </div>
                {{-- Email --}}
                <div class="col-span-1 md:col-span-2">
                    <label class="block text-gray-400 font-medium mb-1">Email:</label>
                    <p class="text-gray-900 font-semibold">{{ $user->email }}</p>
                </div>
            </div>
        </div>

        {{-- image & certificate on the ride side --}}
        <div class="grid grid-cols-2 gap-6">
            <div>
                <label class="block text-gray-400 font-medium mb-1">Foto:</label>
                <img src="{{ $user->image ? asset('storage/' . $user->image) : asset('images/default-user.png') }}"
                    alt="Gambar" class="w-32 h-auto object-cover rounded-md border cursor-pointer"
                    x-on:click="$dispatch('open-modal', { id: 'image-modal', image: '{{ $user->image ? asset('storage/' . $user->image) : asset('images/default-user.png') }}' })">
            </div>

            <div>
                <label class="block text-gray-400 font-medium mb-1">Upload Sertifikat Terakhir:</label>
                <img src="{{ $user->certificate ? asset('storage/' . $user->certificate) : asset('images/default-certificate.png') }}"
                    alt="certificate" class="w-32 h-auto object-cover rounded-md border cursor-pointer"
                    x-on:click="$dispatch('open-modal', { id: 'image-modal', image: '{{ $user->certificate ? asset('storage/' . $user->certificate) : asset('images/default-certificate.png') }}' })">
            </div>
        </div>
    </x-filament::section>

    <x-filament::section class="mt-3">
        <form wire:submit.prevent="edit" class="space-y-4">
            {{ $this->form }}

            <x-filament::button type="submit" color="primary">
                Edit
            </x-filament::button>
        </form>
    </x-filament::section>

    <!-- Modal Preview -->
    <x-filament::modal id="image-modal">
        <div class="p-4 flex items-center justify-center overflow-hidden max-h-screen" x-data="{ image: '', scale: 1, isDragging: false, startX: 0, startY: 0, offsetX: 0, offsetY: 0 }"
            x-on:open-modal.window="if ($event.detail.id === 'image-modal') { image = $event.detail.image; }"
            x-on:close-modal.window="scale = 1; offsetX = 0; offsetY = 0;">
            <div class="relative max-w-full max-h-screen overflow-auto" style="touch-action: none;">
                <!-- Image Container with Dragging -->
                <img x-bind:src="image" alt="Preview" class="rounded-md object-contain cursor-grab"
                    :style="'transform: scale(' + scale + ') translate(' + offsetX + 'px, ' + offsetY + 'px);'"
                    x-on:mousedown="isDragging = true; startX = $event.clientX; startY = $event.clientY; $event.preventDefault();"
                    x-on:mousemove="if (isDragging) { offsetX += $event.clientX - startX; offsetY += $event.clientY - startY; startX = $event.clientX; startY = $event.clientY; }"
                    x-on:mouseup="isDragging = false;"
                    x-on:mouseleave="isDragging = false;"
                    x-on:wheel="scale = Math.max(1, Math.min(scale + $event.deltaY * -0.002, 3))">
            </div>
        </div>
    </x-filament::modal>
        
</x-filament-panels::page>
