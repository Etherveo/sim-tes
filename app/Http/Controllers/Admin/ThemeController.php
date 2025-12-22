<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Theme;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ThemeController extends Controller
{
    public function index()
    {
        $themes = Theme::latest()->get();
        return view('admin.themes.index', compact('themes'));
    }

    public function create()
    {
        return view('admin.themes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'           => 'required|string|max:255',
            'site_logo'      => 'required|image|mimes:png,jpg,jpeg|max:5120',
            'login_image'    => 'required|image|mimes:png,jpg,jpeg|max:5120',
            'register_image' => 'required|image|mimes:png,jpg,jpeg|max:5120',
        ]);

        $data = ['name' => $request->name, 'is_active' => false];

        if ($request->hasFile('site_logo')) {
            $data['site_logo'] = $request->file('site_logo')->store('themes', 'public');
        }
        if ($request->hasFile('login_image')) {
            $data['login_image'] = $request->file('login_image')->store('themes', 'public');
        }
        if ($request->hasFile('register_image')) {
            $data['register_image'] = $request->file('register_image')->store('themes', 'public');
        }

        Theme::create($data);

        return redirect()->route('admin.themes.index')->with('success', 'Tema baru berhasil ditambahkan!');
    }

    public function edit(Theme $theme)
    {
        return view('admin.themes.edit', compact('theme'));
    }

    public function update(Request $request, Theme $theme)
    {
        $request->validate([
            'name'           => 'required|string|max:255',
            'site_logo'      => 'nullable|image|mimes:png,jpg,jpeg|max:5120',
            'login_image'    => 'nullable|image|mimes:png,jpg,jpeg|max:5120',
            'register_image' => 'nullable|image|mimes:png,jpg,jpeg|max:5120',
        ]);

        $theme->name = $request->name;

        if ($request->hasFile('site_logo')) {
            if ($theme->site_logo) Storage::disk('public')->delete($theme->site_logo);
            $theme->site_logo = $request->file('site_logo')->store('themes', 'public');
        }

        if ($request->hasFile('login_image')) {
            if ($theme->login_image) Storage::disk('public')->delete($theme->login_image);
            $theme->login_image = $request->file('login_image')->store('themes', 'public');
        }

        if ($request->hasFile('register_image')) {
            if ($theme->register_image) Storage::disk('public')->delete($theme->register_image);
            $theme->register_image = $request->file('register_image')->store('themes', 'public');
        }

        $theme->save();

        return redirect()->route('admin.themes.index')->with('success', 'Tema berhasil diperbarui!');
    }

    public function destroy(Theme $theme)
    {
        if ($theme->is_active) {
            return back()->with('error', 'Tidak bisa menghapus tema yang sedang aktif!');
        }

        if ($theme->site_logo) Storage::disk('public')->delete($theme->site_logo);
        if ($theme->login_image) Storage::disk('public')->delete($theme->login_image);
        if ($theme->register_image) Storage::disk('public')->delete($theme->register_image);

        $theme->delete();

        return back()->with('success', 'Tema berhasil dihapus!');
    }

    public function activate(Theme $theme)
    {
        Theme::query()->update(['is_active' => false]);

        $theme->update(['is_active' => true]);

        return back()->with('success', "Tema '{$theme->name}' berhasil diaktifkan!");
    }

    public function deactivate(Theme $theme)
    {
        $theme->update(['is_active' => false]);
        return back()->with('success', 'Tema dinonaktifkan. Website kembali ke tampilan bawaan.');
    }
}