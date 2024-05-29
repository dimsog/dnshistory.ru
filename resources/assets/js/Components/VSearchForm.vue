<template>
    <form @submit.prevent="onSubmitForm">
        <div class="input-group mb-3">
            <input type="text" v-model="form.domain" class="form-control px-3 py-2" placeholder="Название домена" aria-label="Название домена" aria-describedby="v-search-form-button" required>
            <button class="btn btn-outline-light px-3 py-2 btn-search" type="submit" id="v-search-form-button">
                <template v-if="loading">
                    <div class="spinner"></div>
                </template>
                <template v-else>
                    Найти
                </template>
            </button>
        </div>
    </form>
</template>

<style lang="scss" scoped>
.spinner {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    margin: 0 auto;
    background: radial-gradient(farthest-side,#aeaeae 95%,#0000) 50% 0.5px/5.8px 5.8px no-repeat,
    radial-gradient(farthest-side,#0000 calc(100% - 6.7px),rgba(174,174,174,0.1) 0);
    animation: spinner-aur408 1s infinite linear;
}

@keyframes spinner-aur408 {
    to {
        transform: rotate(1turn);
    }
}

.form-control {
    &:focus {
        box-shadow: none;
        border-color: rgb(222, 226, 230);
    }
}
.btn-search {
    text-align: center;
    width: 90px;
    border-color: rgb(222, 226, 230);
    color: #303030;
    background: rgb(248, 248, 255);
    &:hover {
        background: rgb(251, 251, 255);
    }
    &:active {
        background: rgb(240, 240, 246);
    }
}
.input-group:focus-within {
    .form-control, .btn-search {
        border-color: rgb(203, 206, 210);
    }
}
</style>

<script lang="ts">
import { defineComponent } from "vue";
import VErrors from "./VErrors.vue";

export default defineComponent({
    components: {
        VErrors
    },
    emits: ['submit', 'success'],
    props: {
        loading: {
            type: Boolean,
            default: false,
        }
    },
    data() {
        return {
            form: {
                domain: ''
            },
        }

    },
    methods: {
        onSubmitForm(): void {
            this.$emit('submit', this.form.domain);
        }
    }
});
</script>
