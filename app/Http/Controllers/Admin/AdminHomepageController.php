<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Homepage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AdminHomepageController extends Controller
{
    public function edit(): View
    {
        return view('pages.admin.homepage.edit', [
            'homepage' => Homepage::query()->firstOrFail(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'header_title' => ['required', 'string', 'max:255'],
            'header_logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'header_text' => ['nullable', 'string', 'max:255'],
            'color' => ['nullable', 'string', 'max:7', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'title' => ['required', 'string', 'max:255'],
            'label' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $homepage = Homepage::query()->firstOrFail();
        $previousImage = $homepage->image;
        $previousHeaderLogo = $homepage->header_logo;

        if ($request->hasFile('image')) {
            $data['image'] = 'storage/' . $request->file('image')
                ->store('homepages/backgrounds', 'public');
        } else {
            unset($data['image']);
        }

        if ($request->hasFile('header_logo')) {
            $data['header_logo'] = 'storage/' . $request->file('header_logo')
                ->store('homepages/logos', 'public');
        } else {
            unset($data['header_logo']);
        }

        $homepage->update($data);

        if (
            $previousImage !== $homepage->image
            && $previousImage
            && str_starts_with($previousImage, 'storage/homepages/backgrounds/')
        ) {
            Storage::disk('public')->delete(substr($previousImage, 8));
        }

        if (
            $previousHeaderLogo !== $homepage->header_logo
            && $previousHeaderLogo
            && str_starts_with($previousHeaderLogo, 'storage/homepages/logos/')
        ) {
            Storage::disk('public')->delete(substr($previousHeaderLogo, 8));
        }

        return back()->with('success', 'Pengaturan homepage berhasil diperbarui.');
    }
}
