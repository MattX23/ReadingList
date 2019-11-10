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
                        <select class="form-control"
                                v-model="selectedReadingList">
                            <option value="" :selected="selected">Select a list</option>
                            <option v-for="readingList in readingLists"
                                    :value="readingList.id"
                                    v-if="readingList.id !== readingListId">
                                {{ readingList.name }}
                            </option>
                        </select>
                        <small v-if="error" class="text-danger">
                            {{ error }}
                        </small>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-danger"
                                @click.stop="closeModal">Cancel</button>
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
                readingLists: {},
                showModal: false,
                route: '',
                title: '',
                buttonText: '',
                readingListId: '',
                linkId: '',
                selectedReadingList: '',
                error: '',
                selected: true,
                method: '',
            }
        },
        created() {
            EventBus.$on('toggle-selection-modal', (route, title, buttonText, linkId, method, readingListId) => {
                this.fetchData();
                this.selectedReadingList = '';
                this.selected = true;
                this.route = route;
                this.title = title;
                this.buttonText = buttonText;
                this.linkId = linkId;
                this.showModal = true;
                this.method = method;
                this.readingListId = readingListId;
            });
            EventBus.$on('close-modal', () => {
                this.closeModal();
            });
        },
        methods: {
            closeModal() {
                this.showModal = false;
            },
            errorCheck() {
                !this.selectedReadingList ? this.error = 'Please select a list' : this.error = '';
            },
            fetchData() {
                axios.get('/api/lists/get')
                    .then((response) => {
                        this.readingLists = response.data.readingLists;
                    });
            },
            doNothing() {
                return null;
            },
            submitModal() {
                this.errorCheck();

                if (!this.error) {
                    let data = {
                        newListId: this.selectedReadingList,
                    };

                    axios({
                        method: this.method,
                        url: `/api/${this.route}`,
                        data: data
                    })
                    .then((response) => {
                        this.closeModal();
                        EventBus.$emit('re-render');
                        EventBus.$emit('flash', response.data, 'success');
                        EventBus.$emit('reorder-lists');
                    });
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
