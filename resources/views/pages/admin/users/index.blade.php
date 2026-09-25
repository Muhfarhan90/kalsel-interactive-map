@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-medium text-[#da251d]">Akses admin</p>
                <h1 class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">Pengguna</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Kelola akun admin dan operator yang dapat mengakses dashboard.</p>
            </div>
            <a href="{{ route('admin.users.create') }}" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-lg bg-[#da251d] px-4 py-2.5 text-sm font-semibold text-white shadow-theme-xs focus:outline-none focus:ring-4 focus:ring-red-100">
                <span class="text-lg leading-none">+</span> Tambah pengguna
            </a>
        </div>

        @include('components.common.flash-message')

        <x-tables.table :paginator="$users">
            <thead class="bg-gray-50 dark:bg-gray-800/50">
                <tr class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                    <th class="px-5 py-3.5 text-left">Nama</th>
                    <th class="px-5 py-3.5 text-left">Username</th>
                    <th class="px-5 py-3.5 text-left">Email</th>
                    <th class="px-5 py-3.5 text-left">Role</th>
                    <th class="px-5 py-3.5 text-left">Dibuat</th>
                    <th class="px-5 py-3.5 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                @forelse ($users as $user)
                    <tr class="text-sm text-gray-700 dark:text-gray-300">
                        <td class="px-5 py-4">
                            <p class="font-semibold text-gray-900 dark:text-white">{{ $user->name }} @if ($user->is(auth()->user())) <span class="text-xs font-normal text-gray-500">(Anda)</span> @endif</p>
                        </td>
                        <td class="px-5 py-4 text-gray-500">{{ $user->username }}</td>
                        <td class="px-5 py-4 text-gray-500">{{ $user->email }}</td>
                        <td class="px-5 py-4">
                            <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $user->role === 'admin' ? 'bg-red-50 text-[#da251d] dark:bg-red-500/10' : 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300' }}">
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>
                        <td class="px-5 py-4 text-gray-500">{{ $user->created_at?->format('d M Y') }}</td>
                        <td class="px-5 py-4">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('admin.users.edit', $user) }}" class="inline-flex min-h-10 items-center rounded-lg border border-gray-300 px-3 py-2 text-xs font-semibold text-gray-700 dark:border-gray-700 dark:text-gray-300">Edit</a>
                                @unless ($user->is(auth()->user()))
                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('Hapus pengguna ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex min-h-10 items-center rounded-lg border border-error-200 px-3 py-2 text-xs font-semibold text-error-600 dark:border-error-500/30">Hapus</button>
                                    </form>
                                @endunless
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-14 text-center text-sm text-gray-500">Belum ada pengguna.</td>
                    </tr>
                @endforelse
            </tbody>
        </x-tables.table>
    </div>
@endsection
