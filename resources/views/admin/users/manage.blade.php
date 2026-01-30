<x-app-layout>
    <x-slot name="header">
        <h2>Gestión de Usuarios</h2>
    </x-slot>

    <div class="container py-4">

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>
        @endif

        {{-- Formulario Crear Usuario --}}
        <form method="POST" action="{{ route('admin.users.manage') }}">
            @csrf

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" id="email" class="form-control" name="email" value="{{ old('email') }}" required>
                @error('email')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="name" class="form-label">Nombre (Opcional)</label>
                <input type="text" id="name" class="form-control" name="name" value="{{ old('name') }}">
                @error('name')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Contraseña</label>
                <input type="password" id="password" class="form-control" name="password" required>
                @error('password')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary">Crear Usuario</button>
        </form>

        <hr>

        {{-- Tabla Usuarios --}}
        <table class="table table-bordered mt-4">
            <thead>
                <tr>
                    <th>Email</th>
                    <th>Nombre</th>
                    <th>Empresa</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                <tr>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->name ?? '-' }}</td>
                    <td>{{ $user->company_id ?? '-' }}</td>
                    <td>
                        <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editUserModal" onclick="loadUser({{ $user->id }})">Editar</button>
                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Seguro que quieres eliminar este usuario?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">Eliminar</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center">No hay usuarios registrados.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Modal edición --}}
    <div class="modal fade" id="editUserModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="editUserForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="editUserId" name="user_id">
                    <div class="modal-header">
                        <h5 class="modal-title">Editar Usuario</h5>
                        <button type="button" class="btn btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        <label for="editEmail" class="form-label">Email</label>
                        <input type="email" id="editEmail" name="email" class="form-control" required>
                        <label for="editName" class="form-label mt-3">Nombre (Opcional)</label>
                        <input type="text" id="editName" name="name" class="form-control">
                        <label for="editPassword" class="form-label mt-3">Nueva Contraseña (dejar vacío para no cambiar)</label>
                        <input type="password" id="editPassword" name="password" class="form-control">
                        <div id="editUserErrors" class="text-danger mt-2" style="display:none;"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

<script>
function loadUser(id) {
    fetch(`/admin/users/${id}/edit`)
        .then(res => res.json())
        .then(data => {
            document.getElementById('editUserId').value = data.id;
            document.getElementById('editEmail').value = data.email;
            document.getElementById('editName').value = data.name || '';
            document.getElementById('editPassword').value = '';
            document.getElementById('editUserErrors').innerHTML = '';
            document.getElementById('editUserErrors').style.display = 'none';
        });
}

document.getElementById('editUserForm').addEventListener('submit', function(e) {
    e.preventDefault();

    let userId = document.getElementById('editUserId').value;

    const formData = new FormData(this);
    formData.append('_method', 'PUT');

    fetch(`/admin/users/${userId}`, {
        method: 'POST', // Laravel requiere POST con _method=PUT
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
        },
        body: formData,
    })
    .then(res => res.json())
    .then(data => {
        if(data.errors) {
            let errorsHTML = '<ul>';
            for(const [field, messages] of Object.entries(data.errors)) {
                messages.forEach(msg => errorsHTML += `<li>${msg}</li>`);
            }
            errorsHTML += '</ul>';
            document.getElementById('editUserErrors').innerHTML = errorsHTML;
            document.getElementById('editUserErrors').style.display = 'block';
        } else if(data.success) {
            let editModal = bootstrap.Modal.getInstance(document.getElementById('editUserModal'));
            editModal.hide();
            // Recarga o actualiza la tabla usuarios
            window.location.reload(); // opción simple para refrescar
        }
    })
    .catch(() => {
        document.getElementById('editUserErrors').innerText = 'Error al actualizar usuario.';
        document.getElementById('editUserErrors').style.display = 'block';
    });
});
</script>
</x-app-layout>