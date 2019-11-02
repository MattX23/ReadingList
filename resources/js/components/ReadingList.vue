<template>
    <div class="reading-list">
        <div class="card">
            <div class="card-header text-center">
                <h3>{{ name }}
                    <span class="add-link"
                          title="Add a URL"
                          @click="addURL">+</span>
                </h3>
            </div>
            <div v-for="link in links" class="card-body">
                <div class="row link-content">
                    <div class="col-2 img-block">
                        img
                    </div>
                    <div class="col-10">
                        <div>
                            LINK HEADER
                        </div>
                        <div>
                            Link content
                        </div>
                        <div>
                            {{ link.url }}
                        </div>
                    </div>
                    <div class="col-12 footer">
                        footer
                    </div>
                </div>
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
        },
        data() {
            return {
                modal: {
                    method: 'add-link',
                    title: `Add to ${this.name}`,
                    buttonText: "Add",
                    placeholder: "Paste the URL here",
                },
            }
        },
        computed: {

        },
        methods: {
            addURL() {
                EventBus.$emit('toggle-modal', this.modal.method, this.modal.title, this.modal.buttonText, this.modal.placeholder);
                EventBus.$on(this.modal.method, (link) => {
                    const data = {
                        link: link,
                        id: this.id,
                    }
                    axios.post('/api/lists/add-link', data)
                    .then((response) => {
                        EventBus.$emit('close-modal');
                        EventBus.$emit('re-render');
                        console.log(response.status)
                        // TODO flash success message
                        console.log(response)
                    })
                    .catch((error) => {
                        EventBus.$emit('input-error', error.response.data);
                    })
                });
            },
        }
    }
</script>

<style type="scss" scoped>
    .add-link {
        float: right;
        margin-left: -12px;
        cursor: pointer;
    }
    .card {
        border: none;
    }
    .card-body {
        border-bottom: 1px solid silver;
    }
    .card-header {
        height: 50px;
    }
    .footer {
        border: 1px solid silver;
    }
    .img-block {
        border: 1px solid silver;
    }
    .link-content {
        min-height: 100px;
    }
    .reading-list {
        margin-bottom: 15px;
    }
</style>
