<template>
    <td v-if="show" class="border-4 border-lime-500 p-0 cursor-pointer"
        :class="{ 'w-[50px] h-[88px]': !placeHorizontal, 'w-[88px] h-[50px]': placeHorizontal }" @click="select">
        <img :src="image" class="object-contain opacity-0" :class="{ 'w-[88px] h-[84px]': placeHorizontal, 'rotate-90': placeHorizontal, 'rotate-[270deg]': (placeHorizontal && domino.flip) }">
    </td>
    <td width="10"></td>
</template>

<script>
const SIDE_LEFT = 'left';
const SIDE_FIRST = 'first';
const SIDE_RIGHT = 'right';

export default {
    props: ['side'],
    data() {
        return {
            image: 'img/selection.png',
        };
    },
    computed: {
        user() {
            return this.$store.getters.getUser;
        },
        firstDomino() {
            return this.boardDominoes[0];
        },
        lastDomino() {
            return this.boardDominoes[this.boardDominoes.length - 1];
        },
        boardDominoes() {
            return this.$store.getters.getBoardDominoes;
        },
        domino() {
            return this.$store.getters.getDomino(this.selectedDomino);
        },
        selectedDomino() {
            return this.$store.getters.getSelectedDomino;
        },
        placeHorizontal() {
            return this.domino.side1 !== this.domino.side2;
        },
        isMyTurn() {
            return this.$store.getters.isMyTurn;
        },
        show() {
            if (!this.domino || !this.isMyTurn) {
                return false;
            }

            if (this.side === SIDE_FIRST) {
                return true;
            }

            let placedDomino = (this.side === SIDE_LEFT) ? this.firstDomino : this.lastDomino;
            let placedDominoSide = (this.side === SIDE_LEFT) ?
                ((!placedDomino.flip) ? placedDomino.side1 : placedDomino.side2) :
                ((!placedDomino.flip) ? placedDomino.side2 : placedDomino.side1);

            if ((this.domino.side1 === placedDominoSide) || (this.domino.side2 === placedDominoSide)) {
                return true;
            }

            return false;
        },
    },
    methods: {
        select() {
            axios.post(`/api/game-room/place-domino/` + this.selectedDomino, {
                place_left: (this.side === SIDE_LEFT)
            })
            .then((response) => {
                this.$store.dispatch('selectDomino', 0);
            })
            .catch(error => {
                alert('error');
            });
        },
    }
}
</script>
