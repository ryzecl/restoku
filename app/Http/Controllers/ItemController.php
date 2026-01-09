<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Category;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $items = Item::orderBy('name', 'asc')->get();

        return view('admin.item.index', compact('items'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();

        return view('admin.item.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate(
            [
                'name' => 'required|string|max:255',
                'description' => 'required|string',
                'price' => 'required|numeric|min:0',
                'category_id' => 'required|exists:categories,id',
                'img' => 'nullable|image|mimes:jpeg,png,jpg,gif|mimetypes:image/jpeg,image/png,image/gif|max:2048',
                'is_active' => 'required|boolean',
            ],
            [
                'name.required' => 'Nama menu wajib diisi',
                'name.max' => 'Nama menu maksimal 255 karakter',
                'description.required' => 'Deskripsi wajib diisi',
                'price.required' => 'Harga wajib diisi',
                'price.min' => 'Harga minimal 0',
                'category_id.required' => 'Kategori wajib diisi',
                'category_id.exists' => 'Kategori tidak ditemukan',
                'img.image' => 'Gambar harus berupa gambar',
                'img.mimes' => 'Gambar harus berupa gambar dengan format jpeg, png, jpg, gif',
                'img.max' => 'Gambar maksimal 2048 KB',
                'is_active.required' => 'Status wajib diisi',
                'is_active.boolean' => 'Status harus berupa boolean',
            ]
        );

        if ($request->hasFile('img')) {
            $image = $request->file('img');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('img_item_upload'), $imageName);
            $validatedData['img'] = $imageName;
        };

        $item = Item::create($validatedData);

        return redirect()->route('items.index')->with('success', 'Menu berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $item = Item::findOrFail($id);
        $categories = Category::all();

        return view('admin.item.edit', compact('item', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validatedData = $request->validate(
            [
                'name' => 'required|string|max:255',
                'description' => 'required|string',
                'price' => 'required|numeric|min:0',
                'category_id' => 'required|exists:categories,id',
                'img' => 'sometimes|image|mimes:jpeg,png,jpg,gif|mimetypes:image/jpeg,image/png,image/gif|max:2048',
                'is_active' => 'required|boolean',
            ],
            [
                'name.required' => 'Nama menu wajib diisi',
                'name.max' => 'Nama menu maksimal 255 karakter',
                'description.required' => 'Deskripsi wajib diisi',
                'price.required' => 'Harga wajib diisi',
                'price.min' => 'Harga minimal 0',
                'category_id.required' => 'Kategori wajib diisi',
                'category_id.exists' => 'Kategori tidak ditemukan',
                'img.image' => 'Gambar harus berupa gambar',
                'img.mimes' => 'Gambar harus berupa gambar dengan format jpeg, png, jpg, gif',
                'img.max' => 'Gambar maksimal 2048 KB',
                'is_active.required' => 'Status wajib diisi',
                'is_active.boolean' => 'Status harus berupa boolean',
            ]
        );

        $item = Item::findOrFail($id);

        if ($request->hasFile('img')) {
            $image = $request->file('img');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('img_item_upload'), $imageName);
            $validatedData['img'] = $imageName;
        };

        $item->update($validatedData);

        return redirect()->route('items.index')->with('success', 'Menu berhasil diubah');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $item = Item::findOrFail($id);
        $item->delete();

        return redirect()->route('items.index')->with('success', 'Menu berhasil dihapus');
    }
}
