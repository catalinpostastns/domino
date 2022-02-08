<template>
    <span v-if="numberOfPlayers === 0" class="text-white">Wait for other players</span>
    <button v-else @click="startGame"
            class="inline-flex items-center px-4 py-2 bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-400 active:bg-blue-600 focus:outline-none focus:border-blue-600 focus:shadow-outline-blue transition ease-in-out duration-150 space-x-2 cursor-pointer">
        Start game
    </button>
</template>

<script>

export default {
    computed: {
        user() {
            return this.$store.getters.getUser;
        },
        numberOfPlayers() {
            return this.$store.getters.getOpponents.length;
        },
    },
    methods: {
        startGame() {
            axios.post(`/api/game-room/start-game`)
            .then((response) => {
            })
            .catch(error => {
                alert('error');
            });
        }
    }
}
</script>
