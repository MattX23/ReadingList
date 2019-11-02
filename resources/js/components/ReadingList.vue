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
            <div class="card-body">
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
        },
        data() {
            return {
                modal: {
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
                EventBus.$emit('toggle-modal', this.modal.title, this.modal.buttonText, this.modal.placeholder);
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
