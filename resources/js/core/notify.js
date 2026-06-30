const el = () => document.getElementById('notify');

function show(message, type = 'success') {
    const box = el();
    if (!box) return;

    box.textContent = message;
    box.className = type === 'success'
        ? 'mb-4 p-4 rounded text-sm font-medium bg-green-100 text-green-800'
        : 'mb-4 p-4 rounded text-sm font-medium bg-red-100 text-red-800';

    box.classList.remove('hidden');
    box.scrollIntoView({behavior: 'smooth', block: 'nearest'});

    if (type === 'success') {
        setTimeout(() => box.classList.add('hidden'), 4000);
    }
}

export const notify = {
    success: (msg) => show(msg, 'success'),
    error: (msg) => show(msg, 'error'),
};
