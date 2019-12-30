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
            <options
                :showOptions="showOptions"
                :optionsClass="optionsClass"
                :link="link"
                :deleteFunction="deleteLink">
            </options>
        </div>
        <div class="col-12 footer">
            <div class="row text-right" v-if="archived">
                <div class="col">
                    <button @click="reAddLink"
                            class="btn btn-sm btn-success">Add back to list</button>
                    <button @click="deleteLink"
                            class="btn btn-sm btn-danger">Delete</button>
                </div>
            </div>
            <div class="row text-right" v-if="!archived">
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
            linkCount: Number,
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
                archived: !!this.link.deleted_at,
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
                if (this.link.position === this.linkCount && this.linkCount > 1) {
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
                    route: this.archived ? `link/force-delete/${this.link.id}`: `link/delete/${this.link.id}`,
                    body: `Are you sure you want to delete ${this.link.title}?`,
                    buttonText: 'Delete',
                    btnClass: 'delete',
                    method: 'POST',
                };
                EventBus.$emit('toggle-confirmation-modal', data);
            },
            reAddLink() {
                axios.put(`/api/link/restore/${this.link.id}`)
                    .then((response) => {
                        if (!this.archived) EventBus.$emit('close-modal');
                        EventBus.$emit('re-render');
                        EventBus.$emit('archive-restored', this.link.id);
                        EventBus.$emit('flash', response.data, 'success');
                    })
            },
            toggleOptionsMenu() {
                if (this.showOptions) {
                    EventBus.$emit('close-options');
                } else {
                    EventBus.$emit('close-options');
                    this.showOptions = !this.showOptions;
                }
            }
        }
    }
</script>

<style lang="scss" scoped>
    @import '../../sass/variables';

    .card-body {
        border-bottom: 1px solid $silver;
        background: $white;
        position: relative;
        padding: 10px;
    }
    .footer {
        border: 1px solid $silver;
        background: $lightgray;
        margin-bottom: 10px;
        padding: 5px 10px;
        text-align: center;
    }
    .footer-icon {
        max-width: 1.5rem;
        margin-right: 15px;
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
        border-radius: 10px;
        padding-left: 3px;
        margin-right: 5px;
    }
    .link-title {
        font-size: 1rem;
        text-align: left;
        display: table;
        word-break: break-word;
    }
</style>
