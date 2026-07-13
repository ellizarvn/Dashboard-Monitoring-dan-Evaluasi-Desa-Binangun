<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check() && Auth::user()->isSuperAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $userParam = $this->route('user');
        $userId = $userParam instanceof User ? $userParam->id : $userParam;

        return [
            'name'     => ['required', 'string', 'min:3', 'max:100'],
            'email'    => ['required', 'email', 'max:150', 'unique:users,email,' . $userId],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'role'     => ['required', 'in:super_admin,admin'],
            'jabatan'  => ['required_if:role,admin', 'nullable', 'in:Administrator,Kepala Desa,Tim Monitoring,BPD'],
            'phone'    => ['nullable', 'string', 'max:20'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required'       => 'Nama lengkap wajib diisi.',
            'name.min'            => 'Nama minimal terdiri dari 3 karakter.',
            'email.required'      => 'Email wajib diisi.',
            'email.email'         => 'Format email tidak valid.',
            'email.unique'        => 'Email ini sudah terdaftar oleh pengguna lain.',
            'password.min'        => 'Kata sandi baru minimal 8 karakter.',
            'password.confirmed'  => 'Konfirmasi kata sandi baru tidak cocok.',
            'role.required'       => 'Role wajib dipilih.',
            'jabatan.required_if' => 'Jabatan wajib diisi jika role adalah Admin.',
        ];
    }
}
