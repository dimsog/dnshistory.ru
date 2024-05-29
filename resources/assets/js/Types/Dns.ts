import { DnsRecord } from "./DnsRecord";

export type Dns = {
    date: Date,
    records: Array<DnsRecord>
}
