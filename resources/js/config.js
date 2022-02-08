export default {
    mounted() {
        this.$nextTick(function () {
            if (this.user) {
                console.log('da da');
                window.axios.defaults.headers.common['usercustom'] = this.user.id;
            }
        });
    },
    computed: {
        user() {
            return this.$store.getters.getUser;
        }
    }
}
