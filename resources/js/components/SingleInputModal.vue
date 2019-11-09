<template>
    <transition name="modal-fade">
        <div v-show="showModal" @click="closeModal" class="custom-modal" tabindex="-1" role="dialog">
            <div @click.stop="doNothing" class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ title }}</h5>
                        <button @click.stop="closeModal" type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input class="form-control"
                               type="text"
                               v-model="textInput"
                               :placeholder="placeholder"
                               @keydown="clearErrors"
                               autofocus>
                        <small v-if="error" class="text-danger">
                            {{ error }}
                        </small>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-success"
                                v-text="buttonText"
                                @click.stop="submitModal">
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </transition>
</template>

<script>
    import { EventBus } from '../eventbus/event-bus.js';

    export default {
        data() {
            return {
                showModal: false,
                route: '',
                title: '',
                buttonText: '',
                textInput: '',
                placeholder: '',
                error: '',
                readingListId: '',
                method: '',
            }
        },
        created() {
            EventBus.$on('toggle-modal', (route, title, buttonText, placeholder, method) => {
                this.clearErrors();
                this.showModal = true;
                this.route = route;
                this.title = title;
                this.buttonText = buttonText;
                this.placeholder = placeholder;
                this.method = method;
            });
            EventBus.$on('close-modal', () => {
                this.closeModal();
            });
        },
        methods: {
            clearErrors() {
                this.error = '';
            },
            closeModal() {
                this.showModal = false;
                this.textInput = '';
            },
            doNothing() {
                return null;
            },
            submitModal() {
                let data = {
                    name: this.textInput,
                    id: this.readingListId,
                };

                axios({
                    method: this.method,
                    url: `/api/${this.route}`,
                    data: data,
                })
                .then((response) => {
                    this.closeModal();
                    EventBus.$emit('re-render');
                    EventBus.$emit('flash', response.data, 'success');
                })
                .catch((error) => {
                    this.error = error.response.data;
                })
            },
        }
    }
</script>

<style type="scss" scoped>
    .custom-modal {
        position: fixed;
        z-index: 9998;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, .5);
        transition: opacity .3s ease;
    }
</style>
