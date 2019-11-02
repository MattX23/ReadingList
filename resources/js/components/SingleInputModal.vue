<template>
    <transition name="modal-fade">
        <div v-show="showModal" class="custom-modal" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ title }}</h5>
                        <button @click="closeModal" type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input class="form-control"
                               type="text"
                               v-model="textInput"
                               :placeholder="placeholder"
                               autofocus>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-success"
                                v-text="buttonText"
                                @click="submitModal"></button>
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
                title: '',
                buttonText: '',
                textInput: '',
                placeholder: '',
            }
        },
        created() {
            EventBus.$on('toggle-modal', (title, buttonText, placeholder) => {
                this.showModal = true;
                this.title = title;
                this.buttonText = buttonText;
                this.placeholder = placeholder;
            });
        },
        methods: {
            closeModal() {
                this.showModal = false;
            },
            submitModal() {
                if (this.textInput) {
                    this.closeModal();
                    EventBus.$emit('submit-input', this.textInput);
                    this.textInput = '';
                }
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
