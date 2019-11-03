<template>
    <div class="reading-list">
        <div class="card">
            <div class="card-header text-center">
                <h3>{{ name }}
                    <span class="add-link"
                          title="Add a link"
                          @click.stop="addURL">+</span>
                </h3>
            </div>
            <div v-for="link in links">
                <reading-link :link="link"></reading-link>
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
                showOptions: false,
            }
        },
        created() {

        },
        computed: {

        },
        methods: {
            addURL() {
                EventBus.$emit('toggle-modal', this.modal.method, this.modal.title, this.modal.buttonText, this.modal.placeholder, this.id);
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
    .card-header {
        height: 50px;
        position: -webkit-sticky;
        position: sticky;
        top: 0;
        background: white;
        z-index: 999;
    }
    .reading-list {
        margin-bottom: 15px;
    }
</style>
