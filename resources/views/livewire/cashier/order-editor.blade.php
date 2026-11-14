<div>
    <flux:modal wire:model="showModal" class="max-w-md">
        <form wire:submit="save" class="space-y-4">
            <div>
                <flux:heading size="lg">Edit Pesanan #{{ $orderId }}</flux:heading>
                <flux:subheading>Perbarui catatan pesanan atau ubah status antrean.</flux:subheading>
            </div>

            <flux:input wire:model="consumerName" label="Nama Konsumen" required />

            <flux:textarea wire:model="notes" label="Catatan Pesanan" rows="3" placeholder="Tambahkan catatan khusus untuk dapur/barista..." />

            <flux:select wire:model="status" label="Status Pesanan">
                <option value="processing">Processing</option>
                <option value="ready">Ready</option>
                <option value="completed">Completed</option>
                <option value="cancelled">Cancelled</option>
            </flux:select>

            <div class="flex justify-end gap-2 pt-2">
                <flux:button type="button" variant="ghost" wire:click="$set('showModal', false)">Batal</flux:button>
                <flux:button type="submit" variant="primary">Simpan Perubahan</flux:button>
            </div>
        </form>
    </flux:modal>
</div>