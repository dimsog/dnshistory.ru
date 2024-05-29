<template>
    <div class="d-flex dns-record">
        <div>
            <div class="dns-record__type" :class="'dns-record__type--' + dnsRecord.type.toLowerCase()">
                {{ dnsRecord.type }}
            </div>
        </div>
        <div class="d-flex align-items-center">
            <div v-if="dnsRecord.type === 'A'">
                <strong>{{ dnsRecord.value }}</strong><br>
                <small v-if="dnsRecord.organization_name != null">{{ dnsRecord.organization_name }}</small>
                <small v-if="dnsRecord.country != null">({{ dnsRecord.country }})</small>
            </div>
            <div v-else>
                {{ dnsRecord.value }}
            </div>
        </div>
    </div>
</template>

<style lang="scss" scoped>
.dns-record {
    padding-bottom: 10px;

    .dns-record__type {
        display: inline-block;
        padding: 2px 0;
        box-sizing: border-box;
        min-width: 50px;
        text-align: center;
        border-radius: 5px;
        font-weight: 600;
        color: #FFF;
        margin-right: 15px;
        background: #5068a5;

        &.dns-record__type--a {
            background: #29AB87;
        }

        &.dns-record__type--ns {
            background: #FFC30B;
        }
    }
}

</style>

<script lang="ts">
import {defineComponent} from "vue";

export default defineComponent({
    props: {
        dnsRecord: {
            type: Object,
            required: true
        }
    }
});
</script>
