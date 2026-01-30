<x-app-layout>
    <x-slot name="header">
        <h2>Crear Usuario</h2>
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

        <form method="POST" action="{{ route('admin.users.store') }}">
            @csrf

            <div class="mb-3">
                <label>Email</label>
                <input type="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus>
            </div>

            <div class="mb-3">
                <label>Nombre (Opcional)</label>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}">
            </div>

            <div class="mb-3">
                <label>Contraseña</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <button class="btn btn-primary" type="submit">Crear</button>
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary ms-2">Cancelar</a>
        </form>

    </div>
</x-app-layout>

