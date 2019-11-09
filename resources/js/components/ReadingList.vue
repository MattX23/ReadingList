<template>
    <div class="reading-list-bar">
        <div class="card">
            <div @mouseover="showEditMenu"
                 @mouseout="hideEditMenu"
                 class="card-header text-center">
                <span title="Edit list name" v-show="showMenu"
                      @click.stop="editListName">
                    <img src="/images/icons/pencil-icon.png" alt="" class="edit-list-icon">
                </span>
                <h3>{{ name }}
                    <span class="add-link"
                          title="Add a link"
                          @click.stop="addURL">
                        +
                    </span>
                </h3>
            </div>
            <div v-for="link in links">
                <reading-link :link="link"
                :windowWidth="windowWidth"
                :id="id"></reading-link>
            </div>
            <div v-if="links.length < 1"
                 class="card-body">
                You haven't saved anything in this list yet. To add something, click the plus symbol.
            </div>
        </div>
    </div>
</template>

<script>
    import { EventBus } from '../eventbus/event-bus.js';

    export default {
        props: {
            name: String,
            id: Number,
            links: Array,
            windowWidth: Number,
        },
        data() {
            return {
                modal: {
                    method: '',
                    title: '',
                    buttonText: '',
                    placeholder: '',
                },
                showOptions: false,
                showMenu: false,
            }
        },
        methods: {
            addURL() {
                this.modal.method = 'link/create';
                this.modal.title = `Add to ${this.name}`;
                this.modal.buttonText = "Add";
                this.modal.placeholder = "Paste the URL here";
                EventBus.$emit('toggle-modal', this.modal.method, this.modal.title, this.modal.buttonText, this.modal.placeholder, this.id);
            },
            editListName() {
                this.modal.method = `lists/edit/${this.id}`;
                this.modal.title = `Change list name - ${this.name}`;
                this.modal.buttonText = "Edit";
                this.modal.placeholder = "Enter new list name";
                EventBus.$emit('toggle-modal', this.modal.method, this.modal.title, this.modal.buttonText, this.modal.placeholder);
            },
            hideEditMenu() {
                this.showMenu = false;
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
    .edit-list-icon {
        float: left;
        width: 20px;
        margin-top: 5px;
        margin-right: -30px;
        cursor: pointer;
    }
    .hidden {
        visibility: hidden;
    }
    .reading-list-bar {
        margin-bottom: 15px;
        overflow-x: scroll;
    }
</style>
