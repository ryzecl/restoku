<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateItemRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'img' => 'sometimes|image|mimes:jpeg,png,jpg,gif|mimetypes:image/jpeg,image/png,image/gif|max:2048',
            'is_active' => 'required|boolean',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
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
        ];
    }
}
