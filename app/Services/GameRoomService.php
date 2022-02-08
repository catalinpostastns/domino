<?php

namespace App\Services;

use App\Events\DominoPlaced;
use App\Events\DominoSelected;
use App\Events\GameFinished;
use App\Events\GameRestart;
use App\Events\GameStarted;
use App\Events\OpponentJoinedRoom;
use App\Events\UpdateRoom;
use App\Exceptions\NotFoundException;
use App\Exceptions\UnprocessableException;
use App\Models\Domino;
use App\Models\GameRoom;
use App\Models\GameRoomDomino;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class GameRoomService
{
    /**
     * @param User $user
     *
     * @return array
     */
    public function getDashboardData(User $user): array
    {
        $gameRoom = $user->getGameRoom();

        if ($gameRoom) {
            $gameRoom->loadUsers();
            $gameRoom->loadGameRoomDominoes();
            $userDominoes = $user->getGameRoomDominoes();
        }

        return [
            'userData' => $user,
            'rooms' => GameRoom::get()->each->setAppends(['status_name', 'number_of_users', 'allowed_to_join']),
            'room' => $gameRoom,
            'dominoes' => $userDominoes ?? [],
        ];
    }

    /**
     * @param User $user
     * @param int $roomId
     *
     * @return JsonResponse
     *
     * @throws UnprocessableException|NotFoundException
     */
    public function joinRoom(User $user, int $roomId): JsonResponse
    {
        $gameRoom = GameRoom::with('users')->find($roomId)->first();
        if (!$gameRoom) {
            throw new NotFoundException('The game room was not found');
        }

        if (!($gameRoom->hasStatusLobby() || $gameRoom->hasStatusFinished())) {
            throw new UnprocessableException('The game is not in lobby or finished status');
        }

        if ($gameRoom->maximumNumberOfPlayers()) {
            throw new UnprocessableException('The room is limited to maximum 4 players');
        }

        if ($gameRoom->hasUser($user)) {
            throw new UnprocessableException('You are already a member of this room');
        }

        if ($user->hasGameRoom()) {
            throw new UnprocessableException('You are already a member of a game room');
        }

        $gameRoom->addUser($user);

        event(new UpdateRoom('Update room', $gameRoom));
        event(new OpponentJoinedRoom('A player has joined the room', $gameRoom, $user));

        return response()->json(['message' => 'You have successfully joined the room', 'game_room' => $gameRoom], Response::HTTP_OK);
    }

    /**
     * @param User $user
     *
     * @return JsonResponse
     *
     * @throws UnprocessableException|NotFoundException
     */
    public function restartGame(User $user): JsonResponse
    {
        $gameRoom = $user->getGameRoom();
        if (!$gameRoom) {
            throw new NotFoundException('You are not a member of a game room');
        }

        if (!$gameRoom->hasStatusFinished()) {
            throw new UnprocessableException('The game is not in finished status');
        }

        $gameRoom->reset();

        if ($gameRoom->noMinimumPlayers()) {
            throw new UnprocessableException('The game requires at least 2 users to be played');
        }

        $this->generateGameRoomDominoes($gameRoom);
        $this->setGameRoomFirstUserTurn($gameRoom);

        $gameRoom->setStatusSelection();
        $gameRoom->save();

        $gameRoom->loadGameRoomDominoes();

        event(new GameRestart('The game has been restarted', $gameRoom));

        return response()->json(['message' => 'The game has been started successfully'], Response::HTTP_OK);
    }

    /**
     * @param User $user
     *
     * @return JsonResponse
     *
     * @throws UnprocessableException|NotFoundException
     */
    public function startGame(User $user): JsonResponse
    {
        $gameRoom = $user->getGameRoom();
        if (!$gameRoom) {
            throw new NotFoundException('You are not a member of a game room');
        }

        if (!$gameRoom->hasStatusLobby()) {
            throw new UnprocessableException('The game is not in selection status');
        }

        if ($gameRoom->noMinimumPlayers()) {
            throw new UnprocessableException('The game requires at least 2 users to be played');
        }

        $this->generateGameRoomDominoes($gameRoom);
        $this->setGameRoomFirstUserTurn($gameRoom);

        $gameRoom->setStatusSelection();
        $gameRoom->save();

        $gameRoom->loadGameRoomDominoes();

        event(new GameStarted('The game has started', $gameRoom));

        return response()->json(['message' => 'The game has been started successfully'], Response::HTTP_OK);
    }

    /**
     * @param GameRoom $gameRoom
     *
     * @return void
     */
    private function generateGameRoomDominoes(GameRoom $gameRoom)
    {
        $dominoes = Domino::all();

        $shuffledDominoes = $dominoes->shuffle()->all();
        foreach ($shuffledDominoes as $domino) {
            $gameRoomDomino = new GameRoomDomino();
            $gameRoomDomino->setGameRoom($gameRoom);
            $gameRoomDomino->setDomino($domino);
            $gameRoomDomino->save();
        }
    }

    /**
     * @param GameRoom $gameRoom
     *
     * @return void
     */
    private function setGameRoomFirstUserTurn(GameRoom $gameRoom)
    {
        $firstGameRoomUser = $gameRoom->getFirstGameRoomUser();
        $gameRoom->setUserTurn($firstGameRoomUser->getUser());
    }

    /**
     * @param User $user
     * @param int $dominoId
     *
     * @return JsonResponse
     *
     * @throws UnprocessableException|NotFoundException
     */
    public function selectDomino(User $user, int $dominoId): JsonResponse
    {
        $gameRoom = $user->getGameRoom();
        if (!$gameRoom) {
            throw new NotFoundException('You are not a member of a game room');
        }

        if (!$gameRoom->hasStatusSelection()) {
            throw new UnprocessableException('The game is not in selection status');
        }

        if (!$gameRoom->isUserTurn($user)) {
            throw new UnprocessableException('It is not your turn');
        }

        if ($user->maximumNumberOfSelectedDominoes()) {
            throw new UnprocessableException('You have the maximum amount of dominoes');
        }

        $gameRoomDomino = $gameRoom->getGameRoomDomino($dominoId);
        if (!$gameRoomDomino) {
            throw new NotFoundException('Invalid domino selected');
        }

        if ($gameRoomDomino->isSelected()) {
            throw new UnprocessableException('This domino has already been selected');
        }

        $gameRoomDomino->setUser($user);
        $gameRoomDomino->save();

        if ($user->maximumNumberOfSelectedDominoes()) {
            $this->setPlayerOrderAndStartGame($gameRoom);
        }

        event(new DominoSelected('A domino has been selected', $gameRoom, $gameRoomDomino));

        return response()->json(['message' => 'You have successfully selected the domino'], Response::HTTP_OK);
    }

    /**
     * @param User $user
     * @param int $dominoId
     *
     * @return JsonResponse
     *
     * @throws UnprocessableException|NotFoundException
     */
    public function selectExtraDomino(User $user, int $dominoId): JsonResponse
    {
        $gameRoom = $user->getGameRoom();
        if (!$gameRoom) {
            throw new NotFoundException('You are not a member of a game room');
        }

        if (!$gameRoom->hasStatusStarted()) {
            throw new UnprocessableException('The game is not in started status');
        }

        if (!$gameRoom->isUserTurn($user)) {
            throw new UnprocessableException('It is not your turn');
        }

        if ($this->userHasMatchingDominoes($user)) {
            throw new UnprocessableException("You don't need to select an extra domino");
        }

        $gameRoomDomino = $gameRoom->getGameRoomDomino($dominoId);
        if (!$gameRoomDomino) {
            throw new NotFoundException('Invalid domino selected');
        }

        if ($gameRoomDomino->isSelected()) {
            throw new UnprocessableException('This domino has already been selected');
        }

        $gameRoomDomino->setUser($user);
        $gameRoomDomino->save();

        event(new DominoSelected('An extra domino has been selected', $gameRoom, $gameRoomDomino));

        return response()->json(['message' => 'You have successfully selected the domino'], Response::HTTP_OK);
    }

    /**
     * @param GameRoom $gameRoom
     *
     * @return void
     */
    private function setPlayerOrderAndStartGame(GameRoom $gameRoom)
    {
        $nextGameRoomUser = $gameRoom->getNextGameRoomUser();
        if (!$nextGameRoomUser) {
            $biggestOwnedDoubleDomino = $gameRoom->getBiggestOwnedDoubleDomino();
            if ($biggestOwnedDoubleDomino) {
                $nextGameRoomUser = $gameRoom->getGameRoomUser($biggestOwnedDoubleDomino->user_id);

                $nextGameRoomUser->setIndex(1);
                $nextGameRoomUser->save();

                $gameRoomUsers = $gameRoom->getGameRoomUsers();
                $co = 2;
                foreach ($gameRoomUsers as $gameRoomUser) {
                    if ($gameRoomUser->getId() == $nextGameRoomUser->getId()) {
                        continue;
                    }

                    $gameRoomUser->setIndex($co++);
                    $gameRoomUser->save();
                }
            } else {
                $nextGameRoomUser = $gameRoom->getFirstGameRoomUser();
            }

            $gameRoom->setStatusStarted();
        }

        $gameRoom->setUserTurn($nextGameRoomUser->getUser());
        $gameRoom->save();
    }

    /**
     * @param User $user
     * @param int $gameRoomId
     * @param bool $place_left
     *
     * @return JsonResponse
     *
     * @throws UnprocessableException|NotFoundException
     */
    public function placeDomino(User $user, int $gameRoomId, bool $place_left): JsonResponse
    {
        $gameRoom = $user->getGameRoom();
        if (!$gameRoom) {
            throw new NotFoundException('You are not a member of a game room');
        }

        if (!$gameRoom->hasStatusStarted()) {
            throw new UnprocessableException('The game is not in started status');
        }

        if (!$gameRoom->isUserTurn($user)) {
            throw new UnprocessableException('It is not your turn');
        }

        $gameRoomDomino = $gameRoom->getGameRoomDomino($gameRoomId);
        if (!$gameRoomDomino) {
            throw new NotFoundException('Invalid domino selected');
        }

        if (!$gameRoomDomino->isOwner($user)) {
            throw new UnprocessableException('The specified domino is not yours');
        }

        if ($gameRoomDomino->isPlaced()) {
            throw new UnprocessableException('This domino has already been placed');
        }

        $this->validateAndPlaceDomino($gameRoom, $gameRoomDomino, $place_left);

        if ($user->noDominoesRemaining() || (!$gameRoom->hasExtraDominoesLeft() && !$this->userHasMatchingDominoes($user))) {
            $gameRoom->setStatusFinished();
            $gameRoom->save();

            event(new GameFinished('The game has been finished', $gameRoom));

            return response()->json(['message' => 'The game has been finished'], Response::HTTP_OK);
        }

        $nextGameRoomUser = $gameRoom->getNextGameRoomUser();
        if (!$nextGameRoomUser) {
            $nextGameRoomUser = $gameRoom->getFirstGameRoomUser();
        }

        $gameRoom->setUserTurn($nextGameRoomUser->getUser());
        $gameRoom->save();

        event(new DominoPlaced('A domino has been placed', $gameRoom, $gameRoomDomino));

        return response()->json(['message' => 'You have successfully placed the domino'], Response::HTTP_OK);
    }

    /**
     * @param GameRoom $gameRoom
     * @param GameRoomDomino $gameRoomDomino
     * @param bool $place_left
     *
     * @return void
     *
     * @throws UnprocessableException
     */
    private function validateAndPlaceDomino(GameRoom $gameRoom, GameRoomDomino $gameRoomDomino, bool $place_left)
    {
        if ($gameRoom->hasPlacedDominoes()) {
            $placedDomino = $place_left ? $gameRoom->getFirstPlacedDomino() : $gameRoom->getLastPlacedDomino();

            $placedDominoSide = $place_left ? $placedDomino->getLeftSide() : $placedDomino->getRightSide();

            if ($gameRoomDomino->matchesSide($placedDominoSide)) {
                $gameRoomDominoSide = $place_left ? $gameRoomDomino->getSide1() : $gameRoomDomino->getSide2();
                if ($gameRoomDominoSide === $placedDominoSide) {
                    $gameRoomDomino->setFlipped();
                }

                $placedDominoIndex = $placedDomino->getIndexPosition();
                $newIndexPosition = $place_left ? ($placedDominoIndex - 1) : ($placedDominoIndex + 1);

                $gameRoomDomino->setIndexPosition($newIndexPosition);
                $gameRoomDomino->save();
            } else {
                throw new UnprocessableException('Incompatible domino');
            }
        } else {
            $gameRoomDomino->setIndexPosition(0);
            $gameRoomDomino->save();
        }
    }

    /**
     * @param User $user
     *
     * @return bool
     *
     * @throws NotFoundException
     */
    private function userHasMatchingDominoes(User $user): bool
    {
        $gameRoom = $user->getGameRoom();
        if (!$gameRoom) {
            throw new NotFoundException('You are not a member of a game room');
        }

        $userGameRoomDominoes = $user->getNotPlacedGameRoomDominoes();
        if ($userGameRoomDominoes->count() > 0) {
            $firstPlacedDomino = $gameRoom->getFirstPlacedDomino();
            $lastPlacedDomino = $gameRoom->getLastPlacedDomino();

            foreach ($userGameRoomDominoes as $domino) {
                $leftSide = $firstPlacedDomino->getLeftSide();
                if ($domino->matchesSide($leftSide)) {
                    return true;
                }

                $rightSide = $lastPlacedDomino->getRightSide();
                if ($domino->matchesSide($rightSide)) {
                    return true;
                }
            }
        }

        return false;
    }
}
