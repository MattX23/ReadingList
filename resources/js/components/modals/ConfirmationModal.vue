<template>
    <transition name="modal-fade">
        <div v-show="showModal" ref="modal" @click="closeModal" @keyup.enter="submitModal" class="custom-modal" tabindex="-1" role="dialog">
            <div @click.stop="doNothing" class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Please Confirm</h5>
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
    import { EventBus } from '../../eventbus/event-bus.js';

    export default {
        data() {
            return {
                showModal: false,
                route: '',
                method: '',
                buttonText: '',
                textInput: '',
                body: '',
                btnClass: 'btn btn-success',
            }
        },
        created() {
            EventBus.$on('toggle-confirmation-modal', (data) => {
                this.showModal = true;
                this.route = data.route;
                this.buttonText = data.buttonText;
                this.body = data.body;
                this.method = data.method;
                if (data.btnClass === 'delete') {
                    this.btnClass = 'btn btn-danger';
                } else if (data.btnClass === 'primary') {
                    this.btnClass = 'btn btn-primary';
                }
                this.$nextTick(() => this.$refs.modal.focus())
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
                axios({
                    method: this.method,
                    url: `/api/${this.route}`,
                    data: {},
                })
                .then((response) => {
                    EventBus.$emit('close-modal');
                    EventBus.$emit('re-render');
                    EventBus.$emit('flash', response.data, 'success');
                })
                .catch((error) => {
                    EventBus.$emit('close-modal');
                    EventBus.$emit('flash', error.response.data, 'danger');
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
