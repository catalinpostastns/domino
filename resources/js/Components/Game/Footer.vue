<template>
    <div class="h-36 inset-x-0 bg-blue-500 text-white relative" :class="{ 'border-orange': (isMyTurn && !isGameStatusFinished) }">
        <div class="h-36 p-4 grid grid-rows-1 grid-flow-col sm:gap-2 md:gap-8 justify-center flex items-center">
            <template v-if="!isGameStatusFinished" v-for="domino in dominoes">
                <Domino :id="domino.id"></Domino>
            </template>
        </div>

        <div class="absolute inset-y-0 right-0 w-32 flex items-center p-4 text-center">
            <div class="inline-block align-middle flex flex-col">
                <span class="text-[20px] row text-white">{{ user.name }}</span>
            </div>
        </div>
    </div>
</template>

<style scoped>
    .border-orange {
        border: 5px solid orange;
    }
</style>

<script>
import Domino from './Domino';

export default {
    components: {
        Domino
    },
    computed: {
        dominoes() {
            return this.$store.getters.getUserDominoes;
        },
        user() {
            return this.$store.getters.getUser;
        },
        isGameStatusFinished() {
            return this.$store.getters.isGameStatusFinished;
        },
        isMyTurn() {
            return this.$store.getters.isMyTurn;
        }
    }
}
</script>
