<x-layouts.admin title="Tambah Lokasi Baru">
    @if ($errors->any())
        <div class="toast toast-bottom toast-center z-50">
            <ul class="alert alert-error">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="container mx-auto p-10">
        <div class="card bg-base-100 shadow-sm">
            <div class="card-body">
                <h2 class="card-title text-2xl mb-6">Tambah Lokasi Baru</h2>

                <form class="space-y-4" method="POST" action="{{ route('admin.lokasi.store') }}">
                    @csrf

                    <!-- Nama Lokasi -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">Nama Lokasi</span>
                        </label>
                        <input type="text" name="nama_lokasi" class="input input-bordered w-full"
                            placeholder="Contoh: Stadion Utama" required>
                    </div>

                    <!-- Tombol -->
                    <div class="card-actions justify-end mt-6">
                        <button type="reset" class="btn btn-ghost">Reset</button>
                        <button type="submit" class="btn btn-primary">Simpan Lokasi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.admin>
