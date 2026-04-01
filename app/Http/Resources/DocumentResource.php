<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DocumentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nom' => $this->nom ?? $this->titre ?? null,
            'type' => $this->type ?? null,
            'chemin' => $this->chemin ?? $this->path ?? null,
            'url' => $this->chemin ? asset('storage/' . $this->chemin) : null,
            'uploaded_by' => $this->uploaded_by ?? null,
            'cree_le' => $this->created_at?->format('d/m/Y H:i'),
        ];
    }

    public function with(Request $request): array
    {
        return ['success' => true];
    }
}