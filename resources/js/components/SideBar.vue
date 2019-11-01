<template>
    <div v-show="showMenu" class="side-bar">
        <div class="menu-item"
             @click="newList">New List</div>
        <div class="menu-item">Archives</div>
        <div class="menu-item"
             @click="logout">Logout</div>
    </div>
</template>

<script>
    import { EventBus } from '../eventbus/event-bus.js';

    export default {
        data() {
            return {
                showMenu: false,
                modal: {
                    title: 'What is this list about?',
                    buttonText: 'Create new list',
                }
            }
        },
        mounted() {

        },
        created() {
            EventBus.$on('toggle-sidebar', (state) => {
                !state ? (this.showMenu = true) : (this.showMenu = false);
            });
        },
        computed: {

        },
        methods: {
            closeSideBar() {
                EventBus.$emit('toggle-sidebar', true);
            },
            logout() {
                const data = document.head.querySelector('meta[name="csrf-token"]').content;
                axios.post('/api/logout', data)
                .then(() => {
                    location.href = '/login';
                })
            },
            newList() {
                this.closeSideBar();
                EventBus.$emit('toggle-modal', this.modal.title, this.modal.buttonText);
                EventBus.$on('submit-input', (listName) => {
                    const data = {
                        name: listName,
                    }
                    axios.post('/api/create-list', data)
                    .then((response) => {
                        console.log(response)
                    })
                });
            },
        }
    }
</script>

<style type="scss" scoped>
    .menu-item {
        font-size: 1.15rem;
        text-align: center;
        border-top: 1px solid silver;
        padding-top: 0.25rem;
        line-height: 3.5rem;
        cursor: pointer;
    }
    .menu-item:hover {
        background: silver;
        color: white;
    }
    .side-bar {
        width: 400px;
        background: white;
        position: fixed;
        right: 0;
        top: 55px;
    }
    .toggle-container {
        height: 20px;
        margin-bottom: 15px;
    }
    .toggle-sidebar {
        float: right;
        margin-right: 25px;
    }
</style>
