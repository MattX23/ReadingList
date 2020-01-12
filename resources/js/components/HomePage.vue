<template>
    <div @click="closeMenus">
        <div v-if="loaded" class="container-fluid reading-list-container">
            <div v-if="readingLists.length < 1" class="card">
                <div class="card-body text-center">
                    You have no ReadingLists saved yet. <br><br>
                    To add one, click the arrow in the navbar and then select "New List".
                </div>
            </div>
            <draggable class="row" v-model="readingLists"
                       @start="drag=true"
                       @end="endDrag">
                    <reading-list
                        v-for="readingList in readingLists"
                        class="reading-list"
                        :name="readingList.name"
                        :id="readingList.id"
                        :readingList="readingList"
                        :key="readingList.id"
                        ></reading-list>
            </draggable>
        </div>
    </div>
</template>

<script>
    import draggable from 'vuedraggable';
    import { EventBus } from '../eventbus/event-bus.js';

    export default {
        components: {
            draggable,
        },
        data() {
            return {
                readingLists: [],
                windowWidth: window.innerWidth,
                loaded: false,
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
            });
        },
        methods: {
            closeMenus() {
                EventBus.$emit('close-options');
            },
            endDrag() {
                this.drag = false;
                let order = [];
                this.readingLists.forEach(list => order.push(list.id));
                this.reorderReadingLists(order);
            },
            fetchData() {
                axios.get('/api/lists/get')
                .then((response) => {
                    this.readingLists = response.data.readingLists;
                    this.loaded = true;
                });
            },
            reorderReadingLists(order) {
                axios.put('/api/lists/reorder', order);
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

    @media (max-width: 576px){
        .reading-list-container {
            width: 100vw;
        }
    }
</style>
