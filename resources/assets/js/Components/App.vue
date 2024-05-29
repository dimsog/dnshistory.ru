<template>
    <div class="container">
        <div class="row justify-content-center pt-4">
            <div class="col-lg-6">
                <v-search-form @submit="onSubmitForm" :loading="loadingDns" ></v-search-form>
                <v-errors v-if="errors.length > 0" :errors="errors"></v-errors>

                <template v-if="dataIsLoaded">
                    <v-whois-view :whois="whois" :site-availability="siteAvailability"></v-whois-view>
                    <v-result-view v-if="currentDns != null" class="pt-2" :current-dns="currentDns"></v-result-view>
                    <v-result-view class="pt-2" :dns="dnsHistoryItems"></v-result-view>
                </template>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { defineComponent } from "vue";
import VSearchForm from "./VSearchForm.vue";
import VResultView from "./VResultView.vue";
import VErrors from "./VErrors.vue";
import VWhoisView from "./VWhoisView.vue";
import { AppType } from "../Types/AppType";
import DnsRecordsFetcher from "../Services/DnsRecordsFetcher";

export default defineComponent({
    components: {
        VSearchForm,
        VResultView,
        VWhoisView,
        VErrors,
    },
    data(): AppType {
        return {
            domain: null,
            whois: null,
            currentDns: null,
            siteAvailability: null,
            dnsHistoryItems: null,
            dataIsLoaded: false,
            loadingDns: false,
            errors: []
        }
    },
    methods: {
        onSubmitForm(domain: string): void {
            this.domain = domain;
            this.errors = [];
            this.loadDns();
        },

        async loadDns() {
            try {
                if (this.domain == null || this.loadingDns) {
                    return;
                }

                this.dataIsLoaded = false;
                this.loadingDns = true;
                this.whois = null;
                this.currentDns = null;
                this.dnsHistoryItems = null;

                const response = await DnsRecordsFetcher.fetch(this.domain);
                if (response.data.success) {
                    if (response.data.data.whoisErrorText !== null) {
                        this.errors.push(response.data.data.whoisErrorText);
                    }
                    this.whois = response.data.data.whois;
                    this.siteAvailability = response.data.data.siteAvailability;
                    this.currentDns = response.data.data.currentDns;
                    this.dnsHistoryItems = response.data.data.dnsHistoryItems;

                    this.dataIsLoaded = true;
                } else {
                    if (response.data.text !== undefined) {
                        this.errors.push(response.data.text);
                    }
                }
            } catch (e) {
                if (e.response.status === 422) {
                    this.errors.push(e.response.data.text.toLowerCase());
                } else {
                    this.errors.push('При отправке данных произошла ошибка. Попробуйте позже');
                }
            } finally {
                this.loadingDns = false;
            }
        },
    }
});
</script>
