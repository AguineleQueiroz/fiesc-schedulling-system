const csrf = () =>
    document.querySelector('meta[name="csrf-token"]')?.content ?? '';

async function request(method, url, data = null, params = {}) {
    const urlObj = new URL(url, window.location.origin);
    Object.entries(params).forEach(([k, v]) => urlObj.searchParams.set(k, v));

    const init = {
        method,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': csrf(),
            Accept: 'application/json',
        },
    };

    if (data !== null) {
        init.headers['Content-Type'] = 'application/json';
        init.body = JSON.stringify(data);
    }

    const response = await fetch(urlObj.toString(), init);

    if (response.status === 204) return null;

    const payload = await response.json().catch(() => ({message: 'Erro inesperado.'}));

    if (!response.ok || response.redirected) {
        const err = new Error(payload.message || 'Requisição falhou.');
        err.status = response.status;
        err.errors = payload.errors ?? {};
        throw err;
    }

    return payload;
}

export const http = {
    get: (url, params = {}) => request('GET', url, null, params),
    post: (url, data = {}) => request('POST', url, data),
    put: (url, data = {}) => request('PUT', url, data),
    delete: (url) => request('DELETE', url),
};
