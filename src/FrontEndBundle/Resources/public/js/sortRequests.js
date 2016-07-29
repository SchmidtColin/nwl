/**
 * Created by 67520 on 27.07.2016.
 */

var sortRequests = function(requests) {
    console.log(requests);
    var pending = [], processed = [];
    for (var i = 0; i < requests.length; i++) {
        var target = requests[i].whitelist_target;
        if (target.state) processed.push(target);
        else pending.push(target);
    }

    pending.sort(function(a, b) {
        if (a.created > b.created) return -1;
        if (a.created < b.created) return 1;
        return 0;
    });

    processed.sort(function(a, b) {
        if (a.created > b.created) return -1;
        if (a.created < b.created) return 1;
        return 0;
    });

    return {pending: pending, processed: processed};
}
