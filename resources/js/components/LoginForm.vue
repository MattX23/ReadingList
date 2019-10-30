<template>
    <div class="container login-container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <div class="form-group">
                            <input type="email" v-model="user.email" @blur="checkEmail" class="form-control login-input" placeholder="Enter your email address" required>
                            <small id="emailHelp" class="text-danger">
                                {{ emailHelpText }}
                            </small>
                        </div>
                        <div class="form-group" v-show="register">
                            <input type="text" v-model="user.username" class="form-control login-input" placeholder="Enter a username" :required="register">
                        </div>
                        <div class="form-group">
                            <input type="password" v-model="user.password" class="form-control login-input" :placeholder="passwordPlaceHolder" required autocomplete="new-password">
                        </div>
                        <div class="form-group" v-show="register">
                            <input type="password" v-model="user.passwordConfirm" class="form-control login-input" placeholder="Confirm your password" :required="register" autocomplete="new-password">
                        </div>
                    </div>
                    <div class="card-footer text-center">
                        <button class="btn btn-success" @click="submitForm">{{ buttonText }}</button>
                        <div class="text-center input-type-selector">
                            {{ accountStatusText }} have an account? <a href="javascript:void(0);" @click="switchType">{{ switchText }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    const SIGN_IN = 'Sign in';
    const REGISTER = 'Register';
    export default {
        data() {
            return {
                accountStatusText: "Don't",
                passwordPlaceHolder: 'Enter your password',
                register: false,
                user: {
                    email: '',
                    password: '',
                    passwordConfirm: '',
                    username: '',
                }
            }
        },
        mounted() {
            this.showCorrectForm();
        },
        computed: {
            buttonText() {
                if (this.register === false) {
                    return SIGN_IN;
                } else {
                    return REGISTER;
                }
            },
            switchText() {
                if (this.register === false) {
                    return REGISTER;
                } else {
                    return SIGN_IN;
                }
            }
        },
        methods: {
            checkEmail() {
                if (this.register) {
                    axios.post('/api/check-email', {
                            email: this.user.email
                    })
                    .then(function (response) {
                        console.log(response.data);
                    })
                    .catch(function (error) {
                        console.log(error);
                    });
                }
            },
            showCorrectForm() {
                const str = window.location.href;
                const n = str.lastIndexOf('/');
                const result = str.substring(n + 1);
                if (result === REGISTER.toLowerCase()) {
                    this.register = true;
                }
            },
            switchType() {
                if (!this.register) {
                    this.register = true;
                    this.accountStatusText = 'Already';
                    this.passwordPlaceHolder = 'Choose a password';
                } else {
                    this.register = false;
                    this.accountStatusText = "Don't";
                    this.passwordPlaceHolder = 'Enter your password';
                }
            },
            submitForm() {
                let route = '/api/register';

                let data = {
                    email: this.user.email,
                    password: this.user.password,
                };

                if (!this.register) {
                    route = 'api/login';
                    data.username = this.user.username;
                    data.password_confirmation = this.user.passwordConfirm;
                }

                axios.post(route, data)
                .then(function (response) {
                    document.querySelector('meta[name="api-token"]').setAttribute("content", response.data);
                    location.href = '/home';
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
        }
    }
</script>

<style type="text/css">
    .card-footer {
        background: white;
    }
    .input-type-selector {
        margin-top: 10px;
    }
    .login-container {
        margin-top: 10%;
    }
    .login-input {
        height: 50px;
        border: 0px;
        border-bottom: 1px solid slategray;
        border-radius: 0px;
    }
</style>
