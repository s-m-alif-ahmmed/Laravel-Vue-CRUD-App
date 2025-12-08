import api from './Api';

export async function login(credentials) {
    try {
        const res = await api.post('/login', credentials);

        // Extract token and user correctly
        const token = res.data.token;
        const user = res.data.data;

        if (!token) throw new Error('Token not returned from API');

        // Save to localStorage
        localStorage.setItem('token', token);
        localStorage.setItem('user', JSON.stringify(user));

        return { token, user };
    } catch (err) {
        console.error('Login failed', err);
        throw err;
    }
}

export async function logout() {
    try { await api.post('/logout'); } catch (e) { console.warn('Logout API error', e); }
    localStorage.removeItem('token');
    localStorage.removeItem('user');
}

export function getUser() {
    const user = localStorage.getItem('user');
    return user ? JSON.parse(user) : null;
}

export function isAuthenticated() {
    return !!localStorage.getItem('token');
}
