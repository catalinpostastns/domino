<template>
    <div class="h-36 inset-x-0 p-4">
        <div class="grid grid-rows-1 grid-flow-col gap-8 justify-center">
            <template v-for="opponent in opponents">
                <Opponent :id="opponent.id"></Opponent>
            </template>
        </div>
    </div>
</template>

<script>
import Opponent from './Opponent';

export default {
    components: {
        Opponent
    },
    computed: {
        opponents() {
            return this.$store.getters.getOpponents;
        },
    }
}
</script>
