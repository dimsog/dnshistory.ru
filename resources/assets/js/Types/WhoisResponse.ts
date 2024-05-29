import { Whois } from "./Whois";

export type WhoisResponse = {
    success: boolean,
    data: Whois,
    text?: String
}
