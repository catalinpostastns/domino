<template>
    <img v-if="domino" :src="image" class="h-20" @click="select"
         :class="{ 'cursor-pointer': isMyTurn, 'invisible': domino.selected }">
</template>

<style scoped>
.invisible {
    visibility: hidden;
}
</style>

<script>
export default {
    props: ['id'],
    data() {
        return {
            image: 'img/selection.png',
        };
    },
    computed: {
        user() {
            return this.$store.getters.getUser;
        },
        domino() {
            return this.$store.getters.getRoomDomino(this.id);
        },
        isMyTurn() {
            return this.$store.getters.isMyTurn;
        },
    },
    methods: {
        select() {
            if (!this.isMyTurn) {
                return;
            }

            axios.post(`/api/game-room/select-domino/${this.id}`)
            .then((response) => {
                this.$store.dispatch('chooseDomino', this.id);
            })
            .catch(error => {
                alert('error');
                });
        },
    }
}
</script>
