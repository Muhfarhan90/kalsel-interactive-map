@extends('layouts.app')

@php($editing = $user->exists)

@section('content')
    <div class="mx-auto max-w-4xl space-y-6">
        <div>
            <a href="{{ route('admin.users.index') }}" class="text-sm font-semibold text-[#da251d]">← Kembali ke pengguna</a>
            <h1 class="mt-3 text-2xl font-bold text-gray-900 dark:text-white">{{ $editing ? 'Edit pengguna' : 'Tambah pengguna' }}</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Admin dapat mengelola pengguna; operator dapat mengelola data wisata dan kategori.</p>
        </div>

        @include('components.common.flash-message')

        <form action="{{ $editing ? route('admin.users.update', $user) : route('admin.users.store') }}" method="POST">
            @csrf
            @if ($editing)
                @method('PUT')
            @endif

            <x-common.component-card title="Informasi pengguna" desc="Username dipakai untuk login; email tetap disimpan sebagai informasi akun.">
                <div class="grid gap-5 md:grid-cols-2">
                    <div class="md:col-span-2">
                        <x-form.form-elements.default-inputs label="Nama" name="name" :value="$user->name" required maxlength="255" autocomplete="name" />
                    </div>
                    <x-form.form-elements.default-inputs label="Username" name="username" :value="$user->username" required maxlength="255" autocomplete="username" />
                    <x-form.form-elements.default-inputs label="Email" name="email" type="email" :value="$user->email" required maxlength="255" autocomplete="email" />
                    <x-form.form-elements.select-inputs label="Role" name="role" required>
                        <option value="operator" @selected(old('role', $user->role ?? 'operator') === 'operator')>Operator</option>
                        <option value="admin" @selected(old('role', $user->role) === 'admin')>Admin</option>
                    </x-form.form-elements.select-inputs>
                    <x-form.form-elements.default-inputs label="Password" name="password" type="password" :required="!$editing" minlength="8" autocomplete="new-password" :help="$editing ? 'Kosongkan jika tidak ingin mengubah password.' : 'Minimal 8 karakter.'" />
                    <x-form.form-elements.default-inputs label="Konfirmasi password" name="password_confirmation" type="password" :required="!$editing" minlength="8" autocomplete="new-password" />
                </div>

                <div class="flex flex-col-reverse gap-3 border-t border-gray-100 pt-5 sm:flex-row sm:justify-end dark:border-gray-800">
                    <a href="{{ route('admin.users.index') }}" class="inline-flex min-h-11 items-center justify-center rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-semibold text-gray-700 dark:border-gray-700 dark:text-gray-300">Batal</a>
                    <button type="submit" class="inline-flex min-h-11 items-center justify-center rounded-lg bg-[#da251d] px-5 py-2.5 text-sm font-semibold text-white shadow-theme-xs focus:outline-none focus:ring-4 focus:ring-red-100">
                        {{ $editing ? 'Simpan perubahan' : 'Tambah pengguna' }}
                    </button>
                </div>
            </x-common.component-card>
        </form>
    </div>
@endsection
