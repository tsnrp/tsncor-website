/**
 * This module contains functions to retrieve data from the TSNCOR spreadsheet (which acts kind of like an API).
 * It also caches the results in local browser storage.
 */
import * as Config from "../configuration.js"

var cacheDisabled = false;

export async function fetchOfficerData() {
    let url = `${Config.TSNCOR_OFFICERS_URL}&max_age=${cacheDisabled ? "0" : Config.SHORT_CACHE_DURATION_SECONDS.toString()}`
    let officers = await fetchJson(url);
    return officers;
}


export async function fetchShipData() {
    let url = `${Config.TSNCOR_SHIPS_URL}&max_age=${cacheDisabled ? "0" : Config.LONG_CACHE_DURATION_SECONDS.toString()}`
    let ships = await fetchJson(url);
    return ships;
}

export async function fetchAwardData() {
    let url = `${Config.TSNCOR_AWARDS_URL}&max_age=${cacheDisabled ? "0" : Config.LONG_CACHE_DURATION_SECONDS.toString()}`
    let awards = await fetchJson(url);
    return awards;
}

export async function fetchAssignmentRecordData() {
    let url = `${Config.TSNCOR_ASSIGNMENTS_RECORDS_URL}&max_age=${cacheDisabled ? "0" : Config.SHORT_CACHE_DURATION_SECONDS.toString()}`
    let assignmentRecords = await fetchJson(url);
    return assignmentRecords;
}

export async function fetchAwardRecordData() {
    let url = `${Config.TSNCOR_AWARDS_RECORDS_URL}&max_age=${cacheDisabled ? "0" : Config.SHORT_CACHE_DURATION_SECONDS.toString()}`
    let awardsRecords = await fetchJson(url);
    return awardsRecords;
}

export async function fetchRankData() {
    let url = `${Config.TSNCOR_RANKS_URL}&max_age=${cacheDisabled ? "0" : Config.LONG_CACHE_DURATION_SECONDS.toString()}`
    let ranks = await fetchJson(url);
    return ranks;
}

export async function fetchStardateData() {
    let url = `${Config.TSNCOR_STARDATES_URL}&max_age=${cacheDisabled ? "0" : Config.LONG_CACHE_DURATION_SECONDS.toString()}`
    let stardates = await fetchJson(url);
    return stardates;
}

export async function disableCache() {
    localStorage.clear();
    cacheDisabled = true;
}

export async function enableCache() {
    cacheDisabled = false;
}

async function fetchJson(url) {
    let res = await fetch(url);
    return await res.json()
}
