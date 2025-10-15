<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDigitalArchiveRequest;
use App\Http\Requests\UpdateDigitalArchiveRequest;
use App\Models\Core\DigitalArchive;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class DigitalArchiveController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $archives = DigitalArchive::query()
            ->with(['uploadedBy'])
            ->filter($request->only(['search', 'document_type', 'is_public', 'archivable_type']))
            ->notExpired()
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();


        $documentTypes = DigitalArchive::select('document_type')
            ->distinct()
            ->pluck('document_type');


        $archivableTypes = DigitalArchive::select('archivable_type')
            ->whereNotNull('archivable_type')
            ->distinct()
            ->pluck('archivable_type');


        $stats = [
            'total' => DigitalArchive::count(),
            'total_size' => DigitalArchive::sum('file_size'),
            'public' => DigitalArchive::public()->count(),
            'private' => DigitalArchive::private()->count(),
        ];

        return Inertia::render('digital-archive/Index', [
            'archives' => $archives,
            'filters' => $request->only(['search', 'document_type', 'is_public', 'archivable_type']),
            'documentTypes' => $documentTypes,
            'archivableTypes' => $archivableTypes,
            'stats' => $stats,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('digital-archive/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDigitalArchiveRequest $request)
    {
        $validated = $request->validated();

        try {
            $archive = DB::transaction(function () use ($validated, $request) {

                $file = $request->file('file');
                $filePath = $file->store('digital-archive');


                $archiveData = [
                    'name' => $validated['name'],
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $filePath,
                    'mime_type' => $file->getMimeType(),
                    'file_size' => $file->getSize(),
                    'description' => $validated['description'] ?? null,
                    'document_type' => $validated['document_type'],
                    'archivable_id' => $validated['archivable_id'] ?? null,
                    'archivable_type' => $validated['archivable_type'] ?? null,
                    'uploaded_by' => Auth::id(),
                    'is_public' => $validated['is_public'] ?? false,
                    'expires_at' => $validated['expires_at'] ?? null,
                ];

                return DigitalArchive::create($archiveData);
            });

            return redirect()
                ->route('digital-archive.show', $archive)
                ->with('success', 'Ficheiro enviado com sucesso!');

        } catch (\Exception $e) {
            \Log::error('Erro ao enviar ficheiro:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->withInput()->with('error', 'Erro ao enviar ficheiro. Por favor, tente novamente.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(DigitalArchive $digitalArchive)
    {
        $digitalArchive->load(['uploadedBy', 'archivable']);

        return Inertia::render('digital-archive/Show', [
            'archive' => $digitalArchive,
            'fileExists' => $digitalArchive->fileExists(),
            'formattedSize' => $digitalArchive->getFormattedFileSize(),
            'extension' => $digitalArchive->getFileExtension(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DigitalArchive $digitalArchive)
    {
        return Inertia::render('digital-archive/Edit', [
            'archive' => $digitalArchive,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDigitalArchiveRequest $request, DigitalArchive $digitalArchive)
    {
        $validated = $request->validated();

        try {
            $digitalArchive->update($validated);

            return redirect()
                ->route('digital-archive.show', $digitalArchive)
                ->with('success', 'Ficheiro atualizado com sucesso!');

        } catch (\Exception $e) {
            \Log::error('Erro ao atualizar ficheiro:', [
                'archive_id' => $digitalArchive->id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->withInput()->with('error', 'Erro ao atualizar ficheiro. Por favor, tente novamente.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DigitalArchive $digitalArchive)
    {
        try {
            $archiveName = $digitalArchive->name;

            $digitalArchive->deleteFile();
            $digitalArchive->delete();

            return redirect()
                ->route('digital-archive.index')
                ->with('success', "Ficheiro \"{$archiveName}\" eliminado com sucesso!");

        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() === '23000') {
                return back()->with('error', 'Este ficheiro não pode ser eliminado pois está associado a outros registos.');
            }

            return back()->with('error', 'Erro ao eliminar ficheiro. Por favor, tente novamente.');

        } catch (\Exception $e) {
            \Log::error('Erro ao eliminar ficheiro:', [
                'archive_id' => $digitalArchive->id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Erro inesperado ao eliminar ficheiro. Contacte o suporte.');
        }
    }


    public function download(DigitalArchive $digitalArchive)
    {
        if (!$digitalArchive->fileExists()) {
            return back()->with('error', 'Ficheiro não encontrado.');
        }

        if ($digitalArchive->isExpired()) {
            return back()->with('error', 'Ficheiro expirado.');
        }

        return Storage::download(
            $digitalArchive->file_path,
            $digitalArchive->file_name
        );
    }


    public function view(DigitalArchive $digitalArchive)
    {
        if (!$digitalArchive->fileExists()) {
            return back()->with('error', 'Ficheiro não encontrado.');
        }

        if ($digitalArchive->isExpired()) {
            return back()->with('error', 'Ficheiro expirado.');
        }

        $headers = [
            'Content-Type' => $digitalArchive->mime_type,
            'Content-Disposition' => 'inline; filename="' . $digitalArchive->file_name . '"'
        ];

        return response()->file(
            Storage::path($digitalArchive->file_path),
            $headers
        );
    }
}
