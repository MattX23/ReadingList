<template>
    <div class="container-fluid reading-list-container">
        <div class="row">
            <div v-for="readingList in readingLists" class="reading-list">
                <reading-list
                :name="readingList.name"
                :id="readingList.id"
                :links="readingList.links"
                :windowWidth="windowWidth"></reading-list>
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
                windowWidth: window.innerWidth,
            }
        },
        mounted() {
            this.fetchData();
            window.onresize = () => {
                this.windowWidth = window.innerWidth
            }
        },
        created() {
            EventBus.$on('re-render', () => {
                this.fetchData();
            })
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
    .reading-list {
        display: inline-block;
        width: 25rem;
        padding: 10px;
    }
    .reading-list-container {
        //overflow-y: scroll;
        width: max-content;
    }
</style>
