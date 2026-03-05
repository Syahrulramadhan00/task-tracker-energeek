<template>
  <div v-if="isLoading" class="p-8 text-center text-gray-500">
    Memuat detail project...
  </div>

  <div v-else-if="project" class="h-full flex flex-col">
    <div
      class="mb-6 bg-white p-6 rounded-xl border border-gray-200 shadow-sm flex justify-between items-start"
    >
      <div>
        <router-link
          to="/projects"
          class="text-sm text-gray-500 hover:text-blue-600 mb-2 inline-block"
        >
          &larr; Kembali
        </router-link>
        <h1 class="text-2xl font-bold text-gray-900 mb-2">
          {{ project.name }}
        </h1>
        <p class="text-gray-600 text-sm">{{ project.description }}</p>
      </div>
      <div class="flex items-center gap-4">
        <span
          class="px-3 py-1 text-xs font-semibold rounded-full"
          :class="
            project.status === 'active'
              ? 'bg-green-100 text-green-700'
              : 'bg-gray-100 text-gray-700'
          "
        >
          {{ project.status === "active" ? "Active" : "Archived" }}
        </span>
      </div>
    </div>

    <div class="flex justify-between items-center mb-4">
      <h2 class="text-lg font-bold">Task</h2>
      <button
        @click="openModal('create')"
        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors"
      >
        + Tambah Task
      </button>
    </div>

    <div class="flex gap-6 overflow-x-auto pb-4 flex-1">
      <div
        v-for="col in columns"
        :key="col.id"
        class="shrink-0 w-72 bg-gray-100 rounded-xl p-4 flex flex-col max-h-full"
      >
        <div class="flex justify-between items-center mb-4 px-1">
          <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider">
            {{ col.name }}
          </h3>
          <span
            class="bg-gray-200 text-gray-600 text-xs py-0.5 px-2 rounded-full"
            >{{ getTasksByCategory(col.id).length }}</span
          >
        </div>

        <draggable
          v-model="tasksByColumn[col.id]"
          group="tasks"
          item-key="id"
          class="flex-1 overflow-y-auto space-y-3 min-h-[50px] p-1"
          @end="onDragEnd($event, col.id)"
        >
          <template #item="{ element }">
            <div
              class="bg-white p-4 rounded-lg shadow-sm border border-gray-200 cursor-grab active:cursor-grabbing hover:border-blue-300 transition-colors"
            >
              <h4 class="text-sm font-semibold text-gray-800 mb-1">
                {{ element.title }}
              </h4>
              <p class="text-xs text-gray-500 mb-3 line-clamp-2">
                {{ element.description }}
              </p>

              <div
                class="flex justify-between items-center mt-3 pt-3 border-t border-gray-100"
              >
                <span
                  class="text-[10px] text-gray-500 bg-gray-100 px-2 py-1 rounded"
                  >{{ formatDate(element.due_date) }}</span
                >
                <div class="flex gap-2">
                  <button
                    @click="openModal('edit', element)"
                    class="text-blue-500 hover:text-blue-700 text-xs font-medium"
                  >
                    Edit
                  </button>
                  <button
                    @click="deleteTask(element.id)"
                    class="text-red-500 hover:text-red-700 text-xs font-medium"
                  >
                    Hapus
                  </button>
                </div>
              </div>
            </div>
          </template>
        </draggable>
      </div>
    </div>

    <div
      v-if="isModalOpen"
      class="fixed inset-0 bg-black/50 flex items-center justify-center z-50"
    >
      <div
        class="bg-white rounded-xl shadow-xl w-full max-w-lg overflow-hidden"
      >
        <div class="flex justify-between items-center p-6 border-b">
          <h2 class="text-xl font-bold">
            {{ modalMode === "create" ? "Tambah Task Baru" : "Edit Task" }}
          </h2>
          <button
            @click="closeModal"
            class="text-gray-400 hover:text-gray-600 text-2xl leading-none"
          >
            &times;
          </button>
        </div>

        <form @submit.prevent="submitForm" class="p-6 space-y-4">
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1"
              >Judul Task</label
            >
            <input
              v-model="form.title"
              type="text"
              placeholder="Contoh: Setup CI/CD"
              class="w-full px-3 py-2 border rounded-md outline-none focus:ring-2 focus:ring-blue-500"
              :class="{ 'border-red-500': errors.title }"
            />
            <span v-if="errors.title" class="text-xs text-red-500 mt-1 block">{{
              errors.title[0]
            }}</span>
          </div>

          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1"
              >Kategori / Status</label
            >
            <select
              v-model="form.category_id"
              class="w-full px-3 py-2 border rounded-md outline-none focus:ring-2 focus:ring-blue-500"
              :class="{ 'border-red-500': errors.category_id }"
            >
              <option v-for="col in columns" :key="col.id" :value="col.id">
                {{ col.name }}
              </option>
            </select>
            <span
              v-if="errors.category_id"
              class="text-xs text-red-500 mt-1 block"
              >{{ errors.category_id[0] }}</span
            >
          </div>

          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1"
              >Due Date</label
            >
            <input
              v-model="form.due_date"
              type="date"
              class="w-full px-3 py-2 border rounded-md outline-none focus:ring-2 focus:ring-blue-500"
              :class="{ 'border-red-500': errors.due_date }"
            />
            <span
              v-if="errors.due_date"
              class="text-xs text-red-500 mt-1 block"
              >{{ errors.due_date[0] }}</span
            >
          </div>

          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1"
              >Deskripsi</label
            >
            <textarea
              v-model="form.description"
              rows="3"
              placeholder="Deskripsi detail task..."
              class="w-full px-3 py-2 border rounded-md outline-none focus:ring-2 focus:ring-blue-500"
              :class="{ 'border-red-500': errors.description }"
            ></textarea>
            <span
              v-if="errors.description"
              class="text-xs text-red-500 mt-1 block"
              >{{ errors.description[0] }}</span
            >
          </div>

          <div class="flex justify-end gap-3 mt-6 pt-4 border-t">
            <button
              type="button"
              @click="closeModal"
              class="px-4 py-2 text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-md text-sm font-medium"
            >
              Batal
            </button>
            <button
              type="submit"
              :disabled="isSubmitting"
              class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm font-medium disabled:opacity-50"
            >
              {{ isSubmitting ? "Menyimpan..." : "Simpan" }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from "vue";
import { useRoute } from "vue-router";
import draggable from "vuedraggable";
import api from "../lib/axios";
import { taskService, type Task } from "../services/task.service";

const route = useRoute();
const projectId = Number(route.params.id);

const project = ref<any>(null);
const isLoading = ref(true);

// Hardcode kategori sesuai seeder backend (ID 1-5)
const columns = [
  { id: 1, name: "TODO" },
  { id: 2, name: "IN PROGRESS" },
  { id: 3, name: "TESTING" },
  { id: 4, name: "DONE" },
  { id: 5, name: "PENDING" },
];

const tasksByColumn = ref<Record<number, Task[]>>({
  1: [],
  2: [],
  3: [],
  4: [],
  5: [],
});

// === STATE MODAL ===
const isModalOpen = ref(false);
const modalMode = ref<"create" | "edit">("create");
const isSubmitting = ref(false);
const currentEditId = ref<number | null>(null);
const errors = ref<Record<string, string[]>>({});

const form = reactive({
  project_id: projectId,
  category_id: 1,
  title: "",
  description: "",
  due_date: "",
});

// === METHODS ===
const loadProjectAndTasks = async () => {
  isLoading.value = true;
  try {
    const [projectRes, tasksRes] = await Promise.all([
      api.get(`/projects/${projectId}`),
      taskService.getTasksByProject(projectId),
    ]);

    project.value = projectRes.data;

    // Reset state sebelum diisi ulang
    tasksByColumn.value = { 1: [], 2: [], 3: [], 4: [], 5: [] };

    // Kelompokkan task ke dalam masing-masing kolom
    tasksRes.forEach((t: Task) => {
      const col = tasksByColumn.value[t.category_id];
      if (col) {
        col.push(t);
      }
    });
  } catch (error) {
    console.error("Gagal memuat data", error);
  } finally {
    isLoading.value = false;
  }
};

const getTasksByCategory = (categoryId: number) => {
  return tasksByColumn.value[categoryId] || [];
};

// --- LOGIKA MODAL FORM ---
const openModal = (mode: "create" | "edit", task?: Task) => {
  modalMode.value = mode;
  errors.value = {};

  if (mode === "edit" && task) {
    currentEditId.value = task.id;
    form.category_id = task.category_id;
    form.title = task.title;
    form.description = task.description;
    form.due_date = task.due_date.split("T")[0] ?? ""; // Format YYYY-MM-DD untuk input date
  } else {
    currentEditId.value = null;
    form.category_id = 1; // Default ke TODO
    form.title = "";
    form.description = "";
    form.due_date = "";
  }

  isModalOpen.value = true;
};

const closeModal = () => {
  isModalOpen.value = false;
};

const submitForm = async () => {
  isSubmitting.value = true;
  errors.value = {};

  try {
    if (modalMode.value === "create") {
      await taskService.createTask({ ...form, project_id: projectId });
    } else if (modalMode.value === "edit" && currentEditId.value) {
      await taskService.updateTask(currentEditId.value, {
        ...form,
        project_id: projectId,
      });
    }

    closeModal();
    await loadProjectAndTasks(); // Refresh board
  } catch (err: any) {
    if (err.response?.status === 422) {
      errors.value = err.response.data.errors; // Tampilkan error validasi per-field
    } else {
      alert(err.response?.data?.message || "Terjadi kesalahan sistem.");
    }
  } finally {
    isSubmitting.value = false;
  }
};

// --- LOGIKA DRAG & DROP ---
const onDragEnd = async (evt: any, _fromColId: number) => {
  if (evt.from === evt.to) return; // Tidak berpindah kolom

  const movedTask = evt.item._underlying_vm_;
  // evt.to adalah elemen DOM tujuan. Kita ambil atribut id yang kita tanam (bukan class/key)
  // Cara lebih bersih di Vue:
  const toColId = Number(evt.to.getAttribute("data-col-id"));

  if (!toColId || isNaN(toColId)) return;

  try {
    // Panggil API untuk update kategori
    await taskService.updateTask(movedTask.id, {
      project_id: movedTask.project_id,
      category_id: toColId,
      title: movedTask.title,
      description: movedTask.description,
      due_date: movedTask.due_date,
    });

    // Perbarui state lokal agar konsisten
    movedTask.category_id = toColId;
  } catch (error) {
    console.error("Gagal update status task", error);
    alert("Gagal memindahkan task. Data akan di-refresh.");
    await loadProjectAndTasks(); // Rollback UI jika API gagal
  }
};

const deleteTask = async (id: number) => {
  if (!confirm("Apakah Anda yakin ingin menghapus task ini?")) return;
  try {
    await taskService.deleteTask(id);
    await loadProjectAndTasks(); // Refresh board
  } catch (error) {
    alert("Gagal menghapus task");
  }
};

const formatDate = (dateString: string) => {
  if (!dateString) return "";
  return new Date(dateString).toLocaleDateString("id-ID", {
    day: "numeric",
    month: "short",
    year: "numeric",
  });
};

onMounted(() => {
  loadProjectAndTasks();
});
</script>
