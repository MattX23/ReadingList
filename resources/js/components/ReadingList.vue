<template>
    <div class="reading-list-bar">
        <draggable v-model="readingList.links"
                   group="readingList.links"
                   :class="[card, !noListItems ? emptyClass : '']"
                   draggable="false">
            <div @mouseover="showEditMenu"
                 @mouseout="hideEditMenu"
                 class="card-header text-center">
                <h5>{{ name }}</h5>
                <span @click.stop="openListMenu"><i class="arrow down"></i></span>
                <div v-show="showListOptions" class="options">
                    <div class="option-item"
                         @click.stop="addURL">
                        Add a link
                    </div>
                    <div class="option-item"
                         @click.stop="editListName">
                        Change title
                    </div>
                    <div class="option-item"
                         @click.stop="deleteList">
                        Delete List
                    </div>
                </div>
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
                            :numLinks="readingList.links.length">
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
            windowWidth: Number,
            readingList: Object
        },
        data() {
            return {
                showOptions: false,
                showMenu: false,
                noItems: this.readingList.links.length,
                card: "card",
                emptyClass: "empty-bar",
                showListOptions: false,
            }
        },
        computed: {
            noListItems() {
                return this.readingList.links.length;
            },
            checkForListChanges() {
                if (this.noListItems !== this.noItems) {
                    this.noItems = this.noListItems;
                    this.reorderMultipleLists();
                    return true;
                }
            }
        },
        created() {
            EventBus.$on('close-options', () => {
                this.closeOptions();
            });
        },
        methods: {
            addURL() {
                this.closeOptions();
                let data = {
                    route: 'link/create',
                    title: `Add to ${this.name}`,
                    buttonText: 'Add',
                    placeholder: 'Paste the URL here',
                    method: 'POST',
                    readingListId: this.id,
                };
                EventBus.$emit('toggle-modal', data);
            },
            closeOptions() {
                this.showListOptions = false;
            },
            editListName() {
                this.closeOptions();
                let data = {
                    route: `lists/edit/${this.id}`,
                    title: `Change list name - ${this.name}`,
                    buttonText: 'Edit',
                    placeholder: 'Enter new list name',
                    textInput: this.name,
                    method: 'PUT',
                };
                EventBus.$emit('toggle-modal', data);
            },
            deleteList() {
                this.closeOptions();
                let data = {
                    route: `lists/delete/${this.id}`,
                    buttonText: 'Delete',
                    btnClass: 'delete',
                    body: `Are you sure you want to delete ${this.name} ?`,
                    method: 'DELETE',
                };
                EventBus.$emit('toggle-confirmation-modal', data);
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
    .edit-list-icon {
        width: 20px;
        margin-top: 5px;
        margin-right: -30px;
    }
    .empty-bar {
        background: $white-medium-transparency;
        height: 100%;
    }
    .delete-list-icon {
        width: 35px;
        margin-left: 25px;
        margin-right: -60px;
        margin-top: 4px;
    }
    .hidden {
        visibility: hidden;
    }
    .options {
        position: absolute;
        border: 1px solid $gray;
        top: 1.25em;
        right: 15px;
        background: $off-white-high-transparency;
        cursor: pointer;
        border-radius: 15px 0px 15px 15px;
        text-align: center;
        margin-top: 10px;
        z-index: 9999;
        min-width:200px;
    }
    .option-item {
        padding: 10px;
        border-bottom: 1px solid $gray;
        color: $black;
    }
    .option-item:hover {
        background: $blue;
        color: $white;
    }
    .option-item:last-child {
        border-bottom: none;
    }
    .option-item:hover:first-child {
        border-radius: 13px 0px 0px 0px;
    }
    .option-item:hover:last-child {
        background: darkred;
        border-radius: 0px 0px 13px 13px;
    }
    .reading-list-bar {
        margin-bottom: 15px;
        overflow-x: scroll;
        min-height: 85vh;
    }
</style>
