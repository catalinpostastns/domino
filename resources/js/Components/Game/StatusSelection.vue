<template>
    <div class="p-4">
        <div class="grid grid-rows-2 grid-flow-col gap-4">
            <template v-for="domino in dominoes">
                <SelectionDomino :id="domino.id"></SelectionDomino>
            </template>
        </div>
    </div>
</template>

<script>
import SelectionDomino from "./SelectionDomino";

export default {
    components: {
        SelectionDomino
    },
    computed: {
        dominoes() {
            return this.$store.getters.getAllDominoes;
        }
    }
}
</script>
