<template>
    <div class="reading-list-bar">
        <div class="card">
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
                <h3>{{ name }}
                    <span class="add-link"
                          title="Add a link"
                          @click.stop="addURL">
                        +
                    </span>
                </h3>
            </div>
            <draggable v-model="readingList.links" @start="drag=true" @end="endDrag">
                <div v-for="link in readingList.links" :key="link.id">
                    <reading-link :link="link"
                                  :windowWidth="windowWidth"
                                  :id="id">
                    </reading-link>
                </div>
            </draggable>
            <div v-if="readingList.links.length < 1"
                 class="card-body">
                You haven't saved anything in this list yet. To add something, click the plus symbol.
            </div>
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
        props: {
            name: String,
            id: Number,
            windowWidth: Number,
            readingList: Object
        },
        data() {
            return {
                modal: {
                    route: '',
                    buttonText: '',
                    placeholder: '',
                    body: '',
                    btnClass: '',
                },
                showOptions: false,
                showMenu: false,
            }
        },
        methods: {
            addURL() {
                this.modal.route = 'link/create';
                this.modal.title = `Add to ${this.name}`;
                this.modal.buttonText = "Add";
                this.modal.placeholder = "Paste the URL here";
                EventBus.$emit('toggle-modal',
                    this.modal.route,
                    this.modal.title,
                    this.modal.buttonText,
                    this.modal.placeholder,
                    'POST',
                    this.id,
                );
            },
            editListName() {
                this.modal.route = `lists/edit/${this.id}`;
                this.modal.title = `Change list name - ${this.name}`;
                this.modal.buttonText = "Edit";
                this.modal.placeholder = "Enter new list name";
                EventBus.$emit('toggle-modal',
                    this.modal.route,
                    this.modal.title,
                    this.modal.buttonText,
                    this.modal.placeholder,
                    'PUT',
                );
            },
            endDrag() {
                this.drag = false;
                let order = [];
                this.readingList.links.forEach(function(link) {
                    order.push(link.id);
                });
                this.reorderLinks(order);
            },
            deleteList() {
                this.modal.route = `lists/delete/${this.id}`;
                this.modal.body = `Are you sure you want to delete ${this.name} ?`;
                this.modal.buttonText = "Delete";
                this.modal.btnClass = "danger";
                EventBus.$emit('toggle-confirmation-modal',
                    this.modal.route,
                    this.modal.title,
                    this.modal.buttonText,
                    this.modal.body,
                    'POST',
                );
            },
            hideEditMenu() {
                this.showMenu = false;
            },
            reorderLinks(order) {
                axios.put('/api/link/reorder', order);
            },
            showEditMenu() {
                this.showMenu = true;
            },
        }
    }
</script>

<style type="scss" scoped>
    h3 {
        margin: 0;
    }
    .add-link {
        float: right;
        margin-left: -12px;
        cursor: pointer;
    }
    .card {
        border: none;
        max-height: 630px;
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
        background: white;
        z-index: 999;
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
    }
</style>
