import api from './api';

export const authService = {
  async login(credentials) {
    try {
      const response = await api.post('/auth', credentials);
      const { token, user } = response.data;
      
      if (token) {
        localStorage.setItem('authToken', token);
      }
      
      return user;
    } catch (error) {
      throw new Error(error.response?.data?.message || 'Ошибка авторизации');
    }
  },

  async logout() {
    try {
      await api.post('/logout');
    } catch (error) {
      console.error('Ошибка при выходе:', error);
    } finally {
      localStorage.removeItem('authToken');
    }
  },

  async getCurrentUser() {
    try {
      const response = await api.get('/profile');
      return response.data;
    } catch (error) {
      throw new Error('Не удалось получить данные пользователя');
    }
  },

  isAuthenticated() {
    return !!localStorage.getItem('authToken');
  }
};