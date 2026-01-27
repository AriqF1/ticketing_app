<x-layouts.admin title="Manajemen Lokasi">
    @if (session('success'))
        <div class="toast toast-bottom toast-center">
            <div class="alert alert-success">
                <span>{{ session('success') }}</span>
            </div>
        </div>

        <script>
            setTimeout(() => {
                document.querySelector('.toast')?.remove()
            }, 3000)
        </script>
    @endif

    <div class="container mx-auto p-10">
        <div class="flex">
            <h1 class="text-3xl font-semibold mb-4">Manajemen Lokasi</h1>
            <a href="{{ route('admin.lokasi.create') }}" class="btn btn-primary ml-auto">
                Tambah Lokasi
            </a>
        </div>

        <div class="overflow-x-auto rounded-box bg-white p-5 shadow-xs">
            <table class="table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th class="w-1/3">Nama Lokasi</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($lokasis as $index => $lokasi)
                        <tr>
                            <th>{{ $index + 1 }}</th>
                            <td>{{ $lokasi->nama_lokasi }}</td>
                            <td>
                                <a href="{{ route('admin.lokasi.show', $lokasi->id) }}"
                                    class="btn btn-sm btn-info mr-2">Detail</a>

                                <a href="{{ route('admin.lokasi.edit', $lokasi->id) }}"
                                    class="btn btn-sm btn-primary mr-2">Edit</a>

                                <button class="btn btn-sm bg-red-500 text-white" onclick="openDeleteModal(this)"
                                    data-id="{{ $lokasi->id }}">
                                    Hapus
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center">
                                Tidak ada lokasi tersedia.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Delete Modal -->
    <dialog id="delete_modal" class="modal">
        <form method="POST" class="modal-box">
            @csrf
            @method('DELETE')

            <input type="hidden" id="delete_lokasi_id">

            <h3 class="text-lg font-bold mb-4">Hapus Lokasi</h3>
            <p>Apakah Anda yakin ingin menghapus lokasi ini?</p>

            <div class="modal-action">
                <button class="btn btn-primary" type="submit">Hapus</button>
                <button class="btn" onclick="delete_modal.close()" type="reset">Batal</button>
            </div>
        </form>
    </dialog>

    <script>
        function openDeleteModal(button) {
            const id = button.dataset.id
            const form = document.querySelector('#delete_modal form')

            form.action = `/admin/lokasis/${id}`
            delete_modal.showModal()
        }
    </script>
</x-layouts.admin>
