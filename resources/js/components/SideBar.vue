<template>
    <div v-show="showMenu" @click="closeSideBar" class="side-bar-container">
        <div class="side-bar">
            <div class="menu-item"
                 @click.stop="newList">New List</div>
            <div class="menu-item">Archives</div>
            <div class="menu-item"
                 @click="logout">Logout</div>
        </div>
    </div>
</template>

<script>
    import { EventBus } from '../eventbus/event-bus.js';

    export default {
        data() {
            return {
                showMenu: false,
                modal: {
                    method: 'lists/create',
                    title: 'What is this list about?',
                    buttonText: 'Create new list',
                    placeholder: 'Give your list a name',
                }
            }
        },
        created() {
            EventBus.$on('toggle-sidebar', (state) => {
                !state ? (this.showMenu = true) : (this.showMenu = false);
            });
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
                EventBus.$emit('toggle-modal', this.modal.method, this.modal.title, this.modal.buttonText, this.modal.placeholder);
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
    .menu-item:hover:last-child {
        border-radius: 0px 0px 15px 15px;
    }
    .side-bar {
        width: 400px;
        position: fixed;
        right: 5px;
        top: 55px;
        z-index: 999;
        background: gainsboro;
        border: 2px solid gray;
        border-top: 0px;
        border-radius: 0px 0px 15px 15px;
    }
    .side-bar-container {
        position: fixed;
        z-index: 9998;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, .5);
        -webkit-transition: opacity .3s ease;
        transition: opacity .3s ease;
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
