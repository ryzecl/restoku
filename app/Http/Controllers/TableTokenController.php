<?php

namespace App\Http\Controllers;

use App\Models\TableToken;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TableTokenController extends Controller
{
    /**
     * Show all tokens (active & expired).
     */
    public function index()
    {
        $tokens = TableToken::orderByDesc('created_at')->get();
        return view('admin.table-token.manage', compact('tokens'));
    }

    /**
     * Generate a new token for a table.
     */
    public function generate(Request $request)
    {
        $request->validate([
            'table_number' => 'required|integer|min:1',
            'duration' => 'required|integer|min:1|max:72', // hours
        ]);

        // Deactivate existing active tokens for this table
        TableToken::where('table_number', $request->table_number)
            ->where('is_active', true)
            ->update(['is_active' => false]);

        $token = TableToken::create([
            'table_number' => $request->table_number,
            'token' => Str::random(32),
            'expires_at' => now()->addHours((int) $request->duration),
            'is_active' => true,
        ]);

        return redirect()->route('table-tokens.index')
            ->with('success', "Token untuk Meja {$request->table_number} berhasil dibuat!");
    }

    /**
     * Revoke (deactivate) a token.
     */
    public function revoke($id)
    {
        $token = TableToken::findOrFail($id);
        $token->update(['is_active' => false]);

        return redirect()->route('table-tokens.index')
            ->with('success', "Token Meja {$token->table_number} berhasil dinonaktifkan.");
    }

    /**
     * Hapus token permanen.
     */
    public function destroy($id)
    {
        TableToken::findOrFail($id)->delete();

        return redirect()->route('table-tokens.index')
            ->with('success', "Token berhasil dihapus permanen.");
    }

    /**
     * Bersihkan semua token yang hangus/expired.
     */
    public function cleanup()
    {
        $count = TableToken::where('is_active', false)
            ->orWhere('expires_at', '<', now())
            ->delete();

        return redirect()->route('table-tokens.index')
            ->with('success', "Berhasil membersihkan $count token yang sudah kadaluarsa.");
    }
}
