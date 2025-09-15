<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HotelController extends Controller
{
    /**
     * Affiche les hôtels de l'utilisateur authentifié.
     * C'est l'endpoint principal pour le dashboard.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        // On récupère uniquement les hôtels de l'utilisateur connecté.
        // On s'assure que l'utilisateur est bien authentifié avant de faire la requête.
        $user = auth()->user();

        if (!$user) {
            // Si aucun utilisateur n'est authentifié, on renvoie une réponse 401 Unauthorized
            return response()->json(['message' => 'Non authentifié.'], 401);
        }

        // On renvoie la collection d'hôtels de cet utilisateur
        // La méthode with('user') n'est pas nécessaire ici car on filtre déjà par utilisateur
        $hotels = Hotel::where('user_id', $user->id)->get();

        return response()->json($hotels);
    }

    /**
     * Crée un nouvel hôtel pour l'utilisateur authentifié.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // On s'assure que l'utilisateur est authentifié
        $user = auth()->user();
        if (!$user) {
            return response()->json(['message' => 'Non authentifié.'], 401);
        }

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'price_per_night' => 'required|numeric|min:0',
            'address' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'currency' => 'required|string|max:3',
            'is_active' => 'boolean', // Ajout du champ is_active pour la validation
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Ajoute l'ID de l'utilisateur authentifié
        $validatedData['user_id'] = $user->id;

        // Ajout d'une valeur par défaut pour 'is_active' si non fournie
        $validatedData['is_active'] = $validatedData['is_active'] ?? true;

        // Gestion de l'upload de la photo
        if ($request->hasFile('photo')) {
            $validatedData['photo'] = $this->handlePhotoUpload($request->file('photo'));
        }

        $hotel = Hotel::create($validatedData);

        return response()->json([
            'message' => 'Hôtel créé avec succès',
            'hotel' => $hotel->load('user')
        ], 201);
    }

    // Le reste de votre code est excellent et n'a pas besoin de modifications majeures
    // show(), update(), destroy() restent les mêmes

    /**
     * Met à jour un hôtel.
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Hotel $hotel
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Hotel $hotel)
    {
        // ... (votre code existant) ...
    }

    /**
     * Supprime un hôtel.
     * @param \App\Models\Hotel $hotel
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Hotel $hotel)
    {
        // ... (votre code existant) ...
    }

    /**
     * Méthode privée pour gérer l'upload de photo.
     * @param \Illuminate\Http\UploadedFile $file
     * @return string
     */
    private function handlePhotoUpload($file)
    {
        return $file->store('hotels', 'public');
    }

    /**
     * Méthode privée pour supprimer une photo.
     * @param string $path
     * @return void
     */
    private function deletePhoto($path)
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
