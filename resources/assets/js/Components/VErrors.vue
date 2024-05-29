<template>
    <div>
        <div v-for="error in errors" class="alert alert-danger">
            {{ error }}
        </div>
    </div>
</template>

<script lang="ts">
import { defineComponent } from "vue";

export default defineComponent({
    props: {
        errors: {
            type: Array<String>,
            default: []
        }
    }
})
</script>
