//composable para chamadas de API 
import { ref } from 'vue'
import axios from 'axios'
import type { Task } from '@/types'

export function useTasks() {
  const tasks = ref<Task[]>([])
  const loading = ref(false)
  const error = ref<string | null>(null)

  const fetchTasks = async () => {
    loading.value = true
    try {
      const { data } = await axios.get('/api/tasks')
      tasks.value = data
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Erro ao carregar tarefas'
    } finally {
      loading.value = false
    }
  }

  const createTask = async (task: Partial<Task>) => {
    await axios.post('/api/tasks', task)
    await fetchTasks()
  }

  const updateTask = async (id: number, task: Partial<Task>) => {
    await axios.put(`/api/tasks/${id}`, task)
    await fetchTasks()
  }

  const deleteTask = async (id: number) => {
    await axios.delete(`/api/tasks/${id}`)
    tasks.value = tasks.value.filter(t => t.id !== id)
  }

  return {
    tasks,
    loading,
    error,
    fetchTasks,
    createTask,
    updateTask,
    deleteTask,
  }
}
