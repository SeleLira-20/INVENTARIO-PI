// utils/apiConfig.js
export const API_URL = 'http://192.168.100.99:8000';

// Credenciales Basic Auth para endpoints protegidos
const CREDENTIALS = btoa('admin:Admin123!');

export const API_HEADERS = {
    'Content-Type':  'application/json',
    'Authorization': `Basic ${CREDENTIALS}`,
};

// Helper fetch con auth
export const apiFetch = async (endpoint, options = {}) => {
    const url = `${API_URL}${endpoint}`;
    const config = {
        ...options,
        headers: {
            ...API_HEADERS,
            ...(options.headers || {}),
        },
    };
    return fetch(url, config);
};