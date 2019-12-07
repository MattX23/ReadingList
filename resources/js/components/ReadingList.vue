<template>
    <div class="reading-list-bar">
        <draggable v-model="readingList.links"
                   group="readingList.links"
                   :class="[card, !noListItems ? emptyClass : '']"
                   draggable="false">
            <div @mouseover="showEditMenu"
                 @mouseout="hideEditMenu"
                 class="card-header text-center">
                <span title="Edit list name"
                      v-show="showMenu"
                      @click.stop="editListName">
                    <img src="/images/icons/pencil-icon.png" alt="" class="list-icon edit-list-icon">
                </span>
                <span title="Delete list"
                      v-show="showMenu"
                      @click.stop="deleteList">
                    <img src="/images/icons/trash-icon.jpg" alt="" class="list-icon delete-list-icon">
                </span>
                <h5>{{ name }}
                    <span class="add-link"
                          title="Add a link"
                          @click.stop="addURL">
                        +
                    </span>
                </h5>
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
                            :numLinks="readingList.links.length"
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
        methods: {
            addURL() {
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
            editListName() {
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
                this.readingList.links.forEach(function(link) {
                    order.push(link.id);
                });
                this.reorderLinks(order);
            },
            hideEditMenu() {
                this.showMenu = false;
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

<style type="scss" scoped>
    h5 {
        margin: 0;
    }
    .add-link {
        float: right;
        margin-left: -12px;
        cursor: pointer;
    }
    .card {
        border: none;
        max-height: 85vh;
        background: rgba(255,255,255,0.3);
    }
    .card-body {
        background: white;
    }
    .card-header {
        height: 50px;
        position: -webkit-sticky;
        position: sticky;
        top: 0;
        background: rgba(0,0,0,0.25);
        z-index: 999;
        color: white;
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
        background: rgba(255,255,255,.5);
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
    .reading-list-bar {
        margin-bottom: 15px;
        overflow-x: scroll;
        min-height: 85vh;
    }
</style>
