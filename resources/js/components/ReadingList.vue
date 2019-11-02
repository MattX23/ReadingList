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
                    <div v-if="link.image" class="col-12 img-block">
                        <img :src="link.image" class="link-image">
                    </div>
                    <div class="col-12">
                        <div>
                            <h3 class="link-title"><a :href="link.url">{{ link.title }}</a></h3>
                        </div>
                        <div>
                            {{ link.description }}
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
        overflow-x: scroll;
        background: rgba(255,255,255,0.3);
    }
    .card-body {
        border-bottom: 1px solid silver;
        background: white;
        margin-bottom: 10px;
        padding-bottom: 0px;
    }
    .card-header {
        height: 50px;
        position: -webkit-sticky;
        position: sticky;
        top: 0;
        background: white;
        z-index: 999;
    }
    .footer {
        border: 1px solid silver;
        margin-top: 10px;
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
    .reading-list {
        margin-bottom: 15px;
    }
</style>
