<template>
  <div class="file-upload">
    <!-- Botão de upload -->
    <button
      @click="triggerFileInput"
      class="btn btn-ghost btn-sm"
      :disabled="uploading"
    >
      <svg v-if="!uploading" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
      </svg>
      <span v-if="uploading" class="loading loading-spinner loading-sm"></span>
      <span v-if="!uploading">Anexar</span>
    </button>

    <!-- Input de arquivo oculto -->
    <input
      ref="fileInput"
      type="file"
      multiple
      accept="image/*,.pdf,.doc,.docx,.txt,.xls,.xlsx"
      class="hidden"
      @change="handleFileSelect"
    />

    <!-- Preview dos arquivos selecionados -->
    <div v-if="selectedFiles.length > 0" class="mt-2 space-y-2">
      <div
        v-for="(file, index) in selectedFiles"
        :key="index"
        class="flex items-center gap-3 p-2 bg-base-200 rounded-lg"
      >
        <!-- Preview da imagem -->
        <div v-if="file.preview" class="w-12 h-12 rounded-lg overflow-hidden bg-base-300">
          <img :src="file.preview" :alt="file.name" class="w-full h-full object-cover" />
        </div>
        
        <!-- Ícone do arquivo -->
        <div v-else class="w-12 h-12 rounded-lg bg-base-300 flex items-center justify-center text-2xl">
          {{ getFileIcon(file.type) }}
        </div>

        <!-- Informações do arquivo -->
        <div class="flex-1 min-w-0">
          <p class="text-sm font-medium truncate">{{ file.name }}</p>
          <p class="text-xs text-base-content/50">{{ formatFileSize(file.size) }}</p>
        </div>

        <!-- Botão de remover -->
        <button
          @click="removeFile(index)"
          class="btn btn-ghost btn-xs text-error"
        >
          ✕
        </button>
      </div>
    </div>

    <!-- Progress bar -->
    <div v-if="uploading" class="mt-2">
      <progress class="progress progress-primary w-full" :value="uploadProgress" max="100"></progress>
      <p class="text-xs text-center mt-1">{{ uploadProgress }}% enviado</p>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import axios from 'axios';

interface FileWithPreview extends File {
  preview?: string;
}

const emit = defineEmits<{
  'upload-complete': [files: any[]];
  'upload-error': [error: string];
}>();

const fileInput = ref<HTMLInputElement>();
const selectedFiles = ref<FileWithPreview[]>([]);
const uploading = ref(false);
const uploadProgress = ref(0);

// Tipos de arquivo permitidos
const allowedTypes = [
  'image/jpeg',
  'image/png',
  'image/gif',
  'image/webp',
  'application/pdf',
  'application/msword',
  'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
  'application/vnd.ms-excel',
  'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
  'text/plain',
];

// Tamanho máximo por arquivo (10MB)
const maxFileSize = 10 * 1024 * 1024;

// Acionar input de arquivo
function triggerFileInput() {
  fileInput.value?.click();
}

// Lidar com seleção de arquivos
function handleFileSelect(event: Event) {
  const target = event.target as HTMLInputElement;
  const files = Array.from(target.files || []);

  // Validar arquivos
  for (const file of files) {
    if (!allowedTypes.includes(file.type)) {
      emit('upload-error', `Tipo de arquivo não suportado: ${file.name}`);
      continue;
    }

    if (file.size > maxFileSize) {
      emit('upload-error', `Arquivo muito grande: ${file.name} (máximo 10MB)`);
      continue;
    }

    // Criar preview para imagens
    const fileWithPreview = file as FileWithPreview;
    if (file.type.startsWith('image/')) {
      const reader = new FileReader();
      reader.onload = (e) => {
        fileWithPreview.preview = e.target?.result as string;
      };
      reader.readAsDataURL(file);
    }

    selectedFiles.value.push(fileWithPreview);
  }

  // Limpar input
  target.value = '';
}

// Remover arquivo da lista
function removeFile(index: number) {
  selectedFiles.value.splice(index, 1);
}

// Obter ícone do arquivo
function getFileIcon(mimeType: string): string {
  if (mimeType.startsWith('image/')) return '🖼️';
  if (mimeType === 'application/pdf') return '📄';
  if (mimeType.includes('word')) return '📝';
  if (mimeType.includes('excel') || mimeType.includes('sheet')) return '📊';
  if (mimeType === 'text/plain') return '📄';
  return '📎';
}

// Formatar tamanho do arquivo
function formatFileSize(bytes: number): string {
  const units = ['B', 'KB', 'MB', 'GB'];
  let size = bytes;
  let unitIndex = 0;

  while (size >= 1024 && unitIndex < units.length - 1) {
    size /= 1024;
    unitIndex++;
  }

  return `${size.toFixed(1)} ${units[unitIndex]}`;
}

// Upload dos arquivos
async function uploadFiles() {
  if (selectedFiles.value.length === 0) return;

  uploading.value = true;
  uploadProgress.value = 0;

  try {
    const uploadedFiles = [];

    for (let i = 0; i < selectedFiles.value.length; i++) {
      const file = selectedFiles.value[i];
      const formData = new FormData();
      formData.append('file', file);

      const response = await axios.post('/api/files/upload', formData, {
        headers: {
          'Content-Type': 'multipart/form-data',
        },
        onUploadProgress: (progressEvent) => {
          const fileProgress = (progressEvent.loaded / progressEvent.total!) * 100;
          const totalProgress = ((i + fileProgress / 100) / selectedFiles.value.length) * 100;
          uploadProgress.value = Math.round(totalProgress);
        },
      });

      uploadedFiles.push(response.data.file);
    }

    emit('upload-complete', uploadedFiles);
    selectedFiles.value = [];
    uploadProgress.value = 0;
  } catch (error: any) {
    emit('upload-error', error.response?.data?.message || 'Erro ao fazer upload dos arquivos');
  } finally {
    uploading.value = false;
  }
}

// Expor função de upload
defineExpose({
  uploadFiles,
});
</script>




