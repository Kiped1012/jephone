import './bootstrap';

document.addEventListener('DOMContentLoaded', () => {
    const passwordInput = document.getElementById('password');
    const togglePasswordButton = document.getElementById('togglePassword');

    if (passwordInput && togglePasswordButton) {
        togglePasswordButton.addEventListener('click', () => {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);

            // Toggle warna hijau
            if (type === 'text') {
                togglePasswordButton.classList.remove('text-gray-400');
                togglePasswordButton.classList.add('text-green-500');
            } else {
                togglePasswordButton.classList.remove('text-green-500');
                togglePasswordButton.classList.add('text-gray-400');
            }
        });
    }
});

