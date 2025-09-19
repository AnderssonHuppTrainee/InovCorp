<?php

namespace App\Http\Controllers;

use App\Models\FileUpload;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileUploadController extends Controller
{
    /**
     * Upload de arquivo.
     */
    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:10240', // 10MB max
            'message_id' => 'nullable|exists:messages,id',
        ]);

        $file = $request->file('file');
        $originalName = $file->getClientOriginalName();
        $mimeType = $file->getMimeType();
        $size = $file->getSize();

        // Gerar nome único para o arquivo
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $path = 'uploads/' . date('Y/m/d') . '/' . $filename;

        // Determinar tipo do arquivo
        $type = $this->determineFileType($mimeType);

        // Processar imagem se necessário
        $metadata = null;
        if (str_starts_with($mimeType, 'image/')) {
            $metadata = $this->processImage($file, $path);
        }

        // Salvar arquivo
        Storage::putFileAs('public/' . dirname($path), $file, basename($path));

        // Salvar no banco de dados
        $fileUpload = FileUpload::create([
            'user_id' => auth()->id(),
            'message_id' => $request->message_id,
            'original_name' => $originalName,
            'filename' => $filename,
            'path' => $path,
            'mime_type' => $mimeType,
            'size' => $size,
            'type' => $type,
            'metadata' => $metadata,
        ]);

        return response()->json([
            'file' => $fileUpload->load('user:id,name'),
            'url' => $fileUpload->url,
        ]);
    }

    /**
     * Deletar arquivo.
     */
    public function destroy(FileUpload $fileUpload)
    {
        // Verificar se o usuário pode deletar o arquivo
        if ($fileUpload->user_id !== auth()->id()) {
            return response()->json(['message' => 'Não autorizado.'], 403);
        }

        $fileUpload->delete();

        return response()->json(['message' => 'Arquivo deletado com sucesso.']);
    }

    /**
     * Obter arquivo.
     */
    public function show(FileUpload $fileUpload)
    {
        if (!Storage::exists('public/' . $fileUpload->path)) {
            return response()->json(['message' => 'Arquivo não encontrado.'], 404);
        }

        return Storage::response('public/' . $fileUpload->path, $fileUpload->original_name);
    }

    /**
     * Determinar tipo do arquivo.
     */
    private function determineFileType(string $mimeType): string
    {
        if (str_starts_with($mimeType, 'image/')) {
            return 'image';
        }

        $documentTypes = [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'text/plain',
        ];

        if (in_array($mimeType, $documentTypes)) {
            return 'document';
        }

        return 'attachment';
    }

    /**
     * Processar imagem (obter dimensões).
     */
    private function processImage($file, string $path): ?array
    {
        try {
            $imageInfo = getimagesize($file->path());

            if ($imageInfo) {
                return [
                    'width' => $imageInfo[0],
                    'height' => $imageInfo[1],
                ];
            }

            return null;
        } catch (\Exception $e) {
            \Log::error('Erro ao processar imagem:', ['error' => $e->getMessage()]);
            return null;
        }
    }
}