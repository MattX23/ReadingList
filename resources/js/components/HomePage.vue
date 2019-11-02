<template>
    <div class="container-fluid">
        <div class="row">
            <div v-for="readingList in readingLists" class="col-xl-4 col-md-6">
                <reading-list
                :name="readingList.name"
                :id="readingList.id"
                :links="readingList.links"></reading-list>
            </div>
        </div>
    </div>
</template>

<script>
    import { EventBus } from '../eventbus/event-bus.js';

    export default {
        data() {
            return {
                readingLists: {},
            }
        },
        mounted() {
            this.fetchData();
        },
        created() {
            EventBus.$on('re-render', () => {
                this.fetchData();
            })
        },
        computed: {

        },
        methods: {
            fetchData() {
                axios.get('/api/lists/get')
                .then((response) => {
                    this.readingLists = response.data.readingLists;
                });
            },
        }
    }
</script>

<style type="scss" scoped>

</style>
