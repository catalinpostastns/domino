<template>
    <div className="board-container relative flex flex-col">
        <Top></Top>

        <div className="grow flex justify-center items-center">
            <StatusJoin v-if="!room"></StatusJoin>
            <StatusStopped v-else-if="statusLobby"></StatusStopped>
            <StatusSelection v-else-if="statusSelection"></StatusSelection>
            <StatusStarted v-else-if="statusStarted"></StatusStarted>
            <StatusFinished v-else-if="statusFinished"></StatusFinished>
        </div>

        <Footer></Footer>
    </div>
</template>

<style scoped>
.board-container {
    border: 3px solid white;
    position: absolute;
    height: 100%;
    width: 100%;
}
</style>

<script>
import Top from './Top';
import Footer from "./Footer";
import StatusJoin from './StatusJoin';
import StatusStopped from './StatusLobby';
import StatusSelection from './StatusSelection';
import StatusStarted from './StatusStarted';
import StatusFinished from './StatusFinished';

export default {
    components: {
        Top,
        Footer,
        StatusJoin,
        StatusStopped,
        StatusSelection,
        StatusStarted,
        StatusFinished,
    },
    computed: {
        room() {
            return this.$store.getters.getRoom;
        },
        statusLobby() {
            return this.$store.getters.isGameStatusLobby;
        },
        statusSelection() {
            return this.$store.getters.isGameStatusSelection;
        },
        statusStarted() {
            return this.$store.getters.isGameStatusStarted;
        },
        statusFinished() {
            return this.$store.getters.isGameStatusFinished;
        },
    }
}
</script>
