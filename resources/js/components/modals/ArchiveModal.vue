<template>
    <transition name="modal-fade">
        <div v-show="showModal" @click="closeModal" class="custom-modal" tabindex="-1" role="dialog">
            <div @click.stop="doNothing" class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Archives</h5>
                        <button @click.stop="closeModal" type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div v-for="link in links"
                             :key="link.id">
                            <reading-link
                                :link="link"
                                :id="link.id"
                            >
                            </reading-link>
                        </div>
                        <div v-if="!links.length">
                            <p>There is nothing saved in your archives.</p>
                        </div>
                    </div>
                    <div class="modal-footer">

                    </div>
                </div>
            </div>
        </div>
    </transition>
</template>

<script>
    import { EventBus } from '../../eventbus/event-bus.js';

    export default {
        props: {
            userId: Number,
        },
        data() {
            return {
                showModal: false,
                links: {},
            }
        },
        created() {
            EventBus.$on('toggle-archive-modal', () => {
                axios.get('/api/link/archives')
                .then((response) => {
                    this.links = response.data;
                    this.showModal = true;
                })
            });
            EventBus.$on('archive-restored', (id) => {
                const index = this.links.map(x => {
                    return x.id;
                }).indexOf(id);

                this.links.splice(index, 1);
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
    .modal-body {
        max-height: 500px;
        overflow-y: scroll;
    }
</style>
