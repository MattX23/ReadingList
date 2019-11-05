<template>
    <div>
        <div @click="closeOptions" class="card-body">
            <div class="row link-content">
                <div v-if="link.image" class="col-12 img-block">
                    <img :src="link.image" class="link-image">
                </div>
                <div class="col-12">
                    <div>
                        <h3 class="link-title"><a :href="link.url" target="_blank">{{ link.title }}</a></h3>
                    </div>
                    <div>
                        {{ link.description }}
                    </div>
                </div>
            </div>
            <div v-show="showOptions" class="options">
                <div class="option-item">
                    Change title
                </div>
                <div class="option-item">
                    Change description
                </div>
                <div class="option-item">
                    Move
                </div>
                <div class="option-item">
                    Archive
                </div>
                <div @click="deleteLink"
                     class="option-item">
                    Delete
                </div>
            </div>
        </div>
        <div class="col-12 footer">
            <div class="row">
                <div class="col">
                    <img src="/images/icons/email.png" class="footer-icon">
                </div>
                <div class="col">
                    <img src="/images/icons/archive.png" class="footer-icon">
                </div>
                <div class="col">
                    <img src="/images/icons/calendar.png" class="footer-icon">
                </div>
                <div class="col">
                    <img src="/images/icons/facebook.png" class="footer-icon">
                </div>
                <div class="col">
                    <img src="/images/icons/twitter.png" class="footer-icon">
                </div>
                <div class="col">
                    <img src="/images/icons/view.png" class="footer-icon">
                </div>
                <div class="col">
                    <img src="/images/icons/options.png"
                         @click="toggleOptionsMenu"
                         class="footer-icon"
                         title="Options">
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import { EventBus } from '../eventbus/event-bus.js';

    export default {
        props: {
            link: Object
        },
        data() {
            return {
                showOptions: false,
                modal: {
                    method: 'delete-link',
                    title: 'Please confirm',
                    buttonText: "Delete",
                    body: `Are you sure you want to delete ${this.link.title}?`,
                    btnClass: 'delete',
                },
            }
        },
        created() {

        },
        computed: {

        },
        methods: {
            closeOptions() {
                this.showOptions = false;
            },
            deleteLink() {
                EventBus.$emit('toggle-confirmation-modal', this.modal.method, this.modal.title, this.modal.buttonText, this.modal.body, this.modal.btnClass, this.link.id);
            },
            toggleOptionsMenu() {
                this.showOptions = !this.showOptions;
            },
        }
    }
</script>

<style type="scss" scoped>
    .card-body {
        border-bottom: 1px solid silver;
        background: white;
        padding-bottom: 15px;
        position: relative;
        padding-top: 30px;
    }
    .footer {
        border: 1px solid silver;
        background: lightgray;
        margin-bottom: 10px;
        padding: 5px 10px;
        text-align: center;
    }
    .footer-icon {
        width: 1.5rem;
        cursor: pointer;
    }
    .img-block {
        text-align: center;
    }
    .link-content {
        min-height: 100px;
    }
    .link-image {
        max-height: 100px;
        margin-bottom: 10px;
    }
    .link-title {
        margin: 10px 0px;
    }
    .options {
        position: absolute;
        border: 1px solid gray;
        bottom: 0;
        right: 1px;
        width: 10rem;
        background: rgb(244,244,244);
        cursor: pointer;
        border-radius: 15px 15px 0px 15px;
    }
    .option-item {
        padding: 10px;
        border-bottom: 1px solid gray;
    }
    .option-item:last-child {
        border-bottom: none;
    }
    .option-item:hover {
        background: gray;
        color: white;
    }
    .option-item:hover:first-child {
        border-radius: 13px 13px 0px 0px;
    }
    .option-item:hover:last-child {
        background: darkred;
        border-radius: 0px 0px 0px 13px;
    }
</style>