import {STATUS_SELECTION, STATUS_STARTED, STATUS_LOBBY, STATUS_FINISHED} from "@/constants";

export const domino = {
    state: () => ({
        room: false,
        rooms: [],
        winners_name: '',
        user: {},
        dominoes: [],
        selectedDomino: 0,
    }),

    actions: {
        restartRoom({commit, state}) {
            commit('restartRoom');
        },
        setRoom({commit, state}, room) {
            commit('setRoom', room);
        },
        setRooms({commit, state}, rooms) {
            commit('setRooms', rooms);
        },
        updateRoom({commit, state}, room) {
            commit('updateRoom', room);
        },
        setRoomUserTurn({commit, state}, user_id) {
            commit('setRoomUserTurn', user_id);
        },
        setRoomStatus({commit, state}, status) {
            commit('setRoomStatus', status);
        },
        setWinners({commit, state}, winners_name) {
            commit('setWinners', winners_name);
        },
        setOpponents({commit, state}, opponents) {
            commit('setOpponents', opponents);
        },
        setUser({commit, state}, value) {
            commit('setUser', value);
        },
        selectDomino({commit, state}, index) {
            commit('selectDomino', index);
        },
        setRoomDominoes({commit, state}, dominoes) {
            commit('setRoomDominoes', dominoes);
        },
        setDominoes({commit, state}, dominoes) {
            commit('setDominoes', dominoes);
        },
        setDominoSelected({commit, state}, game_room_domino) {
            commit('setDominoSelected', game_room_domino);
        },
        setDominoPlaced({commit, state}, values) {
            commit('setDominoPlaced', values);
        },
        chooseDomino({commit, state}, id) {
            commit('chooseDomino', id);
        },
        addOpponent({commit, state}, opponent) {
            commit('addOpponent', opponent);
        },
    },

    mutations: {
        restartRoom(state) {
            state.room = false;
            state.winners = [];
            state.dominoes = [];
            state.selectedDomino = 0;
        },
        addOpponent(state, opponent) {
            state.room.users.push(opponent);
        },
        setOpponents(state, opponents) {
            state.room.users = opponents;
        },
        setRoom(state, room) {
            state.room = room;
        },
        setRooms(state, rooms) {
            state.rooms = rooms;
        },
        updateRoom(state, room) {
            let id = room.id;

            let roomIndex = state.rooms.findIndex(function (room) {
                return room.id === id;
            });

            state.rooms[roomIndex] = room;
        },
        setRoomUserTurn(state, user_id) {
            state.room.user_id = user_id;
        },
        setRoomStatus(state, status) {
            state.room.status = status;
        },
        setWinners(state, winners_name) {
            state.winners_name = winners_name;
        },
        setUser(state, value) {
            state.user = value;
        },
        setRoomDominoes(state, dominoes) {
            state.room.game_room_dominoes = dominoes;
        },
        setDominoes(state, dominoes) {
            state.dominoes = dominoes;
        },
        selectDomino(state, index) {
            state.selectedDomino = index;
        },
        setDominoSelected(state, game_room_domino) {
            let id = game_room_domino.id;

            let dominoIndex = state.room.game_room_dominoes.findIndex(function (domino) {
                return domino.id === id;
            });

            state.room.game_room_dominoes[dominoIndex].selected = true;
        },
        setDominoPlaced(state, game_room_domino) {
            let id = game_room_domino.id;

            let dominoIndex = state.room.game_room_dominoes.findIndex(function (domino) {
                return domino.id === id;
            });

            state.room.game_room_dominoes[dominoIndex].index_position = game_room_domino.index_position;
            state.room.game_room_dominoes[dominoIndex].flip = game_room_domino.flip;

            state.dominoes = state.dominoes.filter(function (domino) {
                return domino.id !== id;
            });
        },
        chooseDomino(state, id) {
            let dominoIndex = state.room.game_room_dominoes.findIndex(function (domino) {
                return domino.id === id;
            });

            state.room.game_room_dominoes[dominoIndex].selected = true;

            state.dominoes.push(
                state.room.game_room_dominoes[dominoIndex]
            );
        }
    },

    getters: {
        getRoom(state) {
            return state.room;
        },
        getRooms(state) {
            return state.rooms;
        },
        isMyTurn(state) {
            return state.room.user_id === state.user.id;
        },
        isOpponentsTurn: (state) => (opponent_id) => {
            return state.room.user_id === opponent_id;
        },
        getStatus(state) {
            return state.room.status;
        },
        isGameStatusLobby(state) {
            return state.room.status === STATUS_LOBBY;
        },
        isGameStatusSelection(state) {
            return state.room.status === STATUS_SELECTION;
        },
        isGameStatusStarted(state) {
            return state.room.status === STATUS_STARTED;
        },
        isGameStatusFinished(state) {
            return state.room.status === STATUS_FINISHED;
        },
        getSelectedDomino(state) {
            return state.selectedDomino;
        },
        getOpponent: (state) => (id) => {
            let userIndex = state.room.users.findIndex(function (user) {
                return user.id === id;
            });

            return state.room.users[userIndex];
        },
        getOpponents(state) {
            if (state.room && state.room.users) {
                return state.room.users.filter(function (user) {
                    return user.id != state.user.id;
                });
            }

            return [];
        },
        getAllDominoes(state) {
            return state.room.game_room_dominoes;
        },
        getBoardDominoes(state) {
            return state.room.game_room_dominoes.filter(function (domino) {
                return domino.index_position !== null;
            }).sort(function (a, b) {
                if (a.index_position < b.index_position) {
                    return -1;
                }

                if (a.index_position > b.index_position) {
                    return 1;
                }

                return 0;
            });
        },
        getExtraDominoes(state) {
            return state.room.game_room_dominoes.filter(function (domino) {
                return domino.index_position === null;
            });
        },
        getRoomDomino: (state) => (id) => {
            let dominoIndex = state.room.game_room_dominoes.findIndex(function (domino) {
                return domino.id === id;
            });

            return state.room.game_room_dominoes[dominoIndex];
        },
        getDomino: (state) => (id) => {
            let dominoIndex = state.dominoes.findIndex(function (domino) {
                return domino.id === id;
            });

            return state.dominoes[dominoIndex];
        },
        getUserDominoes(state) {
            return state.dominoes.filter(function (domino) {
                return domino.index_position === null;
            });
        },
        getUser(state) {
            return state.user;
        },
        getWinnersName(state) {
            return state.winners_name;
        },
        getExtraNeeded(state) {
            return state.room.extraNeeded;
        }
    }
}
