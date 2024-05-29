import $http from "./Api";
import { DnsResponse } from "../Types/DnsResponse";
import { AxiosResponse } from "axios";

export default class {
    static async fetch(domain: string): Promise<AxiosResponse<DnsResponse>> {
        return $http.post<DnsResponse>('/api/v2/dns/read', {
            domain
        })
    }
}
