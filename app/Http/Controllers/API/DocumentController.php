<?php


namespace App\Http\Controllers\API;
 
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDocumentRequest;
use App\Http\Resources\DocumentResource;
use App\Models\Document;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Storage;
 
class DocumentController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Document::with('uploadeur');
        if ($request->filled('type'))       $query->deType($request->type);
        if ($request->filled('expires'))    $query->expires();
        if ($request->filled('expirant_bientot')) {
            $query->expirantBientot($request->input('jours', 30));
        }
        if ($request->filled('documentable_type') && $request->filled('documentable_id')) {
            $query->where('documentable_type', $request->documentable_type)
                  ->where('documentable_id', $request->documentable_id);
        }
        return DocumentResource::collection(
            $query->orderBy('created_at', 'desc')->paginate($request->input('per_page', 15))
        );
    }
 
    public function store(StoreDocumentRequest $request): DocumentResource
    {
        $data                = $request->validated();
        $data['uploaded_by'] = $request->user()->id;
        $fichier             = $request->file('fichier');
 
        $document = Document::create([
            ...$data,
            'chemin_fichier' => $fichier->store('documents', 'public'),
            'format_fichier' => $fichier->getClientOriginalExtension(),
            'taille_fichier' => (int) round($fichier->getSize() / 1024),
        ]);
 
        return (new DocumentResource($document->load('uploadeur')))
            ->additional(['message' => 'Document uploadé avec succès.']);
    }
 
    public function show(Document $document): DocumentResource
    {
        return new DocumentResource($document->load('documentable', 'uploadeur'));
    }
 
    public function destroy(Document $document): JsonResponse
    {
        Storage::disk('public')->delete($document->chemin_fichier);
        $document->delete();
        return response()->json(['success' => true, 'message' => 'Document supprimé.']);
    }
 
    public function alertes(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data'    => [
                'expires'          => DocumentResource::collection(Document::expires()->with('documentable')->get()),
                'expirant_bientot' => DocumentResource::collection(Document::expirantBientot(30)->with('documentable')->get()),
            ],
        ]);
    }
}