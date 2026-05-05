function getFirstDate() {
    var date = new Date(), y = date.getFullYear(), m = date.getMonth();
    var firstDay = new Date(y, m, 1);

    return firstDay;
}

function getLastDate() {
    var date = new Date(), y = date.getFullYear(), m = date.getMonth();
    var firstDay = new Date(y, m, 1);
    var lastDay = new Date(y, m + 1, 0);

    return lastDay;
}

function getFormatDate(src) {
    var newDate = new Date(src);

    var day = newDate.getDate();
    var month = newDate.getMonth() + 1;
    var year = newDate.getFullYear();

    return day + '/' + month + '/' + year;
}

function getFormatDateTime(src) {
    var newDate = new Date(src);

    var year = newDate.getFullYear(),
    month = newDate.getMonth() + 1,
    day = newDate.getDate(),
    hour = newDate.getHours(),
    minute = newDate.getMinutes(),
    second = newDate.getSeconds(),
    hourFormatted = hour % 12, // hour returned in 24 hour format
    minuteFormatted = minute < 10 ? "0" + minute : minute,
    morning = hour < 12 ? "am" : "pm";

    return day + '/' + month + '/' + year + ' ' + hour + ":" + minuteFormatted;
}

$(document).ready(function() {
    if (location.hash) {
        $("a[href='" + location.hash + "']").tab("show");
    }
    $(document.body).on("click", "a[data-toggle]", function(event) {
        location.hash = this.getAttribute("href");
    });
});

$(window).on("popstate", function() {
    var anchor = location.hash || $("a[data-toggle='tab']").first().attr("href");
    $("a[href='" + anchor + "']").tab("show");
});