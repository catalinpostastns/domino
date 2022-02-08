<template>
    <div class="p-4 w-full">
        <table align="center" class="max-w-[1000px]">
            <tbody>
            <tr>
                <template v-if="boardDominoes.length" v-for="(domino, index) in boardDominoes">
                    <SkipDomino v-if="showSkipDomino(index)"></SkipDomino>
                    <BoardDomino v-else-if="!skipDomino(index)" :id="domino.id"></BoardDomino>
                </template>
                <template v-else-if="selectedDomino">
                    <PlaceDomino side="first"></PlaceDomino>
                </template>
            </tr>
            </tbody>
        </table>

        <div v-if="extraNeeded" class="absolute bottom-40 flex w-full left-0 justify-center">
            <div class="grid grid-flow-col gap-2">
                <template v-for="domino in extraDominoes">
                    <ExtraDomino :id="domino.id"></ExtraDomino>
                </template>
            </div>
        </div>
    </div>
</template>

<script>
const SIDE_LEFT = 'left';
const SIDE_RIGHT = 'right';

import PlaceDomino from "./PlaceDomino";
import BoardDomino from "./BoardDomino";
import ExtraDomino from "./ExtraDomino";
import SkipDomino from "./SkipDomino";

export default {
    components: {
        PlaceDomino,
        BoardDomino,
        ExtraDomino,
        SkipDomino
    },
    computed: {
        extraDominoes() {
            return this.$store.getters.getExtraDominoes;
        },
        selectedDomino() {
            return this.$store.getters.getSelectedDomino;
        },
        isMyTurn() {
            return this.$store.getters.isMyTurn;
        },
        extraNeeded() {
            return this.isMyTurn && !this.hasAvailableDomino;
        },
        dominoes() {
            return this.$store.getters.getUserDominoes;
        },
        hasAvailableDomino() {
            let dominoes = this.dominoes;
            let numberOfDominoes = dominoes.length;

            let numberOfBoardDominoes = this.boardDominoes.length;
            if (numberOfBoardDominoes === 0) {
                return true;
            }

            for (let i = 0; i < numberOfDominoes; i++) {
                if (this.checkShowDomino(dominoes[i], SIDE_LEFT) || this.checkShowDomino(dominoes[i], SIDE_RIGHT)) {
                    return true;
                }
            }

            return false;
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
    },
    methods: {
        checkShowDomino(domino, side) {
            let placedDomino = (side === SIDE_LEFT) ? this.firstDomino : this.lastDomino;
            let placedDominoSide = (side === SIDE_LEFT) ?
                ((!placedDomino.flip) ? placedDomino.side1 : placedDomino.side2) :
                ((!placedDomino.flip) ? placedDomino.side2 : placedDomino.side1);

            if ((domino.side1 === placedDominoSide) || (domino.side2 === placedDominoSide)) {
                return true;
            }

            return false;
        },
        showSkipDomino(index) {
            if (this.boardDominoes.length < 6) {
                return false;
            }

            if (index === 3) {
                return true;
            }

            return false;
        },
        skipDomino(index) {
            let numberOfBoardDominoes = this.boardDominoes.length;

            if (numberOfBoardDominoes < 6) {
                return false;
            }

            if ((index > 3) && (index < numberOfBoardDominoes - 3)) {
                return true;
            }

            return false;
        }
    }
}
</script>
