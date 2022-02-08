export default {
    computed: {
        gameRoom() {
            return this.$store.getters.getRoom;
        }
    },
    mounted() {
        if (!this.gameRoom) {
            var gameRoomsChannel = window.pusher.subscribe('game-rooms');
            gameRoomsChannel.bind('update-room', this.updateRoom);
        }
    },
    methods: {
        subscribeToRoom() {
            if (!this.gameRoom) {
                return;
            }

            var gameRoomChannel = window.pusher.subscribe('game-room-' + this.gameRoom.id);

            gameRoomChannel.bind('opponent-joined-room', this.opponentJoinedRoom);
            gameRoomChannel.bind('game-started', this.gameStarted);
            gameRoomChannel.bind('domino-selected', this.dominoSelected);
            gameRoomChannel.bind('domino-placed', this.dominoPlaced);
            gameRoomChannel.bind('game-finished', this.gameFinished);
            gameRoomChannel.bind('game-restart', this.gameRestart);
        },
        opponentJoinedRoom(data) {
            if (this.user.id !== data.user.id) {
                this.$store.dispatch('addOpponent', data.user);
            }
        },
        gameStarted(data) {
            this.$store.dispatch('setRoomUserTurn', data.game_room.user_id);
            this.$store.dispatch('setRoomStatus', data.game_room.status);
            this.$store.dispatch('setRoomDominoes', data.game_room.game_room_dominoes);
        },
        dominoSelected(data) {
            this.$store.dispatch('setRoomUserTurn', data.game_room.user_id);
            this.$store.dispatch('setRoomStatus', data.game_room.status);
            this.$store.dispatch('setDominoSelected', data.game_room_domino);
        },
        dominoPlaced(data) {
            this.$store.dispatch('setRoomUserTurn', data.game_room.user_id);
            this.$store.dispatch('setRoomStatus', data.game_room.status);
            this.$store.dispatch('setDominoPlaced', data.game_room_domino);
        },
        gameFinished(data) {
            this.$store.dispatch('setRoomStatus', data.game_room_status);
            this.$store.dispatch('setWinners', data.winners_name);
        },
        gameRestart(data) {
            this.$store.dispatch('restartRoom');
            this.$store.dispatch('setRoom', data.game_room);
        },
        updateRoom(data) {
            this.$store.dispatch('updateRoom', data.game_room);
        }
    }
}
