document.addEventListener('DOMContentLoaded', () => {
    const btnSimpan = document.getElementById('btnSimpan');
    const idUserInput = document.getElementById('idUser');

    btnSimpan.addEventListener('click', function (e) {
        const username = document.getElementById('username').value.trim();
        const email = document.getElementById('email').value.trim();
        const role = document.getElementById('role').value.trim();

        // Validasi field wajib (username, email, role)
        if (!username || !email || !role) {
            e.preventDefault();
            return window.dispatchEvent(new CustomEvent('show-error', {
                detail: 'Lengkapi semua field terlebih dahulu!'
            }));
        }

        // Validasi password hanya untuk mode create (bukan edit)
        if (!window.isEditMode) {
            const password = document.getElementById('password').value.trim();
            const confirmPassword = document.getElementById('password_confirmation').value.trim();
            
            if (!password || !confirmPassword) {
                e.preventDefault();
                return window.dispatchEvent(new CustomEvent('show-error', {
                    detail: 'Password dan konfirmasi password wajib diisi!'
                }));
            }
            
            if (password !== confirmPassword) {
                e.preventDefault();
                return window.dispatchEvent(new CustomEvent('show-error', {
                    detail: 'Password dan konfirmasi tidak cocok!'
                }));
            }

            // Generate ID otomatis jika create
            let newId = '';
            const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            let isUnique = false;

            while (!isUnique) {
                const randomPart = Array.from({ length: 4 }, () =>
                    chars.charAt(Math.floor(Math.random() * chars.length))
                ).join('');
                newId = 'USR_' + randomPart;

                isUnique = !window.dataUserTersimpan?.some(u => u.id_usr === newId);
            }

            idUserInput.value = newId;
        }
        
        // Untuk mode edit, pastikan ID user sudah ter-set dari data yang ada
        if (window.isEditMode && window.editUser && window.editUser.id_usr) {
            idUserInput.value = window.editUser.id_usr;
        }
    });
});