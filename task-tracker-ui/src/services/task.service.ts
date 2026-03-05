import api from '../lib/axios';

export interface Category {
  id: number;
  name: string;
}

export interface Task {
  id: number;
  project_id: number;
  category_id: number;
  title: string;
  description: string;
  due_date: string;
  category?: Category;
  project?: { id: number; name: string };
}

export const taskService = {
  async getTasksByProject(projectId: number) {
    const response = await api.get<Task[]>(`/tasks`, { params: { project_id: projectId } });
    return response.data;
  },
  async getTasks(search: string = '') {
    // Memanggil endpoint GET /api/tasks dengan query pencarian
    const response = await api.get<Task[]>('/tasks', {
      params: { search }
    });
    return response.data;
  },
  async createTask(data: any) {
    const response = await api.post('/tasks', data);
    return response.data;
  },
  async updateTask(id: number, data: any) {
    const response = await api.put(`/tasks/${id}`, data);
    return response.data;
  },
  async deleteTask(id: number) {
    const response = await api.delete(`/tasks/${id}`);
    return response.data;
  }
};