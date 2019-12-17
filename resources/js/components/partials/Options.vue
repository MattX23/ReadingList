<template>
    <div v-show="showOptions" :class="optionsClass">
        <div class="option-item"
             @click="editTitle">
            Change title
        </div>
        <div @click="archiveLink"
             class="option-item">
            Archive
        </div>
        <div @click="deleteLink"
             class="option-item">
            Delete
        </div>
    </div>
</template>

<script>
    import { EventBus } from '../../eventbus/event-bus.js';

    export default {
        props: {
            showOptions: Boolean,
            optionsClass: String,
            link: Object,
            deleteFunction: Function
        },
        methods: {
            archiveLink() {
                let data = {
                    route: `link/archive/${this.link.id}`,
                    body: `Are you sure you want to archive ${this.link.title}?`,
                    buttonText: 'Archive',
                    btnClass: 'primary',
                    method: 'POST',
                };
                EventBus.$emit('toggle-confirmation-modal', data);
            },
            deleteLink() {
                this.deleteFunction();
            },
            editTitle() {
                let data = {
                    route: `link/edit/${this.link.id}`,
                    title: 'Edit link title',
                    buttonText: 'Edit',
                    placeholder: 'Add a new link name',
                    textInput: this.link.title,
                    method: 'PUT',
                };
                EventBus.$emit('toggle-modal', data);
            }
        }
    }
</script>

<style lang="scss" scoped>
    @import '../../../sass/variables';

    .options {
        position: absolute;
        border: 1px solid $gray;
        top: 7em;
        right: 45px;
        background: $off-white-high-transparency;
        cursor: pointer;
        border-radius: 15px 0px 15px 15px;
        text-align: center;
        margin-top: 10px;
        z-index: 9999;
        min-width:200px;
    }
    .options-last {
        top: -2em;
        right: 40px;
        border-radius: 15px 15px 0px 15px;
    }
    .option-item {
        padding: 10px;
        border-bottom: 1px solid $gray;
    }
    .option-item:last-child {
        border-bottom: none;
    }
    .option-item:hover {
        background: $blue;
        color: $white;
    }
    .option-item:hover:first-child {
        border-radius: 13px 0px 0px 0px;
    }
    .option-item:hover:last-child {
        background: darkred;
        border-radius: 0px 0px 13px 13px;
    }
    .options-last .option-item:hover:first-child {
        border-radius: 13px 13px 0px 0px;
    }
    .options-last .option-item:hover:last-child {
        background: $darkred;
        border-radius: 0px 0px 0px 13px;
    }
</style>
