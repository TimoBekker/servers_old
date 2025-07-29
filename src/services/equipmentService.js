import api from './api';

export const equipmentService = {
  async getEquipment(params = {}) {
    const response = await api.get('/equipment', { params });
    return response.data;
  },

  async getEquipmentById(id) {
    const response = await api.get(`/equipment/${id}`);
    return response.data;
  },

  async createEquipment(data) {
    const response = await api.post('/equipment', data);
    return response.data;
  },

  async updateEquipment(id, data) {
    const response = await api.put(`/equipment/${id}`, data);
    return response.data;
  },

  async deleteEquipment(id) {
    const response = await api.delete(`/equipment/${id}`);
    return response.data;
  },

  async getEquipmentTypes() {
    const response = await api.get('/equipment/types');
    return response.data;
  },

  async getEquipmentStates() {
    const response = await api.get('/equipment/states');
    return response.data;
  },

  async getEquipmentPasswords(id) {
    const response = await api.get(`/equipment/passes/${id}`);
    return response.data;
  }
};