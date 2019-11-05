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
                        {{ body }}
                    </div>
                    <div class="modal-footer">
                        <button :class="btnClass"
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
                method: '',
                title: '',
                buttonText: '',
                textInput: '',
                body: '',
                btnClass: 'btn btn-success',
                id: '',
            }
        },
        created() {
            EventBus.$on('toggle-confirmation-modal', (method, title, buttonText, body, btnClass, id) => {
                this.showModal = true;
                this.method = method;
                this.title = title;
                this.buttonText = buttonText;
                this.body = body;
                this.id = id;
                if (btnClass === 'delete') {
                    this.btnClass = 'btn btn-danger';
                }
            });
            EventBus.$on('close-modal', () => {
                this.closeModal();
            });
        },
        methods: {
            closeModal() {
                this.showModal = false;
            },
            doNothing() {
                return null;
            },
            submitModal() {
                axios.post(`/api/lists/${this.method}/${this.id}`, {})
                    .then((response) => {
                        EventBus.$emit('close-modal');
                        EventBus.$emit('re-render');
                        EventBus.$emit('flash', response.data, 'success');
                    })
                    .catch((error) => {
                        EventBus.$emit('flash', error.response.data, 'danger');
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
