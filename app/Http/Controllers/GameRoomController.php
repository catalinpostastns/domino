<?php

namespace App\Http\Controllers;

use App\Exceptions\RenderExceptionInterface;
use App\Models\GameRoom;
use App\Models\User;
use App\Services\GameRoomService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class GameRoomController extends Controller
{
    /**
     * @var GameRoomService $gameRoomService
     */
    private GameRoomService $gameRoomService;

    /**
     * @param GameRoomService $gameRoomService
     */
    public function __construct(GameRoomService $gameRoomService)
    {
        $this->gameRoomService = $gameRoomService;
    }

    /**
     * @return Response
     */
    public function dashboard(): Response
    {
        $user = Auth::user();

        $data = $this->gameRoomService->getDashboardData($user);

        return Inertia::render('Dashboard', $data);
    }

    /**
     * @param int $id
     *
     * @return JsonResponse
     */
    public function joinRoom(int $id): JsonResponse
    {
        $user = Auth::user();

        try {
            return $this->gameRoomService->joinRoom($user, $id);
        } catch (RenderExceptionInterface $e) {
            return $e->render();
        }
    }

    /**
     * @return JsonResponse
     */
    public function startGame(): JsonResponse
    {
        $user = Auth::user();

        try {
            return $this->gameRoomService->startGame($user);
        } catch (RenderExceptionInterface $e) {
            return $e->render();
        }
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function restartGame(): JsonResponse
    {
        $user = Auth::user();

        try {
            return $this->gameRoomService->restartGame($user);
        } catch (RenderExceptionInterface $e) {
            return $e->render();
        }
    }

    /**
     * @param int $id
     *
     * @return JsonResponse
     */
    public function selectDomino(int $id): JsonResponse
    {
        $user = Auth::user();

        try {
            return $this->gameRoomService->selectDomino($user, $id);
        } catch (RenderExceptionInterface $e) {
            return $e->render();
        }
    }

    /**
     * @param int $id
     *
     * @return JsonResponse
     */
    public function selectExtraDomino(int $id): JsonResponse
    {

        $user = Auth::user();

        try {
            return $this->gameRoomService->selectExtraDomino($user, $id);
        } catch (RenderExceptionInterface $e) {
            return $e->render();
        }
    }

    /**
     * @param int $id
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function placeDomino(int $id, Request $request): JsonResponse
    {
        $request->validate([
            'place_left' => 'boolean|required',
        ]);

        $user = Auth::user();

        try {
            $place_left = $request->boolean('place_left');

            return $this->gameRoomService->placeDomino($user, $id, $place_left);
        } catch (RenderExceptionInterface $e) {
            return $e->render();
        }
    }
}
