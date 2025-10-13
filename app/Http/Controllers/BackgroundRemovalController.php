<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\BackgroundRemoval;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BackgroundRemovalController extends Controller
{
    public function view(Request $request, string $type, string $uuid)
    {
        $removal = BackgroundRemoval::findByUuid($uuid);

        if (! $removal) {
            abort(404, 'Background removal not found');
        }

        // Ensure the user owns this record or is authorized
        $this->authorize('view', $removal);

        $path = match ($type) {
            'original' => $removal->original_path,
            'processed' => $removal->processed_path,
            default => abort(404, 'Invalid file type')
        };

        if (! $path || ! Storage::disk('public')->exists($path)) {
            abort(404, 'File not found');
        }

        $fileContents = Storage::disk('public')->get($path);
        $mimeType = $removal->mime_type ?? 'application/octet-stream';

        return response($fileContents)
            ->header('Content-Type', $mimeType)
            ->header('Content-Disposition', 'inline');
    }

    public function download(Request $request, string $type, string $uuid): StreamedResponse
    {
        $removal = BackgroundRemoval::findByUuid($uuid);

        if (! $removal) {
            abort(404, 'Background removal not found');
        }

        // Ensure the user owns this record or is authorized
        $this->authorize('view', $removal);

        $path = match ($type) {
            'original' => $removal->original_path,
            'processed' => $removal->processed_path,
            default => abort(404, 'Invalid file type')
        };

        if (! $path || ! Storage::disk('public')->exists($path)) {
            abort(404, 'File not found');
        }

        $filename = $type === 'original'
            ? $removal->original_filename
            : 'processed_'.$removal->original_filename;

        $fileContents = Storage::disk('public')->get($path);
        $mimeType = $removal->mime_type ?? 'application/octet-stream';

        return response()->streamDownload(function () use ($fileContents) {
            echo $fileContents;
        }, $filename, [
            'Content-Type' => $mimeType,
        ]);
    }
}
