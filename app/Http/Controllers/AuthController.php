<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class AuthController extends Controller
{
    // Method untuk menampilkan form login
    public function showLogin()
    {
        return view('login');
    }

    // Method untuk menampilkan form forgot password
    public function showForgotPassword()
    {
        return view('forgotpassword');
    }

    // Method untuk menampilkan form reset password
    public function showResetPassword($token)
    {
        // Cek apakah token valid
        $resetTokens = $this->getResetTokens();
        $tokenData = null;
        
        foreach ($resetTokens as $reset) {
            if ($reset['token'] === $token && strtotime($reset['expires_at']) > time()) {
                $tokenData = $reset;
                break;
            }
        }

        if (!$tokenData) {
            return redirect()->route('forgot.password')->with('error', 'Token reset password tidak valid atau sudah kadaluarsa.');
        }

        return view('resetpassword', [
            'token' => $token,
            'email' => $tokenData['email']
        ]);
    }

    // Method untuk proses login
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $users = $this->getUsers();
        
        foreach ($users as $user) {
            if ($user['username'] === $request->username && Hash::check($request->password, $user['password'])) {
                // Login berhasil
                session([
                    'user_id' => $user['id_usr'],
                    'username' => $user['username'],
                    'role' => $user['role'],
                    'email' => $user['email']
                ]);
                
                // Redirect berdasarkan role
                if ($user['role'] === 'Admin') {
                    return redirect()->route('dashboard')->with('success', 'Login berhasil!');
                } elseif ($user['role'] === 'Kasir') {
                    return redirect()->route('penjualan.index')->with('success', 'Login berhasil!');
                } else {
                    // Default redirect jika role tidak dikenali
                    return redirect()->route('dashboard')->with('success', 'Login berhasil!');
                }
            }
        }

        return back()->with('error', 'Username atau password salah.');
    }

    // Method untuk proses forgot password
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $users = $this->getUsers();
        $userFound = false;

        foreach ($users as $user) {
            if ($user['email'] === $request->email) {
                $userFound = true;
                break;
            }
        }

        if (!$userFound) {
            return back()->with('error', 'Email tidak ditemukan dalam sistem.');
        }

        // Generate token reset password
        $token = Str::random(60);
        $expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour')); // Token berlaku 1 jam

        // Simpan token reset
        $this->saveResetToken($request->email, $token, $expiresAt);

        // Kirim email reset password
        $this->sendResetEmail($request->email, $token);

        return back()->with('success', 'Link reset password telah dikirim ke email Anda.');
    }

    // Method untuk proses reset password
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        // Cek token
        $resetTokens = $this->getResetTokens();
        $validToken = false;

        foreach ($resetTokens as $index => $reset) {
            if ($reset['token'] === $request->token && 
                $reset['email'] === $request->email && 
                strtotime($reset['expires_at']) > time()) {
                $validToken = true;
                // Hapus token setelah digunakan
                $this->removeResetToken($index);
                break;
            }
        }

        if (!$validToken) {
            return back()->with('error', 'Token reset password tidak valid atau sudah kadaluarsa.');
        }

        // Update password user
        $this->updateUserPassword($request->email, $request->password);

        return redirect()->route('login')->with('success', 'Password berhasil direset. Silakan login dengan password baru.');
    }

    // Method untuk logout
    public function logout()
    {
        session()->flush();
        return redirect()->route('login')->with('success', 'Logout berhasil.');
    }

    // Helper methods
    private function getUsers()
    {
        $usersFile = resource_path('data/user.php');
        if (file_exists($usersFile)) {
            return include $usersFile;
        }
        return [];
    }

    private function saveUsers($users)
    {
        $usersFile = resource_path('data/user.php');
        $content = "<?php\n\nreturn " . var_export($users, true) . ";\n";
        file_put_contents($usersFile, $content);
    }

    private function getResetTokens()
    {
        $tokensFile = resource_path('data/reset_tokens.php');
        if (file_exists($tokensFile)) {
            return include $tokensFile;
        }
        return [];
    }

    private function saveResetTokens($tokens)
    {
        $tokensFile = resource_path('data/reset_tokens.php');
        $content = "<?php\n\nreturn " . var_export($tokens, true) . ";\n";
        file_put_contents($tokensFile, $content);
    }

    private function saveResetToken($email, $token, $expiresAt)
    {
        $tokens = $this->getResetTokens();
        
        // Hapus token lama untuk email yang sama
        $tokens = array_filter($tokens, function($reset) use ($email) {
            return $reset['email'] !== $email;
        });

        // Tambah token baru
        $tokens[] = [
            'email' => $email,
            'token' => $token,
            'expires_at' => $expiresAt,
            'created_at' => date('Y-m-d H:i:s')
        ];

        $this->saveResetTokens($tokens);
    }

    private function removeResetToken($index)
    {
        $tokens = $this->getResetTokens();
        unset($tokens[$index]);
        $tokens = array_values($tokens); // Re-index array
        $this->saveResetTokens($tokens);
    }

    private function updateUserPassword($email, $newPassword)
    {
        $users = $this->getUsers();
        
        foreach ($users as &$user) {
            if ($user['email'] === $email) {
                $user['password'] = Hash::make($newPassword);
                break;
            }
        }

        $this->saveUsers($users);
    }

    private function sendResetEmail($email, $token)
    {
        $resetUrl = route('reset.password', ['token' => $token]);
        
        $data = [
            'email' => $email,
            'resetUrl' => $resetUrl,
            'token' => $token
        ];

        Mail::send('reset-password', $data, function ($message) use ($email) {
            $message->to($email)
                    ->subject('Reset Password - JEPhone');
        });
    }
}