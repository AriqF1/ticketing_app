<x-layouts.admin title="Edit Lokasi">
    <div class="container mx-auto p-10">
        <div class="card bg-base-100 shadow-sm">
            <div class="card-body">
                <h2 class="card-title text-2xl mb-6">Edit Lokasi</h2>

                <form class="space-y-4" method="post" action="{{ route('admin.lokasi.update', $lokasi->id) }}">
                    @csrf
                    @method('PUT')

                    <!-- Nama Lokasi -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">Nama Lokasi</span>
                        </label>
                        <input type="text" name="nama_lokasi" placeholder="Contoh: Stadion Utama"
                            class="input input-bordered w-full" value="{{ old('nama_lokasi', $lokasi->nama_lokasi) }}"
                            required />
                        @error('nama_lokasi')
                            <span class="text-error text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Tombol Submit -->
                    <div class="card-actions justify-end mt-6">
                        <a href="{{ route('admin.lokasi.index') }}" class="btn btn-ghost">
                            Batal
                        </a>
                        <button type="submit" class="btn btn-primary">
                            Simpan Lokasi
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Alert Success -->
        @if (session('success'))
            <div class="alert alert-success mt-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif
    </div>
</x-layouts.admin>
