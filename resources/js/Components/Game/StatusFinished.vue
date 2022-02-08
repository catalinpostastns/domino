<template>
    <span v-if="winnersName" class="text-white">
        {{ winnersName }} won the game.
    </span>

    <span v-if="numberOfPlayers === 0" class="text-white">Wait for other players</span>
    <button v-else @click="restartGame"
            class="inline-flex items-center px-4 py-2 bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-400 active:bg-blue-600 focus:outline-none focus:border-blue-600 focus:shadow-outline-blue transition ease-in-out duration-150 space-x-2 cursor-pointer">
        Restart game
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
        winnersName() {
            return this.$store.getters.getWinnersName;
        },
    },
    methods: {
        restartGame() {
            axios.post(`/api/game-room/restart-game`)
                .then((response) => {
                })
                .catch(error => {
                    alert('error');
                });
        }
    }
}
</script>
