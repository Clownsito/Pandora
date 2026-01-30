<x-app-layout>
    <x-slot name="header">
        <h2>Lista de Usuarios</h2>
    </x-slot>

    <div class="container py-4">

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>
        @endif

        <a href="{{ route('admin.users.create') }}" class="btn btn-primary mb-3">Crear Usuario</a>

        <table class="table table-bordered table-striped">
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
                            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-warning">Editar</a>
                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Seguro quiere eliminar este usuario?');">
                                @csrf
                                @method('DELETE')
                                <button onclick="return confirm('Confirmar eliminación?')" class="btn btn-sm btn-danger" type="submit">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @empty
                <tr><td colspan="4" class="text-center">No hay usuarios.</td></tr>
                @endforelse
            </tbody>
        </table>

    </div>
</x-app-layout>
