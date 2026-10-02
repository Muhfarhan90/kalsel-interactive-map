<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PublicPageHeader;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PublicPageHeaderController extends Controller
{
    public function edit(): View
    {
        return view('pages.admin.headers.edit', [
            'header' => $this->sharedHeader(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'header_title' => ['required', 'string', 'max:255'],
            'header_logo_text' => ['nullable', 'string', 'max:255'],
            'header_logo_file' => ['nullable', 'image:allow_svg', 'mimes:jpg,jpeg,png,webp,svg', 'max:2048'],
        ]);

        $header = $this->sharedHeader();
        if ($request->hasFile('header_logo_file')) {
            $data['header_logo'] = $this->storeImage(
                $request->file('header_logo_file'),
                'header-global',
                $header->header_logo,
            );
        }
        unset($data['header_logo_file']);
        $header->update($data);

        return back()->with('success', 'Pengaturan header berhasil diperbarui.');
    }

    private function sharedHeader(): PublicPageHeader
    {
        return PublicPageHeader::where('page_key', 'home')->firstOrFail();
    }

    private function storeImage(UploadedFile $file, string $name, ?string $currentPath): string
    {
        $path = 'headers/'.$name.'.'.strtolower($file->extension());
        if ($currentPath && str_starts_with($currentPath, 'storage/') && $currentPath !== 'storage/'.$path) {
            Storage::disk('public')->delete(substr($currentPath, 8));
        }
        $file->storeAs('headers', basename($path), 'public');

        return 'storage/'.$path;
    }
}
