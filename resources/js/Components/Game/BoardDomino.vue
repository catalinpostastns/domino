<template v-if="domino">
    <PlaceDomino v-if="isFirstDomino" side="left"></PlaceDomino>

    <td class="border-4 border-transparent p-0" :class="{ 'w-[50px] h-[88px]': !placeHorizontal, 'w-[88px] h-[50px]': placeHorizontal }">
         <img :src="image" class="object-contain" :class="{ 'w-[88px] h-[84px]': placeHorizontal, 'rotate-90': placeHorizontal, 'rotate-[270deg]': (placeHorizontal && domino.flip) }">
    </td>
    <td width="10"></td>

    <PlaceDomino v-if="isLastDomino" side="right"></PlaceDomino>
</template>

<script>
import PlaceDomino from "./PlaceDomino";

export default {
    props: ['id'],
    components: {
        PlaceDomino
    },
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
        isFirstDomino() {
            if (!this.firstDomino || !this.domino) {
                return false;
            }

            return this.firstDomino.id === this.domino.id;
        },
        isLastDomino() {
            if (!this.lastDomino || !this.domino) {
                return false;
            }

            return this.lastDomino.id === this.domino.id;
        },
        dominoIndex() {
            let id = this.id;
            return this.boardDominoes.findIndex(function (domino) {
                return domino.id === id;
            });
        },
        firstDomino() {
            if (this.boardDominoes.length > 0) {
                return this.boardDominoes[0];
            }

            return null;
        },
        lastDomino() {
            if (this.boardDominoes.length > 0) {
                return this.boardDominoes[this.boardDominoes.length - 1];
            }

            return null;
        },
        boardDominoes() {
            return this.$store.getters.getBoardDominoes;
        },
        domino() {
            return this.$store.getters.getRoomDomino(this.id);
        },
        selectedDomino() {
            return this.$store.getters.getSelectedDomino;
        },
        placeHorizontal() {
            return this.domino.side1 !== this.domino.side2;
        },
    },
}
</script>
