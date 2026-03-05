import api from '../lib/axios';

export interface Project {
  id: number;
  name: string;
  description: string;
  status: 'active' | 'archived';
  created_at: string;
}

export interface ProjectPayload {
  name: string;
  description: string;
  status: 'active' | 'archived';
}

export const projectService = {
  async getProjects(search: string = '') {
    const response = await api.get<Project[]>('/projects', {
      params: { search }
    });
    return response.data;
  },

  async createProject(data: ProjectPayload) {
    const response = await api.post('/projects', data);
    return response.data;
  },

  async updateProject(id: number, data: ProjectPayload) {
    const response = await api.put(`/projects/${id}`, data);
    return response.data;
  }
};