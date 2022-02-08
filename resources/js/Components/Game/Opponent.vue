<template>
    <div class="p-4 w-[300px] rounded-lg shadow-lg bg-blue-500" :class="{ 'border-orange': (isOpponentsTurn && !isGameStatusFinished) }">
        <div class="text-[20px] text-white">
            {{ opponent.name }}
        </div>
    </div>
</template>

<style scoped>
.border-orange {
    border: 5px solid orange;
}
</style>

<script>
export default {
    props: ['id'],
    computed: {
        opponent() {
            return this.$store.getters.getOpponent(this.id);
        },
        isOpponentsTurn() {
            return this.$store.getters.isOpponentsTurn(this.opponent.id);
        },
        isGameStatusFinished() {
            return this.$store.getters.isGameStatusFinished;
        },
    }
}
</script>
