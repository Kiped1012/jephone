window.showDetail = function (items) {
    const tbody = document.getElementById('detail-content');
    const totalCell = document.getElementById('detail-total');
    tbody.innerHTML = '';

    let total = 0;

    items.forEach((item, index) => {
        const subtotal = item.harga * item.jumlah;
        total += subtotal;

        const row = document.createElement('tr');
        row.innerHTML = `
            <td class="px-3 py-2 border text-center">${index + 1}</td>
            <td class="px-3 py-2 border">${item.nama}</td>
            <td class="px-3 py-2 border text-center">${item.jumlah}</td>
            <td class="px-3 py-2 border text-right">Rp${item.harga.toLocaleString('id-ID')}</td>
            <td class="px-3 py-2 border text-right">Rp${subtotal.toLocaleString('id-ID')}</td>
        `;
        tbody.appendChild(row);
    });

    totalCell.innerText = `Rp${total.toLocaleString('id-ID')}`;
    document.getElementById('modal-detail').classList.remove('hidden');
    document.getElementById('modal-detail').classList.add('flex');
};

window.closeModal = function () {
    document.getElementById('modal-detail').classList.add('hidden');
    document.getElementById('modal-detail').classList.remove('flex');
};
