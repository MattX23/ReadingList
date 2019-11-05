<template>
    <div :class="'alert '+ alertClass"
          v-if="showFlash">
        <span v-text="content"></span>
    </div>
</template>

<script>
    import { EventBus } from '../eventbus/event-bus.js';

    export default {
        data() {
            return {
                content: "",
                showFlash: "",
                alert: '',
            }
        },
        computed: {
            alertClass() {
                return 'alert-' + this.alert;
            },
        },
        created() {
            EventBus.$on('flash', (content, alert) => {
                setTimeout(() => {
                    this.showFlash = true;
                    this.content = content;
                    this.alert = alert;
                    setTimeout(() => {
                        this.showFlash = false;
                        this.content = '';
                        this.alert = '';
                    }, 3000);
                }, 500);

            });
        }
    }
</script>

<style type="scss" scoped>
    .alert{
        position: fixed;
        right: 15px;
        bottom: 30px;
        min-width: 150px;
        text-align: center;
    }
</style>
