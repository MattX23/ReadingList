<template>
    <div class="reading-list-bar">
        <draggable v-model="readingList.links"
                   group="readingList.links"
                   :class="[card, applyEmptyClass ? emptyClass : '']"
                   draggable="false">
            <div @mouseover="showEditMenu"
                 @mouseout="hideEditMenu"
                 class="card-header text-center">
                <h5>{{ name }}</h5>
                <span @click.stop="openListMenu"><i class="arrow down"></i></span>
                <options
                    :showListOptions="showListOptions"
                    :readingList="readingList"
                    :windowWidth="windowWidth"
                    :isExpanded="isExpanded"
                    :initiallyExpanded="initiallyExpanded">
                </options>
            </div>
            <div>
                <draggable v-model="readingList.links"
                           group="readingList.links"
                           @start="drag=true"
                           @end="endDrag">
                    <div v-for="link in readingList.links" :key="link.id">
                        <reading-link
                            :link="link"
                            :windowWidth="windowWidth"
                            :id="id"
                            :linkCount="readingList.links.length"
                            :isExpanded="isExpanded"
                            :initiallyExpanded="initiallyExpanded"
                        >
                        </reading-link>
                    </div>
                </draggable>
            </div>
            <input type="hidden" v-model="checkForListChanges">
        </draggable>
    </div>
</template>

<script>
    import draggable from 'vuedraggable';
    import { EventBus } from '../eventbus/event-bus.js';

    export default {
        components: {
            draggable,
        },
        props: {
            name: String,
            id: Number,
            readingList: Object
        },
        data() {
            return {
                showOptions: false,
                showMenu: false,
                itemsCount: this.readingList.links.length,
                card: "card",
                emptyClass: "empty-bar",
                showListOptions: false,
                windowWidth: window.innerWidth,
                isExpanded: false,
                initiallyExpanded: false,
            }
        },
        computed: {
            applyEmptyClass() {
                return !this.listItemsCount && (this.windowWidth > 576);
            },
            listItemsCount() {
                return this.readingList.links.length;
            },
            checkForListChanges() {
                if (this.listItemsCount !== this.itemsCount) {
                    this.itemsCount = this.listItemsCount;
                    this.reorderMultipleLists();
                    return true;
                }
            }
        },
        created() {
            EventBus.$on('close-options', () => {
                this.closeOptions();
            });
            EventBus.$on('toggle-list', (isExpanded, listId) => {
                if (this.readingList.id === listId) {
                    this.initiallyExpanded = false;
                    this.isExpanded = isExpanded;
                }
            });
        },
        mounted() {
            this.$nextTick(() => {
                window.addEventListener('resize', this.onResize);
            });

            if (this.readingList.position === 1) this.initiallyExpanded = true;

        },
        methods: {
            closeOptions() {
                this.showListOptions = false;
            },
            endDrag() {
                this.drag = false;
                let order = [];
                this.readingList.links.forEach(link => order.push(link.id));
                this.reorderLinks(order);
            },
            hideEditMenu() {
                this.showMenu = false;
            },
            onResize() {
                this.windowWidth = window.innerWidth;
            },
            openListMenu() {
                if (this.showListOptions) {
                    EventBus.$emit('close-options');
                } else {
                    EventBus.$emit('close-options');
                    this.showListOptions = !this.showListOptions;
                }
            },
            reorderLinks(order) {
                axios.put('/api/link/reorder', order);
            },
            reorderMultipleLists() {
                axios.put('/api/lists/reorder-multiple', this.readingList)
                    .then(() => {
                        EventBus.$emit('re-render');
                    });
            },
            showEditMenu() {
                this.showMenu = true;
            },
        }
    }
</script>

<style lang="scss" scoped>
    @import '../../sass/variables';

    h5 {
        color: $blue;
        width: 90%;
        margin-left: 1rem;
    }
    i {
        border: solid $black;
        border-width: 0 3px 3px 0;
        display: inline-block;
        padding: 3px;
        float: right;
        position: relative;
        bottom: 1.5rem;
        right: 10px;
        cursor: pointer;
    }
    .add-link {
        float: right;
        margin-left: -12px;
        cursor: pointer;
    }
    .card {
        border: none;
        max-height: 85vh;
        background: $white-low-transparency;
    }
    .card-body {
        background: $white;
    }
    .card-header {
        height: 50px;
        position: -webkit-sticky;
        position: sticky;
        top: 0;
        background: $white;
        z-index: 999;
        color: black;
        padding: 15px 0px 10px 0px
    }
    .down {
        transform: rotate(45deg);
        -webkit-transform: rotate(45deg);
    }
    .list-icon {
        float: left;
        cursor: pointer;
    }
    .empty-bar {
        background: $white-medium-transparency;
        height: 100%;
    }
    .hidden {
        visibility: hidden;
    }
    .reading-list-bar {
        margin-bottom: 15px;
        overflow-x: scroll;
        min-height: 85vh;
    }

    @media (max-width: 576px) {
        .card-header {
            position: relative;
            z-index: unset;
        }
        .reading-list-bar {
            overflow-x: unset;
            width: 100vw;
            min-height: fit-content;
        }
    }
</style>
