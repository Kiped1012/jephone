<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    protected $filePath = 'data/user.php';

    public function index()
    {
        $users = collect(include resource_path($this->filePath));
        $title = 'Manajemen User';

        return view('manageuser', compact('users', 'title'));
    }

    public function create()
    {
       return view('entriuser', [
        'title' => 'Entri User'
        ]); 
    }

    public function store(Request $request)
    {
        $data = include(resource_path($this->filePath));

        $validated = $request->validate([
            'id_usr' => 'required',
            'username' => 'required',
            'email' => 'required|email',
            'role' => 'required|in:Admin,Kasir',
            'password' => 'required|min:4',
        ]);

        // Tambah user baru
        $data[] = [
            'id_usr' => $validated['id_usr'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'password' => bcrypt($validated['password']),
        ];

        file_put_contents(resource_path($this->filePath), '<?php return ' . var_export($data, true) . ';');

        return redirect()->route('user.index')->with('success', 'User berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $data = include(resource_path($this->filePath));
        $user = collect($data)->firstWhere('id_usr', $id);

        if (!$user) {
            abort(404);
        }

        // Pass both user data and isEdit flag
        $isEdit = true;
        return view('entriuser', compact('user', 'id', 'isEdit'));
    }

    public function update(Request $request, $id)
    {
        $data = include(resource_path($this->filePath));
        $index = collect($data)->search(fn($item) => $item['id_usr'] === $id);

        if ($index === false) {
            abort(404);
        }

        $validated = $request->validate([
            'username' => 'required',
            'email' => 'required|email',
            'role' => 'required|in:Admin,Kasir',
            'password' => 'nullable|min:4',
        ]);

        $data[$index]['username'] = $validated['username'];
        $data[$index]['email'] = $validated['email'];
        $data[$index]['role'] = $validated['role'];

        if (!empty($validated['password'])) {
            $data[$index]['password'] = bcrypt($validated['password']);
        }

        file_put_contents(resource_path($this->filePath), '<?php return ' . var_export($data, true) . ';');

        return redirect()->route('user.index')->with('success', 'User berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $data = include(resource_path($this->filePath));
        $filtered = array_values(array_filter($data, fn($item) => $item['id_usr'] !== $id));

        file_put_contents(resource_path($this->filePath), '<?php return ' . var_export($filtered, true) . ';');

        return redirect()->route('user.index')->with('success', 'User berhasil dihapus!');
    }
}