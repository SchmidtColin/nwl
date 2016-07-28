/**
 * Created by 67520 on 27.07.2016.
 */

var sortTargets = function(targets) {
    var pending = [], processed = [];
    for (var i = 0; i < targets.length; i++) {
        var target = targets[i];
        if (target.state) processed.push(target);
        else pending.push(target);
    }

    for (var p = 0; p < pending.length; p++) {
        var target = pending[p];
        var recentDate;
        for (var r = 0; r < target.whitelist_requests.length; r++) {
            var request = target.whitelist_requests[r];
            if (!recentDate || request.created > recentDate) recentDate = request.created;
        }
        target.mostRecentRequestDate = recentDate;
    }

    pending.sort(function(a, b) {
        if (a.mostRecentRequestDate > b.mostRecentRequestDate) return -1;
        if (a.mostRecentRequestDate < b.mostRecentRequestDate) return 1;
        return 0;
    });

    processed.sort(function(a, b) {
        if (a.decision_date && b.decision_date) {
            if (a.decision_date > b.decision_date) return -1;
            if (a.decision_date < b.decision_date) return 1;
        }
        return 0;
    });

    return {pending: pending, processed: processed};
}
