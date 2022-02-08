<template>
    <img :src="image" class="h-20 box-content border-4" @click="select"
         :class="{ 'border-transparent': !isSelected, 'border-lime-500': isSelected,
         'cursor-pointer': (gameStarted && isMyTurn) }">
</template>

<script>
export default {
    props: ['id'],
    computed: {
        image() {
            let side1 = this.domino.side1;
            let side2 = this.domino.side2;
            if (side2 > side1) {
                let aux = side1;
                side1 = side2;
                side2 = aux;
            }

            return 'img/' + side1 + '-' + side2 + '.png';
        },
        domino() {
            return this.$store.getters.getDomino(this.id);
        },
        selectedDomino() {
            return this.$store.getters.getSelectedDomino;
        },
        isSelected() {
            return this.selectedDomino === this.id;
        },
        gameStarted() {
            return this.$store.getters.isGameStatusStarted;
        },
        isMyTurn() {
            return this.$store.getters.isMyTurn;
        },
    },
    methods: {
        select() {
            if (!this.gameStarted || !this.isMyTurn) {
                return;
            }

            this.$store.dispatch('selectDomino', this.id);
        },
    }
}
</script>
