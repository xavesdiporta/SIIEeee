<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Google\Client;
use Google\Service\Drive;
use Google\Service\Drive\DriveFile;
use App\Models\Ata; // Garante que tens o model Ata criado

class AtaController extends Controller
{
    public function index()
    {
        $atas = Ata::where('seccao', 'exploradores')->latest('data_ata')->get();
        return view('pages.expedicao.atas', compact('atas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'data_ata' => ['required', 'date'],
            'descricao' => ['nullable', 'string', 'max:1000'],
            'ficheiro' => ['required', 'file', 'mimes:pdf,doc,docx,png,jpg', 'max:10240'], // máx 10MB
        ]);

        $folderId = config('services.google_drive.folders.atas_exploradores'); // ID da pasta do Drive no .env

        // Upload para o Google Drive
        $credentialsPath = config('services.google_drive.credentials');
        $fullPath = str_starts_with($credentialsPath, '/')
            ? $credentialsPath
            : base_path($credentialsPath);

        $client = new Client();
        $client->setAuthConfig($fullPath);
        $client->addScope(Drive::DRIVE_FILE);

        $driveService = new Drive($client);

        $fileUploaded = $request->file('ficheiro');
        $fileMetadata = new DriveFile([
            'name' => $validated['data_ata'] . ' - ' . $validated['nome'] . '.' . $fileUploaded->getClientOriginalExtension(),
            'parents' => $folderId ? [$folderId] : [],
        ]);

        $content = file_get_contents($fileUploaded->getRealPath());

        $driveFile = $driveService->files->create($fileMetadata, [
            'data' => $content,
            'mimeType' => $fileUploaded->getClientMimeType(),
            'uploadType' => 'multipart',
            'fields' => 'id, webViewLink, webContentLink',
        ]);

        // Guarda na base de dados
        Ata::create([
            'seccao' => 'exploradores',
            'nome' => $validated['nome'],
            'data_ata' => $validated['data_ata'],
            'descricao' => $validated['descricao'] ?? '',
            'drive_file_id' => $driveFile->id,
            'drive_link' => $driveFile->webViewLink,
        ]);

        return back()->with('status', 'Ata guardada e enviada para o Google Drive com sucesso!');
    }
}
