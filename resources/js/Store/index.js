import {createStore} from "vuex";
import {domino} from '@/Store/domino';

const store = createStore({
    modules: {
        domino
    }
})

export default store
