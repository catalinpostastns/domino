<template>
    <Head title="Domino game"/>

    <div
        class="relative flex items-top justify-center min-h-screen bg-gray-100 bg-gray-900 sm:items-center sm:pt-0">
        <Game></Game>
    </div>
</template>

<script>
import {Head, Link} from '@inertiajs/inertia-vue3';
import PusherMixin from '@/Mixins/PusherMixin';

import Game from '@/Components/Game/Index';

export default {
    props: [
        'userData', 'rooms', 'room', 'dominoes'
    ],
    mixins: [PusherMixin],
    components: {
        Head,
        Link,
        Game
    },
    computed: {
        user() {
            return this.$store.getters.getUser;
        }
    },
    mounted() {
        this.$store.dispatch('setUser', this.userData);
        this.$store.dispatch('setRooms', this.rooms);

        if (this.room) {
            this.$store.dispatch('setRoom', this.room);
            this.$store.dispatch('setDominoes', this.dominoes);

            this.subscribeToRoom();
        }
    }
}
</script>
