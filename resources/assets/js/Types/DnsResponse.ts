import { Dns } from "./Dns";
import { Whois } from "./Whois";
import {SiteAvailability} from "./SiteAvailability";

export type DnsResponse = {
    success: boolean,
    text?: string,
    data: {
        whois: Whois,
        whoisErrorText: string|null,

        siteAvailability: SiteAvailability|null,

        currentDns: Dns|null,
        currentDnsErrorText: string|null,

        dnsHistoryItems: Array<Dns>,
    }
};
