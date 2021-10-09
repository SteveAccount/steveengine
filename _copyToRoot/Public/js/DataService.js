"use strict";

class DataService {
    constructor() {
    }

    request(type, url, data, success, error, complete) {
        $.ajax({
            type: type,
            url: url,
            data: data,
            async: false,
            success: success || {},
            error: error || {},
            complete: complete || {}
        });
    }
}
