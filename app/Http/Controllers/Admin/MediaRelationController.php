<?php

namespace App\Http\Controllers\Admin;

use App\Models\MediaRelation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class MediaRelationController extends AdminController
{
    public function destroy(Request $request, MediaRelation $mediaRelation): RedirectResponse
    {
        $mediaRelation->load(['mediaFile', 'mediable']);

        $mediaFile = $mediaRelation->mediaFile;

        abort_if($mediaFile === null, 404);

        $this->authorize('detach', $mediaFile);

        if ($mediaRelation->mediable) {
            $this->authorize('update', $mediaRelation->mediable);
        }

        $auditPayload = [
            'media_file_id' => $mediaFile->id,
            'media_relation_id' => $mediaRelation->id,
            'target_type' => $mediaRelation->mediable_type,
            'target_id' => $mediaRelation->mediable_id,
            'role' => $mediaRelation->role,
            'was_primary' => $mediaRelation->is_primary,
        ];

        $mediaRelation->delete();

        $this->recordAudit($request, $mediaFile, 'media_detached', $auditPayload, null);

        return redirect()->route('admin.media-files.show', $mediaFile)
            ->with('success', 'Media relation detached. The media file and physical file were not deleted.');
    }
}
