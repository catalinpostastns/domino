<template>
    <template v-for="room in rooms">
        <button @click="joinRoom(room)"
                class="inline-flex items-center px-4 py-2 bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-400 active:bg-blue-600 focus:outline-none focus:border-blue-600 focus:shadow-outline-blue transition ease-in-out duration-150 space-x-2"
                :class="{'cursor-pointer' : room.allowed_to_join }">
            Room #{{ room.id }} - {{ room.number_of_users }} players - {{ room.allowed_to_join ? 'Join' : 'Join not allowed' }}
        </button>
    </template>
</template>

<script>
import PusherMixin from '@/Mixins/PusherMixin';

export default {
    mixins: [PusherMixin],
    computed: {
        user() {
            return this.$store.getters.getUser;
        },
        rooms() {
            return this.$store.getters.getRooms;
        }
    },
    methods: {
        joinRoom(room) {
            if (!room.allowed_to_join) {
                return;
            }

            let roomId = room.id;
            window.axios.post(`/api/game-room/${roomId}/join`, {})
            .then((response) => {
                this.$store.dispatch('setRoom', response.data.game_room);
                this.subscribeToRoom();
            })
            .catch(error => {
                alert('error');
            });
        }
    }
}
</script>
