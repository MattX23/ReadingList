<template>
    <div v-show="showMenu" @click="closeSideBar" class="side-bar-container">
        <div class="side-bar">
            <div class="menu-item"
                 @click.stop="newList">New List</div>
            <div class="menu-item"
                 @click.stop="openArchives">Archives</div>
            <div class="menu-item"
                 @click="logout">Logout</div>
        </div>
    </div>
</template>

<script>
    import { EventBus } from '../../eventbus/event-bus.js';

    export default {
        data() {
            return {
                showMenu: false,
            }
        },
        created() {
            EventBus.$on('toggle-sidebar', (state) => {
                EventBus.$emit('close-options');
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
                let data = {
                    route: 'lists/create',
                    title: 'What is this list about?',
                    buttonText: 'Create new list',
                    placeholder: 'Give your list a name',
                    method: 'POST',
                };
                EventBus.$emit('toggle-modal', data);
            },
            openArchives() {
                this.closeSideBar();
                EventBus.$emit('re-render');
                EventBus.$emit('toggle-archive-modal');
            }
        }
    }
</script>

<style lang="scss" scoped>
    @import '../../../sass/variables';

    .menu-item {
        font-size: 1.15rem;
        text-align: center;
        border-top: 1px solid $silver;
        padding-top: 0.25rem;
        line-height: 3.5rem;
        cursor: pointer;
    }
    .menu-item:hover {
        background: $blue;
        color: $white;
    }
    .side-bar {
        width: 400px;
        position: fixed;
        right: -2px;
        top: 55px;
        z-index: 999;
        background: $off-white-high-transparency;
        border: 2px solid $gray;
        border-top: 0px;
    }
    .side-bar-container {
        position: fixed;
        z-index: 9998;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: $black-medium-transparency;
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
