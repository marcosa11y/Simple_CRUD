import React, { createContext, useState, useEffect } from 'react';
import api from '../api';

export const AuthContext = createContext();

export const AuthProvider = ({ children }) => {
    const [user, setUser] = useState(null);

    useEffect(() => {
        const token = localStorage.getItem('auth_token');
        if (token) {
            // fetch user details and preserve role information
            api.get('/user').then(res => setUser(res.data)).catch(() => { });
        }
    }, []);

    const login = async (credentials) => {
        const response = await api.post('/login', credentials);
        const token = response.data.token;
        localStorage.setItem('auth_token', token);
        api.defaults.headers.Authorization = `Bearer ${token}`;
        // fetch user info if endpoint exists
        const userRes = await api.get('/user');
        setUser(userRes.data);
    };

    const register = async (data) => {
        const response = await api.post('/register', data);
        const token = response.data.token;
        localStorage.setItem('auth_token', token);
        api.defaults.headers.Authorization = `Bearer ${token}`;
        const userRes = await api.get('/user');
        setUser(userRes.data);
    };

    const logout = () => {
        localStorage.removeItem('auth_token');
        setUser(null);
    };

    // helper property to easily check admin status
    const isAdmin = user && user.role === 'admin';

    return (
        <AuthContext.Provider value={{ user, isAdmin, login, register, logout }}>
            {children}
        </AuthContext.Provider>
    );
};
