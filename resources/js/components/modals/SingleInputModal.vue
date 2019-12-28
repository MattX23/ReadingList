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
                        <input id="selection-modal-input"
                               class="form-control"
                               type="text"
                               v-model="textInput"
                               :placeholder="placeholder"
                               @keydown="clearErrors"
                               @keyup.enter="submitModal">
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
    import { EventBus } from '../../eventbus/event-bus.js';

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
                method: ''
            }
        },
        created() {
            EventBus.$on('toggle-modal', (data) => {
                this.clearErrors();
                this.showModal = true;
                this.route = data.route;
                this.title = data.title;
                this.buttonText = data.buttonText;
                this.placeholder = data.placeholder;
                this.method = data.method;
                this.readingListId = data.readingListId;
                this.textInput = data.textInput;
                setTimeout(function() {
                    document.getElementById("selection-modal-input").focus();
                }, 0);
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
                return false;
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
                    this.error = error.response.status === 403 ? error.response.data.message : error.response.data;
                })
            },
        }
    }
</script>

<style lang="scss" scoped>
    @import '../../../sass/variables';

    .custom-modal {
        position: fixed;
        z-index: 9998;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: $black-medium-transparency;
        transition: opacity .3s ease;
    }
</style>
