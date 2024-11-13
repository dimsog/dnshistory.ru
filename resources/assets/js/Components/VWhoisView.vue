<template>
    <div>
        <template v-if="whois == null">
            <div class="alert alert-success">
                Не удалось загрузить whois. Возможно домен свободен.
            </div>
        </template>
        <template v-else>
            <div class="whois-container mb-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <div v-if="siteAvailability != null" class="site-availability">
                            <div class="site-availability-badge site-availability-badge--success" title="Сайт доступен" v-if="siteAvailability.status == 200"></div>
                            <div class="site-availability-badge site-availability-badge--error" title="Сайт недоступен" v-else></div>
                        </div>
                        <div class="whois-badge" @click.prevent="onShowMoreInfo">
                            <strong>whois</strong>
                        </div>
                        <div>
                            <span title="Дата регистрации домена">{{ createdDate }}</span> - <span title="До какой даты оплачен домен">{{ whois.paid_till_ru ?? 'неизвестно' }}</span>
                        </div>
                    </div>
                    <div class="whois-registrar">
                        <span title="Регистратор домена">{{ whois.registrar }}</span>
                    </div>
                </div>

                <div v-if="showMoreInfo">
                    <ul class="moreinfo-list">
                        <li><span>дата регистрации:</span> {{ createdDate }}</li>
                        <li><span>дата окончания:</span> {{ whois.paid_till_ru ?? 'неизвестно' }}</li>
                        <li><span>регистратор:</span> {{ whois.registrar }}</li>
                        <li v-for="ns in whois.name_servers"><span>nserver:</span> {{ ns }}</li>
                        <li v-if="whois.states.length > 0"><span>state:</span> {{ whois.states.join(' ') }}</li>
                    </ul>
                </div>
            </div>
        </template>
    </div>
</template>

<style lang="scss" scoped>
.whois-container {
    background: #ebebf4;
    padding: 7px;
    padding-right: 15px;
    border-radius: 5px;
    .site-availability {
        padding: 0 10px;
        .site-availability-badge {
            width: 11px;
            height: 11px;
            border-radius: 50%;
            &.site-availability-badge--success {
                background: #29AB87;
            }
            &.site-availability-badge--error {
                background: #FFC30B;
            }
        }
    }
}
.whois-badge {
    padding: 5px 10px;
    border-radius: 5px;
    margin-right: 10px;
    user-select: none;
    cursor: pointer;
    transition: .3s ease-in-out;
    &:hover {
        background: #dcdce8;
    }
    &:active {
        background: #d1d1dc;
    }
    strong {
        border-bottom: 1px dashed #858585;
    }
}

.moreinfo-list {
    list-style: none;
    padding: 10px;
    margin: 0;
    padding-top: 5px;
    span {
        color: #989898;
    }
}
</style>

<script lang="ts">
import { defineComponent } from "vue";

export default defineComponent({
    props: {
        whois: {
            type: Object
        },
        siteAvailability: {
            type: Object
        }
    },
    data() {
        return {
            showMoreInfo: false
        }
    },
    methods: {
        onShowMoreInfo() {
            this.showMoreInfo = !this.showMoreInfo;
        }
    },
    computed: {
        createdDate(): string {
            if (this.whois?.created_at === undefined || this.whois.created_at === null) {
                return '(неизвестно)';
            }
            return this.whois.created_at_ru
        }
    }
})
</script>
