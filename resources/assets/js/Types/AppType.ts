import { Dns } from "./Dns";
import { ApiResponse } from "./ApiResponse";
import {Whois} from "./Whois";
import {SiteAvailability} from "./SiteAvailability";

export type AppType = {
    domain: string | null,
    whois: Whois | null,
    currentDns: Dns | null,
    siteAvailability: SiteAvailability | null,
    dnsHistoryItems: Array<Dns> | null,
    dataIsLoaded: Boolean,
    loadingDns: Boolean,
    errors: Array<String>
}
