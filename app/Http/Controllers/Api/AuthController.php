<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="Hotel Stock API",
 *      description="API pour la gestion du stock d'hôtel"
 * )
 * @OA\Server(
 *      url="http://hotel_stock.test", 
 *      description="API Server"
 * )
 * @OA\SecurityScheme(
 *     type="http",
 *     description="Authentification par Bearer Token (Sanctum)",
 *     name="Authorization",
 *     in="header",
 *     scheme="bearer",
 *     bearerFormat="Token",
 *     securityScheme="sanctum",
 * )
 */
class AuthController extends Controller
{
    /**
     * @OA\Post(
     *      path="/api/register",
     *      operationId="register",
     *      tags={"Authentication"},
     *      summary="Inscription d'un nouvel utilisateur",
     *      description="Crée un nouveau compte utilisateur",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"name","email","password"},
     *              @OA\Property(property="name", type="string", example="John Doe"),
     *              @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *              @OA\Property(property="password", type="string", format="password", example="password123")
     *          )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Inscription réussie",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Inscription réussie"),
     *              @OA\Property(property="user", type="object"),
     *              @OA\Property(property="token", type="string")
     *          )
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Erreur de validation"
     *      )
     * )
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'message' => 'Inscription réussie',
            'user' => $user,
            'token' => $token
        ], 201);
    }

    /**
     * @OA\Post(
     *      path="/api/login",
     *      operationId="login",
     *      tags={"Authentication"},
     *      summary="Connexion utilisateur",
     *      description="Authentifie un utilisateur existant",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"email","password"},
     *              @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *              @OA\Property(property="password", type="string", format="password", example="password123")
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Connexion réussie",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Connexion réussie"),
     *              @OA\Property(property="user", type="object"),
     *              @OA\Property(property="token", type="string")
     *          )
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Identifiants invalides"
     *      )
     * )
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            throw ValidationException::withMessages([
                'email' => ['Les informations d\'identification fournies sont incorrectes.'],
            ]);
        }

        $user = Auth::user();
        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'message' => 'Connexion réussie',
            'user' => $user,
            'token' => $token
        ]);
    }

    /**
     * @OA\Post(
     *      path="/api/logout",
     *      operationId="logout",
     *      tags={"Authentication"},
     *      summary="Déconnexion utilisateur",
     *      description="Déconnecte l'utilisateur authentifié",
     *      security={{"sanctum": {}}},
     *      @OA\Response(
     *          response=200,
     *          description="Déconnexion réussie",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Déconnexion réussie")
     *          )
     *      )
     * )
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Déconnexion réussie'
        ]);
    }

    /**
     * @OA\Post(
     *      path="/api/forgot-password",
     *      operationId="forgotPassword",
     *      tags={"Authentication"},
     *      summary="Demande de réinitialisation de mot de passe",
     *      description="Envoie un lien de réinitialisation par email",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"email"},
     *              @OA\Property(property="email", type="string", format="email", example="john@example.com")
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Lien de réinitialisation envoyé",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Lien de réinitialisation envoyé")
     *          )
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Erreur lors de l'envoi du lien"
     *      )
     * )
     */
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json([
                'message' => 'Lien de réinitialisation envoyé'
            ]);
        }

        return response()->json([
            'message' => 'Erreur lors de l\'envoi du lien'
        ], 400);
    }

    /**
     * @OA\Post(
     *      path="/api/reset-password",
     *      operationId="resetPassword",
     *      tags={"Authentication"},
     *      summary="Réinitialisation du mot de passe",
     *      description="Réinitialise le mot de passe avec un token",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"token","email","password"},
     *              @OA\Property(property="token", type="string", example="reset-token-here"),
     *              @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *              @OA\Property(property="password", type="string", format="password", example="newpassword123")
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Mot de passe réinitialisé avec succès",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Mot de passe réinitialisé avec succès")
     *          )
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Erreur lors de la réinitialisation"
     *      )
     * )
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json([
                'message' => 'Mot de passe réinitialisé avec succès'
            ]);
        }

        return response()->json([
            'message' => 'Erreur lors de la réinitialisation'
        ], 400);
    }
}
