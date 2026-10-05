<?php
namespace App\Http\Controllers;
use App\Models\{Nasabah, User};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, DB, Hash};

class AuthController extends Controller
{
    public function login(Request $r)
    {
        $c = $r->validate(['email' => 'required|email', 'password' => 'required']);
        if (!Auth::attempt($c)) {
            return back()->withErrors(['email' => 'Email atau kata sandi salah.'])->onlyInput('email');
        }
        $r->session()->regenerate();
        return redirect()->route('dashboard'); // peran dikenali dari akun
    }

    public function register(Request $r) // pendaftaran publik hanya untuk nasabah
    {
        $d = $r->validate([
            'name' => 'required|string|max:100', 'email' => 'required|email|unique:users',
            'password' => 'required|min:6', 'nomor_telepon' => 'required|max:20', 'alamat' => 'required|max:255',
        ]);
        $u = DB::transaction(function () use ($d) {
            $u = User::forceCreate(['name' => $d['name'], 'email' => $d['email'], 'password' => Hash::make($d['password']), 'role' => 'nasabah']);
            Nasabah::create(['id_user' => $u->id, 'alamat' => $d['alamat'], 'nomor_telepon' => $d['nomor_telepon']]);
            return $u;
        });
        Auth::login($u);
        $r->session()->regenerate();
        return redirect()->route('dashboard');
    }

    public function logout(Request $r)
    {
        Auth::logout();
        $r->session()->invalidate();
        $r->session()->regenerateToken();
        return redirect()->route('login');
    }
}
