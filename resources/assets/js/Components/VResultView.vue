<template>
    <div class="current-dns" v-if="currentDns != null && currentDns.records.length > 0">
        <h5>текущие данные</h5>
        <v-dns-record v-for="dnsRecord in currentDns.records" :dns-record="dnsRecord"></v-dns-record>
    </div>
    <div v-if="dns.length > 0">
        <div v-for="dnsItem in dns" class="dns">
            <h5>{{ dnsItem.ru_date }}</h5>
            <v-dns-record v-for="dnsRecord in dnsItem.records" :dns-record="dnsRecord"></v-dns-record>
        </div>
    </div>
</template>

<style lang="scss" scoped>
.dns, .current-dns {
    padding-bottom: 25px;
}
</style>

<script lang="ts">
import { defineComponent } from "vue";
import { Dns } from "../Types/Dns";
import VDnsRecord from "./VDnsRecord.vue";

export default defineComponent({
    props: {
        dns: {
            type: Array<Dns>,
            default: []
        },
        currentDns: {
            type: Object | null,
            default: null
        }
    },
    components: {
        VDnsRecord
    }
});
</script>
