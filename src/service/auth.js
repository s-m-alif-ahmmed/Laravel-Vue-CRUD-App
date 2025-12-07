import api from './api';

export async function login(credentials) {
    const res = await api.post('/login', credentials);
    const { token, user } = res.data.data || res.data;
    localStorage.setItem('token', token);
    localStorage.setItem('user', JSON.stringify(user));
    return res.data;
}

export async function logout() {
  try { await api.post('/logout'); } catch (e) { console.warn('Logout API error', e); }
  localStorage.removeItem('token');
  localStorage.removeItem('user');
}

export function getUser() {
    try { return JSON.parse(localStorage.getItem('user')); } catch { return null; }
}

export function isAuthenticated() {
    return !!localStorage.getItem('token');
}
