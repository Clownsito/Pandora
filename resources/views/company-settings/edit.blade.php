<x-app-layout>
    <x-slot name="header">
        <h2>Editar Usuario</h2>
    </x-slot>

    <div class="container py-4">

        @if($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.users.update', $user) }}">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $user->email) }}" required>
            </div>

            <div class="mb-3">
                <label for="name" class="form-label">Nombre (Opcional)</label>
                <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $user->name) }}">
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Nueva contraseña (dejar vacío para no cambiar)</label>
                <input type="password" name="password" id="password" class="form-control">
            </div>

            <button class="btn btn-primary" type="submit">Guardar</button>
            <a href="{{ route('admin.users.manage') }}" class="btn btn-secondary ms-2">Cancelar</a>
        </form>

    </div>
</x-app-layout>