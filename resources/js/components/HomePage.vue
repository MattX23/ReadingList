<template>
    <div class="container-fluid reading-list-container" @click="closeMenus">
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
            closeMenus() {
                EventBus.$emit('close-options');
            },
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
        padding: 0px 10px;
    }
    .reading-list-container {
        width: max-content;
    }
</style>
