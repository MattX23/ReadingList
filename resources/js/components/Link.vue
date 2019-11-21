<template>
    <div>
        <div @click="closeOptions" class="card-body">
            <div class="row link-content">
                <div class="col-12">
                    <div class="img-block">
                        <img :src="link.image" v-if="link.image" class="link-image">
                        <h3 class="link-title"><a :href="link.url" target="_blank">{{ link.title }}</a></h3>
                        <div class="link-body" :title="link.description">{{ link.description }}</div>
                    </div>
                </div>
            </div>
            <div v-show="showOptions" :class="optionsClass">
                <div class="option-item" @click="editTitle">
                    Change title
                </div>
                <div v-if="!wideScreen" class="option-item">
                    Share
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
                <div class="col" v-if="wideScreen">
                    <img src="/images/icons/facebook.png" class="footer-icon">
                </div>
                <div class="col" v-if="wideScreen">
                    <img src="/images/icons/twitter.png" class="footer-icon">
                </div>
                <div class="col">
                    <img src="/images/icons/view.png" class="footer-icon">
                </div>
                <div class="col">
                    <img src="/images/icons/options.png"
                         @click.stop="toggleOptionsMenu"
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
            link: Object,
            windowWidth: Number,
            id: Number,
            numLinks: Number,
        },
        data() {
            return {
                showOptions: false,
                modal: {
                    route: '',
                    buttonText: '',
                    body: '',
                    btnClass: '',
                },
            }
        },
        created() {
            EventBus.$on('close-options', () => {
                this.closeOptions();
            });
        },
        computed: {
            wideScreen() {
                if (this.windowWidth > 1445) {
                    return true;
                }
            },
            optionsClass() {
                let classList = 'options';
                if (this.link.position === this.numLinks && this.numLinks > 1) {
                    classList += ' options-last';
                }
                return classList;
            },
        },
        methods: {
            closeOptions() {
                this.showOptions = false;
            },
            deleteLink() {
                let data = {
                    route: `link/delete/${this.link.id}`,
                    body: `Are you sure you want to delete ${this.link.title}?`,
                    buttonText: 'Delete',
                    btnClass: 'delete',
                    method: 'POST',
                };
                EventBus.$emit('toggle-confirmation-modal', data);
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
            },
            toggleOptionsMenu() {
                if (this.showOptions) {
                    EventBus.$emit('close-options');
                } else {
                    EventBus.$emit('close-options');
                    this.showOptions = !this.showOptions;
                }
            },
        }
    }
</script>

<style type="scss" scoped>
    .card-body {
        border-bottom: 1px solid silver;
        background: white;
        position: relative;
        padding: 10px;
    }
    .footer {
        border: 1px solid silver;
        background: lightgray;
        margin-bottom: 10px;
        padding: 5px 10px;
        text-align: center;
    }
    .footer-icon {
        max-width: 1.5rem;
        cursor: pointer;
    }
    .img-block {
        text-align: center;
    }
    .link-body {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .link-content {
        min-height: 75px;
    }
    .link-image {
        max-height: 70px;
        margin-bottom: 5px;
        max-width: 100px;
        float: left;
        padding-right: 5px;
        border-radius: 20px;
        padding-left: 3px;
        margin-right: 5px;
    }
    .link-title {
        font-size: 1rem;
        text-align: left;
        display: table;
        word-break: break-word;
    }
    .options {
        position: absolute;
        border: 1px solid gray;
        bottom: -200px;
        right: 45px;
        background: rgba(244,244,244, 0.9);
        cursor: pointer;
        border-radius: 15px 0px 15px 15px;
        text-align: center;
        margin-top: 10px;
        z-index: 9999;
        min-width:200px;
    }
    .options-last {
        bottom: -19px;
        right: 40px;
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
        background: darkred;
        border-radius: 0px 0px 0px 13px;
    }
</style>
